<?php

namespace App\Http\Controllers;

use App\Models\Dispute;
use App\Models\Order;
use App\Models\Listing;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class DisputeController extends Controller
{
    /**
     * 🆕 Mở khiếu nại
     */
    public function store(Request $request)
    {
        $request->validate([
            'listing_id' => 'required|exists:listings,id',
            'reason' => 'required|string|min:20|max:2000',
        ]);

        $user = $request->user();
        $listing = Listing::findOrFail($request->listing_id);

        // ✅ Kiểm tra người này có thuộc giao dịch không
        $order = Order::where('listing_id', $listing->id)
            ->where(function ($q) use ($user) {
                $q->where('buyer_id', $user->id)
                    ->orWhere('seller_id', $user->id);
            })
            ->first();

        if (!$order) {
            return response()->json(['message' => 'Bạn không có quyền khiếu nại giao dịch này.'], 403);
        }

        // ✅ Kiểm tra trùng dispute
        $existing = Dispute::where('order_id', $order->id)
            ->where('status', 'open')
            ->first();

        if ($existing) {
            return response()->json(['message' => 'Bạn đã gửi khiếu nại cho giao dịch này rồi.'], 409);
        }

        $againstId = $user->id === $order->buyer_id ? $order->seller_id : $order->buyer_id;

        $dispute = Dispute::create([
            'listing_id' => $listing->id,
            'order_id' => $order->id,
            'opener_id' => $user->id,
            'against_user_id' => $againstId,
            'reason' => $request->reason,
            'status' => 'open',
        ]);

        // ✏️ Ghi Audit Log
        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'dispute.open',
            'auditable_type' => 'dispute',
            'auditable_id' => $dispute->id,
            'new_values' => ['reason' => $request->reason, 'status' => 'open'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => '🎫 Khiếu nại đã được mở thành công.',
            'dispute' => $dispute
        ], 201);
    }

    /**
     * 📋 Danh sách khiếu nại của user
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $disputes = Dispute::with(['listing:id,title', 'order:id,order_number', 'auditLogs'])
            ->where('opener_id', $user->id)
            ->orWhere('against_user_id', $user->id)
            ->latest()
            ->get();

        return response()->json($disputes);
    }

    /**
     * 🔍 Xem chi tiết
     */
    public function show($id)
    {
        $user = auth()->user();
        $dispute = Dispute::with(['listing', 'order', 'opener:id,name', 'againstUser:id,name', 'auditLogs'])
            ->findOrFail($id);

        if (!in_array($user->id, [$dispute->opener_id, $dispute->against_user_id]) && !$user->is_admin) {
            return response()->json(['message' => 'Bạn không có quyền truy cập khiếu nại này.'], 403);
        }

        return response()->json($dispute);
    }

    /**
     * 🏁 Đóng khiếu nại
     */
    public function close($id)
    {
        $user = auth()->user();
        $dispute = Dispute::findOrFail($id);

        if (!in_array($user->id, [$dispute->opener_id, $dispute->against_user_id]) && !$user->is_admin) {
            return response()->json(['message' => 'Bạn không có quyền đóng khiếu nại này.'], 403);
        }

        $oldStatus = $dispute->status;

        $dispute->update([
            'status' => 'closed',
            'resolved_at' => now(),
        ]);

        // ✏️ Ghi Audit Log
        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'dispute.close',
            'auditable_type' => 'dispute',
            'auditable_id' => $dispute->id,
            'old_values' => ['status' => $oldStatus],
            'new_values' => ['status' => 'closed'],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => '✅ Khiếu nại đã được đóng.',
            'dispute' => $dispute,
        ]);
    }
    public function adminIndex()
{
    $disputes = \App\Models\Dispute::with([
        'opener:id,name,email',
        'againstUser:id,name,email',
        'listing:id,title',
        'order:id,order_number'
    ])
        ->latest()
        ->paginate(10);

    return response()->json($disputes);
}

/**
 * 🔍 Xem chi tiết khiếu nại (Admin)
 */
public function adminShow($id)
{
    $dispute = \App\Models\Dispute::with([
        'opener:id,name,email',
        'againstUser:id,name,email',
        'listing:id,title',
        'order:id,order_number,status,total_amount'
    ])->findOrFail($id);

    return response()->json($dispute);
}

/**
 * 🧾 Cập nhật trạng thái khiếu nại (Admin xử lý)
 */
public function adminUpdate(Request $request, $id)
{
    $request->validate([
        'status' => 'required|in:open,under_review,resolved,rejected,closed',
        'admin_note' => 'nullable|string|max:2000',
    ]);

    $dispute = \App\Models\Dispute::findOrFail($id);

    $oldStatus = $dispute->status;

    $dispute->update([
        'status' => $request->status,
        'admin_note' => $request->admin_note,
        'resolved_at' => in_array($request->status, ['resolved', 'rejected', 'closed']) ? now() : null,
    ]);

    // 📜 Ghi log hành động (nếu có bảng AuditLog)
    if (class_exists(\App\Models\AuditLog::class)) {
        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'admin.update_dispute_status',
            'auditable_type' => 'dispute',
            'auditable_id' => $dispute->id,
            'old_values' => ['status' => $oldStatus],
            'new_values' => ['status' => $request->status],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }

    return response()->json([
        'message' => 'Trạng thái khiếu nại đã được cập nhật thành công.',
        'dispute' => $dispute
    ]);
}
}
