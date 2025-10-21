<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FollowSeller;

class FollowSellerController extends Controller
{
    public function __construct()
    {
        // Chỉ user đã login mới thao tác
        $this->middleware('auth:sanctum');
    }

    // Follow seller
    public function follow(Request $request)
    {
        $userId = $request->user()->id;
        $sellerId = $request->input('seller_id');

        if (FollowSeller::where('user_id', $userId)->where('seller_id', $sellerId)->exists()) {
            return response()->json(['message' => 'Bạn đã follow seller này'], 400);
        }

        FollowSeller::create([
            'user_id' => $userId,
            'seller_id' => $sellerId,
        ]);

        return response()->json(['message' => 'Follow thành công']);
    }

    // Unfollow seller
    public function unfollow(Request $request, $sellerId)
    {
        $userId = $request->user()->id;

        FollowSeller::where('user_id', $userId)->where('seller_id', $sellerId)->delete();

        return response()->json(['message' => 'Unfollow thành công']);
    }

    // Status follow
    public function status(Request $request, $sellerId)
    {
        $userId = $request->user()->id;

        $isFollowing = FollowSeller::where('user_id', $userId)->where('seller_id', $sellerId)->exists();

        return response()->json(['isFollowing' => $isFollowing]);
    }
}
