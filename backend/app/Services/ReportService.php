<?php

namespace App\Services;

use App\Models\Report;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportService
{
    /**
     * Tạo báo cáo mới
     */
    public function createReport(array $data): Report
    {
        // Normalize reportable type to morph key (listing/user/review)
        $rawType = (string) ($data['reportable_type'] ?? '');
        // unify slashes and strip possible namespace
        $normalized = str_replace('\\\\', '\\', $rawType);
        $simple = strtolower(preg_replace('/^app\\\\models\\\\/i', '', $normalized));
        $map = [
            'listing' => 'listing',
            'user' => 'user',
            'review' => 'review',
            'app\\models\\listing' => 'listing',
            'app\\models\\user' => 'user',
            'app\\models\\review' => 'review',
        ];
        $morphType = $map[$simple] ?? 'listing';

        $report = Report::create([
            'reporter_id' => Auth::id(),
            'reportable_type' => $morphType,
            'reportable_id' => $data['reportable_id'],
            'reason' => $data['reason'],
            'description' => $data['description'],
            'status' => 'pending',
        ]);

        $this->logReportCreation($report, $data);

        return $report;
    }

    /**
     * Ghi log tạo báo cáo
     */
    private function logReportCreation(Report $report, array $data): void
    {
        $report->auditLogs()->create([
            'user_id' => Auth::id(),
            'action' => 'report_created',
            'old_values' => null,
            'new_values' => [
                'reportable_type' => $data['reportable_type'],
                'reportable_id' => $data['reportable_id'],
                'reason' => $data['reason']
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Lấy thống kê báo cáo của user
     */
    public function getUserReportStats(int $userId): array
    {
        return [
            'total_reports' => Report::where('reporter_id', $userId)->count(),
            'pending_reports' => Report::where('reporter_id', $userId)->where('status', 'pending')->count(),
            'reviewed_reports' => Report::where('reporter_id', $userId)->where('status', 'reviewed')->count(),
            'resolved_reports' => Report::where('reporter_id', $userId)->where('status', 'resolved')->count(),
            'dismissed_reports' => Report::where('reporter_id', $userId)->where('status', 'dismissed')->count(),
        ];
    }

    /**
     * Lấy danh sách lý do báo cáo
     */
    public function getReportReasons(): array
    {
        return [
            'fraud' => 'Lừa đảo',
            'fake_product' => 'Hàng giả',
            'spam' => 'Spam',
            'inappropriate_content' => 'Nội dung không phù hợp',
            'price_manipulation' => 'Thao túng giá',
            'fake_reviews' => 'Đánh giá giả',
            'harassment' => 'Quấy rối',
            'copyright_violation' => 'Vi phạm bản quyền',
            'other' => 'Khác'
        ];
    }

    /**
     * Lấy danh sách loại đối tượng có thể báo cáo
     */
    public function getReportableTypes(): array
    {
        return [
            'App\\Models\\Listing' => 'Tin rao',
            'App\\Models\\User' => 'Người dùng',
            'App\\Models\\Review' => 'Đánh giá',
        ];
    }

    // ========== Admin features (kept in this service per design request) ==========

    public function listReportsForAdmin(array $filters): LengthAwarePaginator
    {
        $query = Report::query();

        if (isset($filters['status']) && trim((string) $filters['status']) !== '') {
            $query->where('status', trim((string) $filters['status']));
        }

        if (array_key_exists('type', $filters) && trim((string) $filters['type']) !== '' && trim((string) $filters['type']) !== 'all') {
            $type = trim((string) $filters['type']);
            $morph = $this->normalizeType($type);
            // Include both morph key and legacy class-string to avoid missing older rows
            $classMap = [
                'listing' => 'App\\Models\\Listing',
                'user' => 'App\\Models\\User',
                'review' => 'App\\Models\\Review',
            ];
            $legacy = $classMap[$morph] ?? $type;
            $query->whereIn('reportable_type', [$morph, $legacy]);
        }

        if (isset($filters['search']) && trim((string) $filters['search']) !== '') {
            $s = trim((string) $filters['search']);
            $query->where(function ($q) use ($s) {
                $q->where('reason', 'like', "%$s%")
                    ->orWhere('description', 'like', "%$s%");
            });
        }

        // Do not limit selected columns to avoid edge-cases with traits/scopes
        $page = $query->orderBy('created_at', 'desc')->paginate(20);

        $page->getCollection()->transform(function ($r) {
            $type = $r->reportable_type;
            $id = $r->reportable_id;
            $r->reportable_title = null;
            if ($type === 'listing') {
                $listing = DB::table('listings')->select('title', 'status', 'seller_id')->where('id', $id)->first();
                if ($listing) {
                    $r->reportable_title = $listing->title;
                    $r->reportable_status = $listing->status;
                    $r->reportable_seller = DB::table('users')->where('id', $listing->seller_id)->value('name');
                }
                $r->report_link = url("/listings/{$id}");
            } elseif ($type === 'user' || $type === 'App\\Models\\User') {
                $r->reportable_title = DB::table('users')->where('id', $id)->value('name');
                $r->report_link = url("/admin/users?focus={$id}");
            } else {
                $r->report_link = null;
            }

            try {
                $r->evidence = method_exists($r, 'getMedia') ? $r->getMedia('evidence')->map->getUrl()->all() : [];
            } catch (\Throwable $e) {
                $r->evidence = [];
            }
            return $r;
        });

        \Log::info('listReportsForAdmin', [
            'total_all' => Report::count(),
            'filters' => $filters,
            'page_total' => $page->total(),
            'page_count' => count($page->items()),
        ]);
        return $page;
    }

    public function handleReportByAdmin(Report $report, string $action, ?string $adminNotes): Report
    {
        $map = [
            'accept' => 'reviewed',
            'resolve' => 'resolved',
            'reject' => 'dismissed',
        ];

        $newStatus = $map[$action] ?? 'reviewed';

        $report->update([
            'status' => $newStatus,
            'admin_notes' => $adminNotes,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        // Notify reporter
        try {
            $user = \App\Models\User::find($report->reporter_id);
            if ($user) {
                $user->notifications()->create([
                    'type' => 'report_result',
                    'title' => 'Kết quả báo cáo',
                    'message' => $this->messageForStatus($newStatus),
                    'data' => [
                        'report_id' => $report->id,
                        'status' => $newStatus,
                        'admin_notes' => $adminNotes,
                        'reportable_type' => $report->reportable_type,
                        'reportable_id' => $report->reportable_id,
                    ],
                ]);
            }
        } catch (\Throwable $e) {
        }

        return $report->fresh();
    }

    private function normalizeType(string $type): string
    {
        $normalized = str_replace('\\\\', '\\', $type);
        $map = [
            'App\\Models\\Listing' => 'listing',
            'App\\Models\\User' => 'user',
            'App\\Models\\Review' => 'review',
        ];
        return $map[$normalized] ?? $normalized;
    }

    private function messageForStatus(string $status): string
    {
        return match ($status) {
            'reviewed' => 'Báo cáo của bạn đã được tiếp nhận và đang được xem xét.',
            'resolved' => 'Báo cáo của bạn đã được xử lý. Cảm ơn bạn đã đóng góp.',
            'dismissed' => 'Báo cáo của bạn không đủ cơ sở để xử lý. Vui lòng xem ghi chú của quản trị.',
            default => 'Cập nhật trạng thái báo cáo',
        };
    }
   public function exportCsv($reports): string
{
    $output = fopen('php://temp', 'r+');

    // ✨ Tiêu đề cột tiếng Việt
    fputcsv($output, [
        'ID',
        'Người báo cáo',
        'Loại đối tượng',
        'Tiêu đề',
        'Lý do',
        'Mô tả',
        'Trạng thái',
        'Ngày tạo'
    ]);

    // Map lý do & trạng thái giống Vue
    $reasonMap = [
        'fraud' => 'Lừa đảo',
        'fake_product' => 'Hàng giả',
        'spam' => 'Spam',
        'inappropriate_content' => 'Nội dung không phù hợp',
        'price_manipulation' => 'Thao túng giá',
        'fake_reviews' => 'Đánh giá giả',
        'harassment' => 'Quấy rối',
        'copyright_violation' => 'Vi phạm bản quyền',
        'other' => 'Khác',
    ];
    $statusMap = [
        'pending' => 'Chờ xử lý',
        'reviewed' => 'Đang xem xét',
        'resolved' => 'Đã xử lý',
        'dismissed' => 'Bị từ chối',
    ];
    $typeMap = [
        'listing' => 'Tin rao',
        'user' => 'Người dùng',
        'review' => 'Đánh giá',
        'App\\Models\\Listing' => 'Tin rao',
        'App\\Models\\User' => 'Người dùng',
        'App\\Models\\Review' => 'Đánh giá',
    ];

    foreach ($reports as $r) {
        $reporterName = \App\Models\User::where('id', $r->reporter_id)->value('name') ?? 'Không rõ';
        $reasonLabel = $reasonMap[$r->reason] ?? $r->reason;
        $statusLabel = $statusMap[$r->status] ?? $r->status;
        $typeLabel = $typeMap[$r->reportable_type] ?? $r->reportable_type;

        // Lấy tiêu đề và link (nếu có)
        $title = $r->reportable_title ?? '';
        $link = $r->report_link ?? '';

        fputcsv($output, [
            $r->id,
            $reporterName,
            $typeLabel,
            $title,
            $reasonLabel,
            $r->description,
            $statusLabel,
            optional($r->created_at)->format('Y-m-d H:i:s'),
        ]);
    }

    rewind($output);
    $csv = stream_get_contents($output);
    fclose($output);

    // 💡 Thêm BOM để Excel đọc tiếng Việt đúng
    $csv = "\xEF\xBB\xBF" . $csv;

    return $csv;
}


public function exportXlsx($reports): string
{
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $sheet->fromArray([
        'ID',
        'Người báo cáo',
        'Loại đối tượng',
        'Tiêu đề',
        'Lý do',
        'Mô tả',
        'Trạng thái',
        'Ngày tạo'
    ], null, 'A1');

    $reasonMap = [
        'fraud' => 'Lừa đảo',
        'fake_product' => 'Hàng giả',
        'spam' => 'Spam',
        'inappropriate_content' => 'Nội dung không phù hợp',
        'price_manipulation' => 'Thao túng giá',
        'fake_reviews' => 'Đánh giá giả',
        'harassment' => 'Quấy rối',
        'copyright_violation' => 'Vi phạm bản quyền',
        'other' => 'Khác',
    ];
    $statusMap = [
        'pending' => 'Chờ xử lý',
        'reviewed' => 'Đang xem xét',
        'resolved' => 'Đã xử lý',
        'dismissed' => 'Bị từ chối',
    ];
    $typeMap = [
        'listing' => 'Tin rao',
        'user' => 'Người dùng',
        'review' => 'Đánh giá',
        'App\\Models\\Listing' => 'Tin rao',
        'App\\Models\\User' => 'Người dùng',
        'App\\Models\\Review' => 'Đánh giá',
    ];

    $row = 2;
    foreach ($reports as $r) {
        $reporterName = \App\Models\User::where('id', $r->reporter_id)->value('name') ?? 'Không rõ';
        $reasonLabel = $reasonMap[$r->reason] ?? $r->reason;
        $statusLabel = $statusMap[$r->status] ?? $r->status;
        $typeLabel = $typeMap[$r->reportable_type] ?? $r->reportable_type;

        $title = $r->reportable_title ?? '';
        $sheet->fromArray([
            $r->id,
            $reporterName,
            $typeLabel,
            $title,
            $reasonLabel,
            $r->description,
            $statusLabel,
            optional($r->created_at)->format('Y-m-d H:i:s'),
        ], null, "A{$row}");
        $row++;
    }

    // ✅ Auto-fit tất cả cột
    foreach (range('A','I') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $temp = tmpfile();
    $path = stream_get_meta_data($temp)['uri'];
    $writer->save($path);
    return file_get_contents($path);
}

}
