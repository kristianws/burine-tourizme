<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BisnisOwnerController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\ReviewController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * Routes API tanpa token
 */
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/destinations/search', [DestinationController::class, 'searchReviews']);


/**
 * Routes API dengan token
 */
Route::middleware('auth:sanctum')->group(function() {
  Route::post('/logout', [AuthController::class, 'logout']);
  Route::post('/auth/register-bisnis-owner', [AuthController::class, 'registerBisnisOwner']);
});

Route::prefix('tourist/')->middleware(['auth:sanctum', 'role:tourist'])->group(function() {

  Route::post('register-bisnis-owner/', [BisnisOwnerController::class, 'RegisterBisnisOwner']);

  Route::get('destinations/reviews/{destinationId}', [ReviewController::class, 'reviewByDestinationId']);
  
  Route::post('reviews/{id}', [ReviewController::class, 'store']);
  
  Route::patch('reviews/update', [ReviewController::class, 'update']);
  
  // id destinassi yang akan dimasukkan ke wishlist
  Route::post('wishlists/add/{id}', [WishlistController::class, 'store']);

  // id wishlist yang akan dihapus dari wishlist
  Route::delete('wishlists/remove/{id}', [WishlistController::class, 'destroy']);

  // get wishlist berdasarkan user id yang login sekarang
  Route::get('wishlists/', [WishlistController::class, 'show']);

});

Route::prefix('bisnis-owner/')->middleware(['auth:sanctum', 'role:bisnis_owner'])->group(function() {

  Route::get('destinations/', [DestinationController::class, 'index']);

  Route::post('destinations/new', [DestinationController::class, 'store']);

  Route::patch('destinations/{id}/update/', [DestinationController::class, 'update']);

  Route::patch('destinations/{id}/reply-review/', [ReviewController::class, 'replyReview']);

  Route::post('upload', [DestinationController::class, 'upload']);

});

Route::prefix('admin/')->middleware(['auth:sanctum', 'role:admin'])->group(function() {
    Route::patch('approve-bisnis-owner/{bisnisOwner}', [BisnisOwnerController::class, 'approvedBisnisOwner']);

    Route::patch('reject-bisnis-owner/{bisnisOwner}', [BisnisOwnerController::class, 'rejectBisnisOwner']);

    Route::patch('destinations/{id}/approve', [DestinationController::class, 'approved']);

    Route::patch('destinations/{id}/reject', [DestinationController::class, 'rejected']);

    Route::patch('destinations/{id}/delete', [DestinationController::class, 'deleted']);

    Route::patch('destinations/{id}/pending', [DestinationController::class, 'pending']);


});