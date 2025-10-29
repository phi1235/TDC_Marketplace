<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditLogService
{
    public function list(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = AuditLog::query()->with('user');

        if (!empty($filters['action'])) {
            $query->where('action', (string) $filters['action']);
        }
        if (!empty($filters['user_id'])) {
            $query->where('user_id', (int) $filters['user_id']);
        }
        if (!empty($filters['auditable_type'])) {
            $query->where('auditable_type', (string) $filters['auditable_type']);
        }
        if (!empty($filters['search'])) {
            $s = (string) $filters['search'];
            $query->where(function ($q) use ($s) {
                $q->where('action', 'like', "%$s%")
                  ->orWhere('ip_address', 'like', "%$s%")
                  ->orWhere('user_agent', 'like', "%$s%");
            });
        }

        return $query->orderByDesc('created_at')->paginate($perPage);
    }

    /**
     * Ghi log chuẩn hoá cho bất kỳ model auditable nào
     */
    public function log(Model $auditable, string $action, ?array $oldValues = null, ?array $newValues = null): void
    {
        try {
            $auditable->auditLogs()->create([
                'user_id' => Auth::id(),
                'action' => $action,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Throwable $e) {
            // swallow logging errors
        }
    }
}


