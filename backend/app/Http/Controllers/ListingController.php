<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreListingRequest;
use App\Http\Requests\UpdateListingRequest;
use App\Models\Listing;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Http;
use App\Services\AuditLogService;

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
            $query->where('condition', $request->condition);
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
        $sortOrder = $request->get('order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = (int)($request->get('per_page', 10));
        $listings = $query->paginate($perPage);

        return response()->json($listings);
    }

    public function show(Listing $listing): JsonResponse
    {
        // ⚙️ Load thêm seller kèm total_sales, total_revenue
        $listing->load([
            'seller:id,name,email,phone,total_sales,total_revenue,rating,total_ratings,created_at',
            'category',
            'images',
            'offers',
        ]);
        //  Tăng lượt xem
        $listing->increment('views_count');

        // Ghi log lượt xem
        $listing->views()->create([
            'user_id' => Auth::id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return response()->json($listing);
    }


    public function store(StoreListingRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $data['status'] = 'pending'; // Default status for new listings

            $listing = Auth::user()->listings()->create($data);

            // Handle image uploads with optimization
            if ($request->hasFile('images')) {
                $manager = new ImageManager(new Driver());

                foreach ($request->file('images') as $file) {
                    if (!$file) {
                        continue;
                    }

                    $ext = strtolower($file->getClientOriginalExtension());
                    $base = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeBase = preg_replace('/[^a-zA-Z0-9-_]/', '-', $base);
                    $ts = now()->format('YmdHis');
                    $dir = 'listings/' . date('Y/m/d');

                    $img = $manager->read($file->getPathname());
                    $img->scaleDown(1600);
                    $quality = in_array($ext, ['jpg', 'jpeg']) ? 80 : 90;
                    $filename = $safeBase . '-' . $ts . '.' . $ext;
                    $path = $dir . '/' . $filename;
                    $binary = $img->encodeByExtension($ext, quality: $quality);
                    Storage::disk('public')->put($path, $binary);

                    $listing->images()->create([
                        'image_path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'file_size' => strlen($binary),
                        'mime_type' => $file->getMimeType(),
                        'width' => $img->width(),
                        'height' => $img->height(),
                        'is_primary' => $listing->images()->count() === 0,
                    ]);

                    // audit log image uploaded
                    try {
                        app(AuditLogService::class)->log($listing, 'listing_image_uploaded', null, ['path' => $path]);
                    } catch (\Throwable $e) {
                    }
                }
            }

            // Log activity
            $listing->auditLogs()->create([
                'user_id' => Auth::id(),
                'action' => 'created',
                'old_values' => null,
                'new_values' => $listing->toArray(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return response()->json([
                'message' => 'Tin rao đã được tạo thành công và đang chờ duyệt',
                'listing' => $listing->load(['category', 'images']),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi tạo tin rao',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(UpdateListingRequest $request, Listing $listing): JsonResponse
    {
        try {
            // Check ownership
            if ($listing->seller_id !== Auth::id()) {
                return response()->json(['message' => 'Bạn không có quyền chỉnh sửa tin này'], 403);
            }

            // Check if listing can be edited (not approved yet or is draft)
            if ($listing->status === 'approved') {
                return response()->json(['message' => 'Tin rao đã được duyệt, không thể chỉnh sửa'], 400);
            }

            $oldValues = $listing->toArray();
            $data = $request->validated();

            // No slug handling needed for current database structure

            // Reset status to pending if content changed
            if ($listing->status === 'rejected') {
                $data['status'] = 'pending';
            }

            $listing->update($data);

            // Handle new image uploads with optimization
            if ($request->hasFile('images')) {
                foreach ($listing->images as $image) {
                    Storage::disk('public')->delete($image->image_path);
                    try {
                        app(AuditLogService::class)->log($listing, 'listing_image_deleted', ['path' => $image->image_path], null);
                    } catch (\Throwable $e) {
                    }
                    $image->delete();
                }

                foreach ($request->file('images') as $file) {
                    if (!$file) {
                        continue;
                    }
                    $ext = strtolower($file->getClientOriginalExtension());
                    $base = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeBase = preg_replace('/[^a-zA-Z0-9-_]/', '-', $base);
                    $ts = now()->format('YmdHis');
                    $dir = 'listings/' . date('Y/m/d');

                    $manager = new ImageManager(new Driver());
                    $img = $manager->read($file->getPathname());
                    $img->scaleDown(1600);
                    $quality = in_array($ext, ['jpg', 'jpeg']) ? 80 : 90;
                    $filename = $safeBase . '-' . $ts . '.' . $ext;
                    $path = $dir . '/' . $filename;
                    $binary = $img->encodeByExtension($ext, quality: $quality);
                    Storage::disk('public')->put($path, $binary);

                    $listing->images()->create([
                        'image_path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'file_size' => strlen($binary),
                        'mime_type' => $file->getMimeType(),
                        'width' => $img->width(),
                        'height' => $img->height(),
                        'is_primary' => $listing->images()->count() === 0,
                    ]);
                    try {
                        app(AuditLogService::class)->log($listing, 'listing_image_uploaded', null, ['path' => $path]);
                    } catch (\Throwable $e) {
                    }
                }
            }

            // Log activity
            $listing->auditLogs()->create([
                'user_id' => Auth::id(),
                'action' => 'updated',
                'old_values' => $oldValues,
                'new_values' => $listing->toArray(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return response()->json([
                'message' => 'Tin rao đã được cập nhật thành công',
                'listing' => $listing->load(['category', 'images']),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi cập nhật tin rao',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Listing $listing): JsonResponse
    {
        try {
            // Check ownership
            if ($listing->seller_id !== Auth::id()) {
                return response()->json(['message' => 'Bạn không có quyền xóa tin này'], 403);
            }

            // Check if listing can be deleted (not approved or has no orders)
            if ($listing->status === 'approved' && $listing->orders()->exists()) {
                return response()->json(['message' => 'Không thể xóa tin rao đã có đơn hàng'], 400);
            }

            // Load images relationship
            $listing->load('images');
            $oldValues = $listing->toArray();

            // Delete associated images
            if ($listing->images->count() > 0) {
                foreach ($listing->images as $image) {
                    \Storage::disk('public')->delete($image->image_path);
                    $image->delete();
                }
            }

            // Log activity before deletion
            try {
                $listing->auditLogs()->create([
                    'user_id' => Auth::id(),
                    'action' => 'deleted',
                    'old_values' => $oldValues,
                    'new_values' => null,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            } catch (\Exception $logError) {
                \Log::warning('Failed to create audit log for delete: ' . $logError->getMessage());
            }

            $listing->delete();

            return response()->json(['message' => 'Tin rao đã được xóa thành công']);
        } catch (\Exception $e) {
            \Log::error('Error deleting listing: ' . $e->getMessage(), [
                'listing_id' => $listing->id ?? 'unknown',
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Có lỗi xảy ra khi xóa tin rao',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function myListings(Request $request): JsonResponse
    {
        $query = Auth::user()->listings()->with(['category', 'images']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Search in title/description
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $perPage = (int)($request->get('per_page', 10));
        $listings = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json($listings);
    }

    public function duplicate(Listing $listing): JsonResponse
    {
        try {
            // Check ownership
            if ($listing->seller_id !== Auth::id()) {
                return response()->json(['message' => 'Bạn không có quyền sao chép tin này'], 403);
            }

            // CHỈ CHO PHÉP NHÂN BẢN TIN BỊ TỪ CHỐI
            if ($listing->status !== 'rejected') {
                return response()->json([
                    'message' => 'Chỉ có thể nhân bản tin rao đã bị từ chối'
                ], 403);
            }

            // GIỚI HẠN TỐI ĐA 2 LẦN NHÂN BẢN
            if ($listing->duplicate_count >= 2) {
                return response()->json([
                    'message' => 'Tin rao này đã được nhân bản tối đa 2 lần. Không thể nhân bản thêm để tránh spam.'
                ], 403);
            }

            // Load images relationship
            $listing->load('images');

            $newListing = $listing->replicate();
            $newListing->title = $listing->title . ' (Bản sao ' . ($listing->duplicate_count + 1) . ')';
            $newListing->status = 'pending';
            $newListing->views_count = 0;
            $newListing->duplicate_count = 0; // Reset counter cho bản sao mới
            $newListing->duplicate_source_id = $listing->id; // Lưu ID tin gốc
            $newListing->approved_at = null;
            $newListing->approved_by = null;
            $newListing->rejected_at = null;
            $newListing->rejected_by = null;
            $newListing->rejection_reason = null;
            $newListing->admin_notes = null;
            $newListing->save();

            // Tăng số lần nhân bản của tin gốc
            $listing->increment('duplicate_count');

            // Copy images
            if ($listing->images->count() > 0) {
                foreach ($listing->images as $image) {
                    $newPath = 'listings/' . \Str::random(40) . '.' . pathinfo($image->image_path, PATHINFO_EXTENSION);
                    \Storage::disk('public')->copy($image->image_path, $newPath);

                    $newListing->images()->create([
                        'image_path' => $newPath,
                        'is_primary' => $image->is_primary,
                    ]);
                }
            }

            // Log activity
            try {
                $newListing->auditLogs()->create([
                    'user_id' => Auth::id(),
                    'action' => 'duplicated',
                    'old_values' => null,
                    'new_values' => $newListing->toArray(),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            } catch (\Exception $logError) {
                \Log::warning('Failed to create audit log for duplicate: ' . $logError->getMessage());
            }

            return response()->json([
                'message' => 'Tin rao đã được sao chép thành công',
                'listing' => $newListing->load(['category', 'images']),
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Error duplicating listing: ' . $e->getMessage(), [
                'listing_id' => $listing->id ?? 'unknown',
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Có lỗi xảy ra khi sao chép tin rao',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function toggleStatus(Listing $listing): JsonResponse
    {
        try {
            // Check ownership
            if ($listing->seller_id !== Auth::id()) {
                return response()->json(['message' => 'Bạn không có quyền thay đổi trạng thái tin này'], 403);
            }

            $oldStatus = $listing->status;
            $newStatus = $listing->status === 'active' ? 'inactive' : 'active';

            // Only allow status change for approved listings
            if ($listing->status !== 'approved') {
                return response()->json(['message' => 'Chỉ có thể thay đổi trạng thái tin đã được duyệt'], 400);
            }

            $listing->update(['status' => $newStatus]);

            // Log activity
            $listing->auditLogs()->create([
                'user_id' => Auth::id(),
                'action' => 'status_changed',
                'old_values' => ['status' => $oldStatus],
                'new_values' => ['status' => $newStatus],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return response()->json([
                'message' => 'Trạng thái tin rao đã được cập nhật',
                'listing' => $listing,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Có lỗi xảy ra khi thay đổi trạng thái',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function related(Listing $listing): JsonResponse
    {
        try {
            $esUrl = 'http://tdc-elasticsearch:9200/listings/_search';

            // 🔍 Truy vấn Elasticsearch: tìm tin có nội dung giống + cùng category
            $response = Http::post($esUrl, [
                'size' => 8,
                'query' => [
                    'bool' => [
                        'must' => [
                            [
                                'more_like_this' => [
                                    'fields' => ['title', 'description'],
                                    'like' => [
                                        ['_id' => $listing->id]
                                    ],
                                    'min_term_freq' => 1,
                                    'max_query_terms' => 25
                                ]
                            ]
                        ],
                        'filter' => [
                            ['term' => ['category_id' => $listing->category_id]] // 🔥 cùng danh mục
                        ]
                    ]
                ],
                '_source' => ['id', 'title', 'price', 'category_id', 'status']
            ]);

            if (!$response->successful()) {
                return response()->json([
                    'message' => 'Không thể truy vấn Elasticsearch',
                    'error' => $response->body(),
                ], 500);
            }

            $hits = $response->json()['hits']['hits'] ?? [];
            $ids = collect($hits)->pluck('_source.id')->filter()->toArray();

            // 🗂️ Nếu Elasticsearch không trả về gì → fallback: lấy ngẫu nhiên trong cùng danh mục
            if (empty($ids)) {
                $relatedListings = Listing::with(['images', 'category'])
                    ->where('category_id', $listing->category_id)
                    ->where('id', '!=', $listing->id)
                    ->where('status', 'approved')
                    ->inRandomOrder()
                    ->take(8)
                    ->get();
            } else {
                $relatedListings = Listing::with(['images', 'category'])
                    ->whereIn('id', $ids)
                    ->where('status', 'approved')
                    ->get();
            }

            return response()->json($relatedListings);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi khi lấy tin rao tương tự',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function getPublicListings()
    {
        $listings = \App\Models\Listing::with(['images', 'category', 'seller'])
            ->where('status', 'approved')
            ->latest()
            ->take(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $listings,
        ]);
    }

    // Lấy listing pending của user
    public function checkPending(Request $request)
    {
        $userId = $request->user()->id;

        $pendingListings = Listing::where('seller_id', $userId)
            ->where('status', 'pending')
            ->get();

        return response()->json([
            'count' => $pendingListings->count(),
            'listings' => $pendingListings,
            'message' => $pendingListings->count()
                ? 'Bạn có tin đang chờ duyệt.'
                : 'Không có tin nào đang chờ duyệt.'
        ]);
    }
}
