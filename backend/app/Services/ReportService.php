<?php

namespace App\Services;

use App\Models\Report;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class ReportService
{
    /**
     * Tạo báo cáo mới
     */
    public function createReport(array $data): Report
    {
        $report = Report::create([
            'reporter_id' => Auth::id(),
            'reportable_type' => $data['reportable_type'],
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
}
