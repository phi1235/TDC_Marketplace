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
        $listings = Listing::where('status', 'pending')
            ->with(['seller', 'category', 'images'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($listings);
    }

    public function approveListing(Request $request, Listing $listing): JsonResponse
    {
        $listing->update([
            'status' => 'approved',
            'admin_notes' => $request->admin_notes,
        ]);

        return response()->json([
            'message' => 'Tin rao đã được duyệt',
            'listing' => $listing,
        ]);
    }

    public function rejectListing(Request $request, Listing $listing): JsonResponse
    {
        $listing->update([
            'status' => 'rejected',
            'admin_notes' => $request->admin_notes,
        ]);

        return response()->json([
            'message' => 'Tin rao đã bị từ chối',
            'listing' => $listing,
        ]);
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
