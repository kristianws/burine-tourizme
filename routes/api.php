<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BisnisOwnerController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ItineraryController;
use App\Http\Controllers\ItineraryItemController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * Routes API tanpa token
 */
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/destinations', [DestinationController::class, 'index']);
Route::get('/destinations/search', [DestinationController::class, 'searchReviews']);

/**
 * Routes API dengan token
 */
Route::middleware('auth:sanctum')->group(function () {
  Route::post('/logout', [AuthController::class, 'logout']);
  Route::get('/me', [UserController::class, 'me']);
  Route::patch('/me', [UserController::class, 'update']);
  Route::patch('/me/profile-picture', [UserController::class, 'updateProfilePicture']);
});

/**
 * Routes API untuk role tourist, bisnis owner, dan admin
 */
Route::prefix('tourist/')->middleware(['auth:sanctum', 'role:tourist'])->group(function () {

  Route::post('register-bisnis-owner/', [BisnisOwnerController::class, 'RegisterBisnisOwner']);
  Route::post('profile/update/', [UserController::class, 'update']);

  Route::get('reviews/{destinationId}', [ReviewController::class, 'reviewByDestinationId']);
  Route::post('reviews/new', [ReviewController::class, 'store']);
  Route::patch('reviews/update', [ReviewController::class, 'update']);

  Route::post('wishlists/add/{id}', [WishlistController::class, 'store']);
  Route::delete('wishlists/remove/{id}', [WishlistController::class, 'destroy']);
  Route::get('wishlists/', [WishlistController::class, 'show']);

  Route::get('itineraries/', [ItineraryController::class, 'index']);
  Route::post('itineraries/new', [ItineraryController::class, 'store']);
  Route::get('itineraries/{itinerary}', [ItineraryController::class, 'show']);
  Route::get('itineraries/{itinerary}/items', [ItineraryItemController::class, 'index']);
  Route::post('itineraries/{itinerary}/items', [ItineraryItemController::class, 'store']);
  Route::delete('itineraries/{itinerary}/items/{itineraryItem}', [ItineraryItemController::class, 'destroy']);
});

Route::prefix('bisnis-owner/')->middleware(['auth:sanctum', 'role:bisnis_owner'])->group(function () {

  Route::get('dashboard/', [BisnisOwnerController::class, 'dashboard']);
  Route::get('destinations/', [DestinationController::class, 'index']);
  Route::post('destinations/', [DestinationController::class, 'store']);
  Route::patch('destinations/{id}/update/', [DestinationController::class, 'update']);
  Route::patch('destinations/{id}/reply-review/', [ReviewController::class, 'replyReview']);

});

Route::prefix('admin/')->middleware(['auth:sanctum', 'role:admin'])->group(function () {
  Route::patch('bisnisOwners/{bisnisOwner}/approve', [BisnisOwnerController::class, 'approvedBisnisOwner']);
  Route::patch('bisnisOwners/{bisnisOwner}/reject', [BisnisOwnerController::class, 'rejectBisnisOwner']);
  Route::patch('destinations/{destination}/approve', [DestinationController::class, 'approved']);
  Route::patch('destinations/{destination}/reject', [DestinationController::class, 'rejected']);
  Route::patch('destinations/{destination}/delete', [DestinationController::class, 'deleted']);
  Route::patch('destinations/{destination}/pending', [DestinationController::class, 'pending']);
});
