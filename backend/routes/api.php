<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\ReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// rbac user api
use App\Http\Controllers\UserController;
// FollowSeller
use App\Http\Controllers\FollowSellerController;
//SellerProfile
use App\Models\SellerProfile;
//payment
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ElasticSearchController;
use App\Http\Controllers\SolrController;
use App\Http\Controllers\RatingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


// Public routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Search routes (public)
Route::get('/search', [SearchController::class, 'search']);
Route::get('/search/suggestions', [SearchController::class, 'suggestions']);
Route::get('/search-es', [ElasticSearchController::class, 'index']);
Route::get('/search-es/suggest', [ElasticSearchController::class, 'suggestions']);
Route::delete('/search-es/history/clear', [ElasticSearchController::class, 'clearHistory']);
Route::get('/search-es/history', [ElasticSearchController::class, 'history']);
Route::get('/search-solr', [SolrController::class, 'index']);
Route::get('/search-compare', [CompareController::class, 'index']);

// Listings routes (public)
Route::get('/listings/latest', [ListingController::class, 'latest']);
Route::get('/listings', [ListingController::class, 'index']);
Route::get('/listings/{listing}', [ListingController::class, 'show']);
Route::get('/listings/{listing}/related', [ListingController::class, 'related']);
Route::get('/public-listings', [ListingController::class, 'getPublicListings']);
// Categories routes (public)
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/orders', [OrderController::class, 'store']); // Buyer tạo đơn
    Route::post('/orders/{id}/confirm', [OrderController::class, 'confirm']); // Seller xác nhận
    Route::get('/orders/my', [OrderController::class, 'myOrders']); // Buyer xem
    Route::get('/orders/received', [OrderController::class, 'receivedOrders']); // Seller xem
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::post('/orders/{id}/escrow-pay', [OrderController::class, 'payWithEscrow']);
    //  Seller giao hàng
    Route::post('/orders/{id}/ship', [OrderController::class, 'markShipped']);
    //  Buyer xác nhận đã nhận hàng
    Route::post('/orders/{id}/deliver', [OrderController::class, 'markDelivered']);
    // Buyer hoàn tất đơn hàng (giải phóng escrow)
    Route::post('/orders/{id}/complete', [OrderController::class, 'completeOrder']);
    Route::post('/ratings', [RatingController::class, 'store']);
});
Route::get('/ratings/user/{userId}', [RatingController::class, 'userRatings']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // User routes
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('/my-activities', [UserController::class, 'myActivities']);

    // Listings management
    Route::post('/listings', [ListingController::class, 'store']);
    Route::put('/listings/{listing}', [ListingController::class, 'update']);
    Route::delete('/listings/{listing}', [ListingController::class, 'destroy']);
    Route::get('/my-listings', [ListingController::class, 'myListings']);
    Route::post('/listings/{listing}/duplicate', [ListingController::class, 'duplicate']);
    Route::post('/listings/{listing}/toggle-status', [ListingController::class, 'toggleStatus']);
    //New: Recommended / related listings

    // Media upload
    Route::post('/media/upload', [UploadController::class, 'upload']);

    // Wishlist routes
    Route::get('/wishlists', [WishlistController::class, 'index']);
    Route::post('/wishlists/{listing}/toggle', [WishlistController::class, 'toggle']);
    Route::get('/wishlists/{listing}/check', [WishlistController::class, 'check']);

    // Offer routes
    Route::post('/listings/{listing}/offers', [OfferController::class, 'store']);
    Route::get('/offers', [OfferController::class, 'index']);
    Route::get('/offers/received', [OfferController::class, 'receivedOffers']);
    Route::post('/offers/{offer}/accept', [OfferController::class, 'accept']);
    Route::post('/offers/{offer}/reject', [OfferController::class, 'reject']);

    // Report routes
    Route::post('/reports', [ReportController::class, 'store']);
    Route::get('/reports', [ReportController::class, 'index']);
    Route::get('/reports/{report}', [ReportController::class, 'show']);
    Route::get('/reports-stats', [ReportController::class, 'stats']);
    Route::get('/report-reasons', [ReportController::class, 'getReportReasons']);
    Route::get('/reportable-types', [ReportController::class, 'getReportableTypes']);

    // Admin routes
    Route::prefix('admin')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard']);

        // Listings management
        Route::get('/listings', [AdminController::class, 'allListings']);
        Route::get('/listings/pending', [AdminController::class, 'pendingListings']);
        Route::get('/listings/stats', [AdminController::class, 'listingStats']);
        Route::post('/listings/bulk-action', [AdminController::class, 'bulkAction']);
        Route::post('/listings/{listing}/approve', [AdminController::class, 'approveListing']);
        Route::post('/listings/{listing}/reject', [AdminController::class, 'rejectListing']);

        // Reports management
        Route::get('/reports', [AdminController::class, 'reports']);
        Route::post('/reports/{report}/handle', [AdminController::class, 'handleReport']);

        // Audit logs
        Route::get('/audit-logs', [AdminController::class, 'auditLogs']);

        // Users management
        Route::get('/users', [AdminController::class, 'users']);
        Route::post('/users/{user}/toggle-status', [AdminController::class, 'toggleUserStatus']);
    });
});
//rbac api user
Route::get('/user/current', [UserController::class, 'currentUser']);
Route::get('/users', [UserController::class, 'allUsers']);
//search dashboard
Route::get('/users/search', [UserController::class, 'search']);


// create api test role user_error
Route::get('/auth/current-role', function (Request $request) {
    // Giả lập user hiện tại
    return response()->json([
        'id' => 2,
        'name' => 'Nguyễn Văn A',
        'email' => 'nguyenvana@tdc.edu.vn',
        'role' => 'user',
    ]);
});

//list_wish
// Route::get('/wishes', [WishlistController::class, 'index']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/wishes', [WishlistController::class, 'index']);
    Route::delete('/wishlists/remove-by-listing/{listingId}', [WishlistController::class, 'removeByListing']);
});
// API toggle wishlist
Route::middleware('auth:sanctum')->post('/wishlist/toggle', [WishlistController::class, 'toggle']);


//follow_sellers
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/follow-sellers', [FollowSellerController::class, 'follow']);
    Route::delete('/follow-sellers/{seller}', [FollowSellerController::class, 'unfollow']);
    Route::get('/follow-sellers/{seller}/status', [FollowSellerController::class, 'status']);
});



// Test local không cần login
// Route::post('/follow-toggle', [FollowSellerController::class, 'toggle']);
// Route::get('/follow-status/{sellerId}', [FollowSellerController::class, 'status']);


//SellerProfile
Route::get('/sellers', function () {
    return SellerProfile::all();
});
