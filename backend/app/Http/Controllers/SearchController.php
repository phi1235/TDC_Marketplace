<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q');
        $filters = $request->get('filters', []);

        if (empty($query)) {
            return response()->json([
                'message' => 'Vui lòng nhập từ khóa tìm kiếm',
            ], 400);
        }

        $listings = Listing::search($query)
            ->where('status', 'approved')
            ->with(['seller', 'category', 'images']);

        // Apply filters
        if (isset($filters['category_id'])) {
            $listings->where('category_id', $filters['category_id']);
        }

        if (isset($filters['condition'])) {
            $listings->where('condition_grade', $filters['condition']);
        }

        if (isset($filters['min_price'])) {
            $listings->where('price', '>=', $filters['min_price']);
        }

        if (isset($filters['max_price'])) {
            $listings->where('price', '<=', $filters['max_price']);
        }

        $results = $listings->paginate(20);

        return response()->json($results);
    }

    public function suggestions(Request $request): JsonResponse
    {
        $query = $request->get('q');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $suggestions = Listing::where('status', 'approved')
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->select('title', 'id')
            ->limit(10)
            ->get();

        return response()->json($suggestions);
    }
}
