<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'score' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        $user = $request->user();
        $order = Order::with(['buyer', 'seller'])->findOrFail($request->order_id);

        if ($order->status !== 'completed') {
            return response()->json(['message' => 'Chỉ có thể đánh giá sau khi đơn hàng hoàn tất.'], 400);
        }

        // Xác định hướng đánh giá
        if ($user->id === $order->buyer_id) {
            $toUserId = $order->seller_id;
        } elseif ($user->id === $order->seller_id) {
            $toUserId = $order->buyer_id;
        } else {
            return response()->json(['message' => 'Bạn không liên quan đến đơn hàng này.'], 403);
        }

        // Chặn đánh giá trùng
        $existing = Rating::where('order_id', $order->id)
            ->where('from_user_id', $user->id)
            ->first();

        if ($existing) {
            return response()->json(['message' => 'Bạn đã đánh giá đơn hàng này rồi.'], 400);
        }

        // Tạo đánh giá mới
        $rating = Rating::create([
            'order_id' => $order->id,
            'from_user_id' => $user->id,
            'to_user_id' => $toUserId,
            'score' => $request->score,
            'comment' => $request->comment,
        ]);

        // Cập nhật tổng rating trong bảng users
        $target = User::find($toUserId);
        if ($target) {
            $target->total_ratings += 1;
            $target->rating = Rating::where('to_user_id', $toUserId)->avg('score');
            $target->save();
        }

        return response()->json([
            'success' => true,
            'message' => '🎉 Đánh giá đã được gửi thành công!',
            'rating' => $rating
        ]);
    }

    public function userRatings($userId)
    {
        $ratings = Rating::with('fromUser:id,name')
            ->where('to_user_id', $userId)
            ->latest()
            ->get();

        return response()->json($ratings);
    }
}
