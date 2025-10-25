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
        $wishlist = Auth::user()->wishlists()
            ->where('listing_id', $listing->id)
            ->first();

        if ($wishlist) {
            $wishlist->delete();
            $listing->decrement('favorite_count');
            
            return response()->json([
                'message' => 'Đã xóa khỏi danh sách yêu thích',
                'is_favorited' => false,
            ]);
        } else {
            Auth::user()->wishlists()->create([
                'listing_id' => $listing->id,
            ]);
            $listing->increment('favorite_count');
            
            return response()->json([
                'message' => 'Đã thêm vào danh sách yêu thích',
                'is_favorited' => true,
            ]);
        }
    }
}
