<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        // $wishlists = Auth::user()->wishlists()
        //     ->with(['listing.seller', 'listing.category', 'listing.images'])
        //     ->orderBy('created_at', 'desc')
        //     ->paginate(20);

        // return response()->json($wishlists);

        //get api no depend user_error
        // $wishlists = Wishlist::with(['listing.seller', 'listing.category', 'listing.images'])
        // ->orderBy('created_at', 'desc')
        // ->paginate(20);

        // return response()->json($wishlists);

        //get api = user id is logining
        $user = $request->user(); // Đã CÓ user ✅

        $wishlists = Wishlist::with(['listing.seller', 'listing.category', 'listing.images'])
        ->where('user_id', $user->id) // lấy đúng wishlist theo user
        ->orderBy('created_at', 'desc')
        ->get(); // nếu muốn length => nên get thay vì paginate

        return response()->json($wishlists);
    }

public function toggle(Request $request, Listing $listing): JsonResponse
{
    $user = Auth::user();

    // Kiểm tra user đã thích listing chưa
    $wishlist = $user->wishlists()->where('listing_id', $listing->id)->first();

    if ($wishlist) {
        // Nếu đã có → xóa
        $wishlist->delete();
        $isFavorited = false;
    } else {
        // Nếu chưa có → thêm
        $user->wishlists()->create([
            'listing_id' => $listing->id,
        ]);
        $isFavorited = true;
    }

    // Tổng wishlist của user (nếu muốn hiển thị số lượng)
    $total = $user->wishlists()->count();

    return response()->json([
        'message' => $isFavorited ? 'Đã thêm vào danh sách yêu thích' : 'Đã xóa khỏi danh sách yêu thích',
        'is_favorited' => $isFavorited,
        'total' => $total, // nếu FE không cần có thể bỏ
    ]);
}


}
