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
Route::get('/destinations/search', [DestinationController::class, 'search']);

/**
 * Routes API dengan token
 */
Route::middleware('auth:sanctum')->group(function () {
  Route::post('/logout', [AuthController::class, 'logout']);
  Route::get('/me', [UserController::class, 'me']);
  Route::patch('/me', [UserController::class, 'update']);
  Route::patch('/me/profile-picture', [UserController::class, 'updatePicture']);
});

/**
 * Routes API untuk role tourist, bisnis owner, dan admin
 */
Route::prefix('tourist/')->middleware(['auth:sanctum', 'role:tourist'])->group(function () {

  Route::post('register-bisnis-owner/', [BisnisOwnerController::class, 'RegisterBisnisOwner']);

  Route::post('reviews', [ReviewController::class, 'store']);
  Route::get('reviews/destinations/{destination}', [ReviewController::class, 'show']);
  Route::patch('reviews/{id}', [ReviewController::class, 'update']);

  Route::post('wishlists/{id}', [WishlistController::class, 'store']);
  Route::delete('wishlists/{id}', [WishlistController::class, 'destroy']);
  Route::get('wishlists/', [WishlistController::class, 'show']);

  Route::get('itineraries/', [ItineraryController::class, 'index']); // menampilkan semua  itinerary milik user
  Route::post('itineraries/', [ItineraryController::class, 'store']); 
  Route::get('itineraries/{itinerary}', [ItineraryController::class, 'show']);
  Route::delete('itineraries/{itinerary}', [ItineraryController::class, 'destroy']);

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

  // Route untuk mengelola user


  // route untuk mengelola bisnis owner
  Route::patch('bisnisOwners/{bisnisOwner}/approve', [BisnisOwnerController::class, 'approvedBisnisOwner']);
  Route::patch('bisnisOwners/{bisnisOwner}/reject', [BisnisOwnerController::class, 'rejectBisnisOwner']);

  // Routes untuk mengelola destinasi
  Route::patch('destinations/{destination}/approve', [DestinationController::class, 'approved']);
  Route::patch('destinations/{destination}/reject', [DestinationController::class, 'rejected']);
  Route::patch('destinations/{destination}/delete', [DestinationController::class, 'deleted']);
  Route::patch('destinations/{destination}/pending', [DestinationController::class, 'pending']);
});
