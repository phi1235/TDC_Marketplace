<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Listing;
use App\Models\User;

class OrderController extends Controller
{
    /**
     * Người mua bấm "Mua ngay" → tạo đơn hàng mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'listing_id' => 'required|exists:listings,id',
        ]);

        $buyer = $request->user();
        $listing = Listing::findOrFail($request->listing_id);

        // ✅ 1. Chặn người bán tự mua chính sản phẩm của mình
        if ($listing->seller_id === $buyer->id) {
            return response()->json([
                'success' => false,
                'message' => '🚫 Bạn không thể mua sản phẩm của chính mình.'
            ], 400);
        }

        // ✅ 2. Kiểm tra đơn hàng đã tồn tại (pending/confirmed)
        $existingOrder = Order::where('buyer_id', $buyer->id)
            ->where('listing_id', $listing->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->first();

        if ($existingOrder) {
            // 🔁 Tăng số lượng thay vì tạo mới
            $existingOrder->increment('quantity', 1);
            $existingOrder->total_amount = $existingOrder->quantity * $existingOrder->product_price;
            $existingOrder->save();

            return response()->json([
                'success' => true,
                'message' => '🔄 Đã cập nhật số lượng trong đơn hàng hiện có.',
                'order' => $existingOrder,
                'updated' => true
            ]);
        }

        // ✅ 3. Nếu chưa có đơn thì tạo mới
        $order = Order::create([
            'order_number' => 'ORD' . time() . rand(1000, 9999),
            'buyer_id' => $buyer->id,
            'seller_id' => $listing->seller_id,
            'listing_id' => $listing->id,
            'product_title' => $listing->title,
            'product_price' => $listing->price,
            'quantity' => 1,
            'total_amount' => $listing->price,
            'currency' => 'VND',
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => '✅ Đơn hàng đã được tạo thành công!',
            'order' => $order,
            'updated' => false
        ]);
    }

    /**
     * Người bán xác nhận đơn hàng
     */
    public function confirm($id)
    {
        $order = Order::findOrFail($id);

        // Chỉ người bán mới được xác nhận
        $seller = auth()->user();
        if ($seller->id !== $order->seller_id) {
            return response()->json(['message' => 'Bạn không có quyền xác nhận đơn này!'], 403);
        }

        $order->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);

        return response()->json([
            'message' => 'Người bán đã xác nhận đơn hàng!',
            'order' => $order
        ]);
    }

    /**
     * Người mua xem danh sách đơn của mình
     */
    public function myOrders(Request $request)
    {
        $user = $request->user();

        $buyerOrders = \App\Models\Order::where('buyer_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $sellerOrders = \App\Models\Order::where('seller_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'buyer_orders' => $buyerOrders,
            'seller_orders' => $sellerOrders,
        ]);
    }

    /**
     * Người bán xem các đơn cần xác nhận
     */
    public function receivedOrders()
    {
        $orders = Order::where('seller_id', auth()->id())->latest()->get();
        return response()->json($orders);
    }

    public function show($id)
    {
        $order = \App\Models\Order::with(['seller:id,name,email,phone', 'buyer:id,name,email'])
            ->findOrFail($id);
        return response()->json($order);
    }
    public function payWithEscrow($id)
    {
        try {
            $order = Order::with('escrowAccount')->findOrFail($id);

            // Chỉ người mua mới được thanh toán
            if (auth()->id() !== $order->buyer_id) {
                return response()->json(['message' => 'Bạn không có quyền thanh toán đơn hàng này.'], 403);
            }

            //  Nếu đơn không phải pending thì không cho thanh toán lại
            if ($order->status !== 'pending') {
                return response()->json(['message' => 'Đơn hàng này không thể thanh toán.'], 400);
            }

            // Nếu chưa có escrow thì tạo
            $escrow = $order->escrowAccount;
            if (!$escrow) {
                $escrow = $order->escrowAccount()->create([
                    'order_id'  => $order->id,
                    'buyer_id'  => $order->buyer_id,
                    'seller_id' => $order->seller_id,
                    'amount'    => $order->total_amount,
                    'currency'  => $order->currency ?? 'VND',
                    'status'    => 'holding',
                    'held_at'   => now(),
                ]);
            }

            //  Cập nhật trạng thái đơn
            $order->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => '💳 Thanh toán thành công! Tiền đang được giữ trong tài khoản trung gian.',
                'order'   => $order,
                'escrow'  => $escrow,
            ]);
        } catch (\Exception $e) {
            \Log::error('Lỗi thanh toán escrow: ' . $e->getMessage(), [
                'order_id' => $id,
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xử lý thanh toán.',
            ], 500);
        }
    }
    /**
     * 🟣 Seller đánh dấu đã giao hàng
     */
    public function markShipped($id)
    {
        $order = Order::findOrFail($id);

        if (auth()->id() !== $order->seller_id) {
            return response()->json(['message' => 'Bạn không có quyền giao đơn này.'], 403);
        }

        if ($order->status !== 'confirmed') {
            return response()->json(['message' => 'Đơn hàng chưa được xác nhận hoặc đã giao.'], 400);
        }

        $order->update([
            'status' => 'shipped',
            'shipped_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => '📦 Đơn hàng đã được đánh dấu là "Đang giao".',
            'order' => $order
        ]);
    }

    /**
     * 🟢 Buyer xác nhận đã nhận hàng
     */
    public function markDelivered($id)
    {
        $order = Order::findOrFail($id);

        if (auth()->id() !== $order->buyer_id) {
            return response()->json(['message' => 'Bạn không có quyền xác nhận đơn này.'], 403);
        }

        if ($order->status !== 'shipped') {
            return response()->json(['message' => 'Đơn hàng chưa được giao hoặc đã nhận.'], 400);
        }

        $order->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => '✅ Bạn đã xác nhận đã nhận hàng.',
            'order' => $order
        ]);
    }

    /**
     * 🎉 Buyer hoàn tất đơn hàng (giải phóng escrow)
     */
    public function completeOrder($id)
    {
        $order = Order::with(['escrowAccount', 'listing', 'seller'])->findOrFail($id);

        if (auth()->id() !== $order->buyer_id) {
            return response()->json(['message' => 'Bạn không có quyền hoàn tất đơn này.'], 403);
        }

        if ($order->status !== 'delivered') {
            return response()->json(['message' => 'Đơn hàng chưa được giao hoặc đã hoàn tất.'], 400);
        }

        // ✅ Cập nhật trạng thái đơn hàng
        $order->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // ✅ Cập nhật Escrow (giải phóng tiền)
        if ($order->escrowAccount) {
            $order->escrowAccount->update([
                'status' => 'released',
                'released_at' => now(),
                'escrow_status' => 'released',

            ]);
        }

        // ✅ Đánh dấu sản phẩm là "Đã bán"
        if ($order->listing) {
            $order->listing->update([
                'status' => 'sold',
            ]);
        }

        // ✅ Cập nhật seller stats trực tiếp trong bảng users
        if ($order->seller) {
            $seller = $order->seller;
            $seller->increment('total_sales', 1);
            $seller->total_revenue += $order->total_amount;
            $seller->save();
        }

        return response()->json([
            'success' => true,
            'message' => '🎉 Đơn hàng đã hoàn tất! Tiền đã được chuyển cho người bán, và sản phẩm đã đánh dấu "Đã bán".',
            'order' => $order
        ]);
    }
}
