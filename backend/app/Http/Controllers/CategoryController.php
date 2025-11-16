<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();

        return response()->json($categories);
    }

    public function show(Category $category): JsonResponse
    {
        return response()->json($category);
    }

    // Admin methods
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'icon' => 'nullable|string|max:10',
            'is_active' => 'boolean',
        ]);

        // Auto-generate slug from name
        $validated['slug'] = Str::slug($validated['name']);

        $category = Category::create($validated);

        return response()->json($category, 201);
    }

    public function update(Request $request, Category $category): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'icon' => 'nullable|string|max:10',
            'is_active' => 'boolean',
        ]);

        // Update slug if name changed
        if (isset($validated['name']) && $validated['name'] !== $category->name) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $category->update($validated);

        return response()->json($category);
    }

    public function destroy(Category $category): JsonResponse
    {
        // Check if category has any listings
        if ($category->listings()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete category with existing listings'
            ], 422);
        }

        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully'
        ]);
    }
}