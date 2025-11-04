<?php

namespace App\Repositories;

use App\Models\Report;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ReportRepository
{
    protected Report $model;

    public function __construct(Report $model)
    {
        $this->model = $model;
    }

    /**
     * Tạo báo cáo mới
     */
    public function create(array $data): Report
    {
        return $this->model->create($data);
    }

    /**
     * Tìm báo cáo theo ID
     */
    public function find(int $id): ?Report
    {
        return $this->model->find($id);
    }

    /**
     * Lấy danh sách báo cáo của user với filter
     */
    public function getUserReports(int $userId, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->where('reporter_id', $userId)
            ->with(['reportable']);

        // Filter by status
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filter by reportable type
        if (isset($filters['type']) && $filters['type'] !== 'all' && $filters['type'] !== '') {
            $type = str_replace('\\\\', '\\', (string) $filters['type']);
            $map = [
                'App\\Models\\Listing' => 'listing',
                'App\\Models\\User' => 'user',
                'App\\Models\\Review' => 'review',
            ];
            $normalized = $map[$type] ?? $type; // accept both class string or morph key
            $query->where('reportable_type', $normalized);
        }

        // Search by reason
        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('reason', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $filters['sort'] ?? 'created_at';
        $sortOrder = $filters['order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        $perPage = (int)($filters['per_page'] ?? 15);
        
        $paginator = $query->paginate($perPage);
        $paginator->getCollection()->transform(function ($r) {
            try {
                $r->evidence = method_exists($r, 'getMedia') ? $r->getMedia('evidence')->map->getUrl()->all() : [];
            } catch (\Throwable $e) {
                $r->evidence = [];
            }
            return $r;
        });
        return $paginator;
    }

    /**
     * Lấy thống kê báo cáo của user
     */
    public function getUserReportStats(int $userId): array
    {
        return [
            'total_reports' => $this->model->where('reporter_id', $userId)->count(),
            'pending_reports' => $this->model->where('reporter_id', $userId)->where('status', 'pending')->count(),
            'reviewed_reports' => $this->model->where('reporter_id', $userId)->where('status', 'reviewed')->count(),
            'resolved_reports' => $this->model->where('reporter_id', $userId)->where('status', 'resolved')->count(),
            'dismissed_reports' => $this->model->where('reporter_id', $userId)->where('status', 'dismissed')->count(),
        ];
    }

    /**
     * Kiểm tra user có quyền xem báo cáo không
     */
    public function canUserViewReport(int $userId, int $reportId): bool
    {
        return $this->model->where('id', $reportId)
            ->where('reporter_id', $userId)
            ->exists();
    }
}
