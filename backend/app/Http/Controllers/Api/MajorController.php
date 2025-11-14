<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Major;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MajorController extends Controller
{
    /**
     * Display a listing of active majors (public)
     */
    public function index(): JsonResponse
    {
        $majors = Major::active()
            ->ordered()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $majors
        ]);
    }

    /**
     * Store a newly created major (admin only)
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:majors,name',
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'display_order' => 'integer|min:0',
        ]);

        // Auto generate slug from name
        $validated['slug'] = Str::slug($validated['name']);

        $major = Major::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tạo ngành học thành công',
            'data' => $major
        ], 201);
    }

    /**
     * Display the specified major
     */
    public function show(Major $major): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $major->load(['users', 'listings'])
        ]);
    }

    /**
     * Update the specified major (admin only)
     */
    public function update(Request $request, Major $major): JsonResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('majors', 'name')->ignore($major->id)
            ],
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'display_order' => 'integer|min:0',
        ]);

        // Update slug if name changed
        if (isset($validated['name']) && $validated['name'] !== $major->name) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $major->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật ngành học thành công',
            'data' => $major
        ]);
    }

    /**
     * Remove the specified major (admin only)
     */
    public function destroy(Major $major): JsonResponse
    {
        // Check if major is being used
        $usersCount = $major->users()->count();
        $listingsCount = $major->listings()->count();

        if ($usersCount > 0 || $listingsCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "Không thể xóa ngành này vì có {$usersCount} người dùng và {$listingsCount} tin rao đang sử dụng"
            ], 422);
        }

        $major->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa ngành học thành công'
        ]);
    }
}
