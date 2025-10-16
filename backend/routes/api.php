<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SearchController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// rbac user api
use App\Http\Controllers\UserController;

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

// Listings routes (public)
Route::get('/listings', [ListingController::class, 'index']);
Route::get('/listings/{listing}', [ListingController::class, 'show']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    
    // User routes
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // Listings management
    Route::post('/listings', [ListingController::class, 'store']);
    Route::put('/listings/{listing}', [ListingController::class, 'update']);
    Route::delete('/listings/{listing}', [ListingController::class, 'destroy']);
    Route::get('/my-listings', [ListingController::class, 'myListings']);
    Route::post('/listings/{listing}/duplicate', [ListingController::class, 'duplicate']);
    Route::post('/listings/{listing}/toggle-status', [ListingController::class, 'toggleStatus']);
    
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
        
        // Users management
        Route::get('/users', [AdminController::class, 'users']);
        Route::post('/users/{user}/toggle-status', [AdminController::class, 'toggleUserStatus']);
    });

    
});
//rbac api user
Route::get('/user/current', [UserController::class, 'currentUser']);
