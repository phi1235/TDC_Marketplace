<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('role:admin');
    }

    public function dashboard(): JsonResponse
    {
        $stats = [
            'total_users' => User::count(),
            'active_listings' => Listing::where('status', 'approved')->count(),
            'pending_listings' => Listing::where('status', 'pending')->count(),
            'total_reports' => Report::count(),
            'pending_reports' => Report::where('status', 'pending')->count(),
        ];

        return response()->json($stats);
    }

    public function pendingListings(Request $request): JsonResponse
    {
        $query = Listing::where('status', 'pending')
            ->with(['seller', 'category', 'images']);

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by seller
        if ($request->has('seller_id')) {
            $query->where('seller_id', $request->seller_id);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $perPage = (int)($request->get('per_page', 10));
        $listings = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json($listings);
    }

    public function allListings(Request $request): JsonResponse
    {
        $query = Listing::with(['seller', 'category', 'images']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by seller
        if ($request->has('seller_id')) {
            $query->where('seller_id', $request->seller_id);
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('seller', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Sort
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = (int)($request->get('per_page', 10));
        $listings = $query->paginate($perPage);

        return response()->json($listings);
    }

    public function listingStats(): JsonResponse
    {
        $stats = [
            'total_listings' => Listing::count(),
        ];

        return response()->json($stats);
    }

    public function bulkAction(Request $request): JsonResponse
    {
        $request->validate([
            'action' => 'required|in:approve,reject,delete',
            'listing_ids' => 'required|array|min:1',
            'listing_ids.*' => 'exists:listings,id',
            'admin_notes' => 'nullable|string|max:500',
            'rejection_reason' => 'required_if:action,reject|in:inappropriate_content,spam,duplicate,policy_violation,other',
        ]);

        $listings = Listing::whereIn('id', $request->listing_ids);
        $action = $request->action;
        $adminNotes = $request->admin_notes;
        $rejectionReason = $request->rejection_reason;

        try {
            switch ($action) {
                case 'approve':
                    $listings->update([
                        'status' => 'approved',
                        'admin_notes' => $adminNotes,
                        'approved_at' => now(),
                        'approved_by' => Auth::id(),
                    ]);
                    break;

                case 'reject':
                    $listings->update([
                        'status' => 'rejected',
                        'admin_notes' => $adminNotes,
                        'rejection_reason' => $rejectionReason,
                        'rejected_at' => now(),
                        'rejected_by' => Auth::id(),
                    ]);
                    break;

                case 'delete':
                    // Only allow deletion of pending or rejected listings
                    $toDelete = $listings->whereIn('status', ['pending', 'rejected'])->get();
                    foreach ($toDelete as $item) {
                        // Avoid triggering Scout indexing if configured
                        Listing::withoutSyncingToSearch(function () use ($item) {
                            // delete relations first to avoid FK constraints
                            try { $item->images()->delete(); } catch (\Throwable $e) {}
                            try { $item->offers()->delete(); } catch (\Throwable $e) {}
                            try { $item->views()->delete(); } catch (\Throwable $e) {}
                            try { $item->wishlists()->delete(); } catch (\Throwable $e) {}
                            try { $item->reviews()->delete(); } catch (\Throwable $e) {}
                            $item->delete();
                        });
                    }
                    break;
            }

            // Log bulk action
            foreach ($request->listing_ids as $listingId) {
                $listing = Listing::find($listingId);
                if ($listing) {
                    $listing->auditLogs()->create([
                        'user_id' => Auth::id(),
                        'action' => 'admin_bulk_' . $action,
                        'old_values' => null,
                        'new_values' => ['action' => $action, 'admin_notes' => $adminNotes],
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                    ]);
                }
            }

            return response()->json([
                'message' => "Đã thực hiện hành động '{$action}' cho " . count($request->listing_ids) . " tin rao",
                'processed_count' => count($request->listing_ids),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi thực hiện hành động hàng loạt',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function approveListing(Request $request, Listing $listing): JsonResponse
    {
        try {
            $request->validate([
                'admin_notes' => 'nullable|string|max:500',
            ]);

            $oldStatus = $listing->status;
            
            if ($oldStatus !== 'pending') {
                return response()->json([
                    'message' => 'Chỉ có thể duyệt tin đang chờ duyệt',
                ], 400);
            }

            // Avoid triggering Scout indexing if not configured
            Listing::withoutSyncingToSearch(function () use ($request, $listing) {
                $listing->update([
                    'status' => 'approved',
                    'admin_notes' => $request->admin_notes,
                    'approved_at' => now(),
                    'approved_by' => Auth::id(),
                ]);
            });

            // Log admin activity
            $listing->auditLogs()->create([
                'user_id' => Auth::id(),
                'action' => 'admin_approved',
                'old_values' => ['status' => $oldStatus],
                'new_values' => ['status' => 'approved', 'admin_notes' => $request->admin_notes],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // Notify seller
            $listing->seller->notifications()->create([
                'type' => 'listing_approved',
                'title' => 'Tin rao đã được duyệt',
                'message' => "Tin rao '{$listing->title}' của bạn đã được duyệt và hiển thị công khai.",
                'data' => ['listing_id' => $listing->id],
            ]);

            return response()->json([
                'message' => 'Tin rao đã được duyệt thành công',
                'listing' => $listing->load(['seller', 'category', 'images']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi duyệt tin rao',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function rejectListing(Request $request, Listing $listing): JsonResponse
    {
        try {
            $request->validate([
                'admin_notes' => 'required|string|max:500',
                'rejection_reason' => 'required|in:inappropriate_content,spam,duplicate,policy_violation,other',
            ]);

            $oldStatus = $listing->status;
            
            if ($oldStatus !== 'pending') {
                return response()->json([
                    'message' => 'Chỉ có thể từ chối tin đang chờ duyệt',
                ], 400);
            }

            // Avoid triggering Scout indexing if not configured
            Listing::withoutSyncingToSearch(function () use ($request, $listing) {
                $listing->update([
                    'status' => 'rejected',
                    'admin_notes' => $request->admin_notes,
                    'rejection_reason' => $request->rejection_reason,
                    'rejected_at' => now(),
                    'rejected_by' => Auth::id(),
                ]);
            });

            // Log admin activity
            $listing->auditLogs()->create([
                'user_id' => Auth::id(),
                'action' => 'admin_rejected',
                'old_values' => ['status' => $oldStatus],
                'new_values' => [
                    'status' => 'rejected', 
                    'admin_notes' => $request->admin_notes,
                    'rejection_reason' => $request->rejection_reason
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            // Notify seller
            $listing->seller->notifications()->create([
                'type' => 'listing_rejected',
                'title' => 'Tin rao bị từ chối',
                'message' => "Tin rao '{$listing->title}' của bạn đã bị từ chối. Lý do: {$request->rejection_reason}",
                'data' => [
                    'listing_id' => $listing->id,
                    'rejection_reason' => $request->rejection_reason,
                    'admin_notes' => $request->admin_notes
                ],
            ]);

            return response()->json([
                'message' => 'Tin rao đã bị từ chối',
                'listing' => $listing->load(['seller', 'category', 'images']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi từ chối tin rao',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function reports(Request $request): JsonResponse
    {
        $reports = Report::with(['reporter', 'reportable'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($reports);
    }

    public function handleReport(Request $request, Report $report): JsonResponse
    {
        $report->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
        ]);

        return response()->json([
            'message' => 'Báo cáo đã được xử lý',
            'report' => $report,
        ]);
    }

    public function users(Request $request): JsonResponse
    {
        $users = User::with('sellerProfile')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($users);
    }

    public function toggleUserStatus(Request $request, User $user): JsonResponse
    {
        $user->update(['is_active' => !$user->is_active]);

        return response()->json([
            'message' => 'Trạng thái người dùng đã được cập nhật',
            'user' => $user,
        ]);
    }
}
