<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreListingRequest;
use App\Http\Requests\UpdateListingRequest;
use App\Models\Listing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ListingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Listing::with(['seller', 'category', 'images'])
            ->where('status', 'approved');

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by condition
        if ($request->has('condition')) {
            $query->where('condition_grade', $request->condition);
        }

        // Filter by price range
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $listings = $query->paginate(20);

        return response()->json($listings);
    }

    public function show(Listing $listing): JsonResponse
    {
        $listing->load(['seller.sellerProfile', 'category', 'images', 'offers']);
        
        // Increment view count
        $listing->increment('view_count');
        
        // Log view activity
        $listing->views()->create([
            'user_id' => Auth::id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return response()->json($listing);
    }

    public function store(StoreListingRequest $request): JsonResponse
    {
        $listing = Auth::user()->listings()->create($request->validated());

        return response()->json([
            'message' => 'Tin rao đã được tạo thành công',
            'listing' => $listing->load(['category', 'images']),
        ], 201);
    }

    public function update(UpdateListingRequest $request, Listing $listing): JsonResponse
    {
        // Check ownership
        if ($listing->seller_id !== Auth::id()) {
            return response()->json(['message' => 'Bạn không có quyền chỉnh sửa tin này'], 403);
        }

        $listing->update($request->validated());

        return response()->json([
            'message' => 'Tin rao đã được cập nhật thành công',
            'listing' => $listing->load(['category', 'images']),
        ]);
    }

    public function destroy(Listing $listing): JsonResponse
    {
        // Check ownership
        if ($listing->seller_id !== Auth::id()) {
            return response()->json(['message' => 'Bạn không có quyền xóa tin này'], 403);
        }

        $listing->delete();

        return response()->json(['message' => 'Tin rao đã được xóa thành công']);
    }

    public function myListings(Request $request): JsonResponse
    {
        $listings = Auth::user()->listings()
            ->with(['category', 'images'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($listings);
    }
}
