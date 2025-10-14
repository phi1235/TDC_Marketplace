<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOfferRequest;
use App\Models\Listing;
use App\Models\Offer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OfferController extends Controller
{
    public function store(StoreOfferRequest $request, Listing $listing): JsonResponse
    {
        // Check if user is not the seller
        if ($listing->seller_id === Auth::id()) {
            return response()->json(['message' => 'Bạn không thể đặt giá cho sản phẩm của chính mình'], 403);
        }

        // Check if listing is available
        if ($listing->status !== 'approved') {
            return response()->json(['message' => 'Sản phẩm không khả dụng'], 400);
        }

        // Check if user already has an active offer
        $existingOffer = Auth::user()->offers()
            ->where('listing_id', $listing->id)
            ->where('status', 'pending')
            ->first();

        if ($existingOffer) {
            return response()->json(['message' => 'Bạn đã có đề nghị đang chờ xử lý cho sản phẩm này'], 400);
        }

        $offer = Auth::user()->offers()->create([
            'listing_id' => $listing->id,
            'offer_price' => $request->offer_price,
            'message' => $request->message,
            'expires_at' => now()->addDays(7),
        ]);

        return response()->json([
            'message' => 'Đề nghị đã được gửi thành công',
            'offer' => $offer,
        ], 201);
    }

    public function index(Request $request): JsonResponse
    {
        $offers = Auth::user()->offers()
            ->with(['listing.seller', 'listing.images'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($offers);
    }

    public function receivedOffers(Request $request): JsonResponse
    {
        $offers = Offer::whereHas('listing', function ($query) {
                $query->where('seller_id', Auth::id());
            })
            ->with(['buyer', 'listing.images'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($offers);
    }

    public function accept(Request $request, Offer $offer): JsonResponse
    {
        // Check if user is the seller
        if ($offer->listing->seller_id !== Auth::id()) {
            return response()->json(['message' => 'Bạn không có quyền chấp nhận đề nghị này'], 403);
        }

        if ($offer->status !== 'pending') {
            return response()->json(['message' => 'Đề nghị không thể chấp nhận'], 400);
        }

        $offer->update(['status' => 'accepted']);
        
        // Reject other pending offers for this listing
        Offer::where('listing_id', $offer->listing_id)
            ->where('id', '!=', $offer->id)
            ->where('status', 'pending')
            ->update(['status' => 'rejected']);

        return response()->json([
            'message' => 'Đề nghị đã được chấp nhận',
            'offer' => $offer,
        ]);
    }

    public function reject(Request $request, Offer $offer): JsonResponse
    {
        // Check if user is the seller
        if ($offer->listing->seller_id !== Auth::id()) {
            return response()->json(['message' => 'Bạn không có quyền từ chối đề nghị này'], 403);
        }

        if ($offer->status !== 'pending') {
            return response()->json(['message' => 'Đề nghị không thể từ chối'], 400);
        }

        $offer->update(['status' => 'rejected']);

        return response()->json([
            'message' => 'Đề nghị đã bị từ chối',
            'offer' => $offer,
        ]);
    }
}
