<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ItineraryController;
use App\Http\Controllers\ReviewController;

// public routes
Route::post('/login', [
    AuthController::class,
    'login'
]);

Route::post('/register', [
    AuthController::class,
    'register'
]);

// protected routes
Route::middleware('auth:sanctum')
    ->post('/logout', [
        AuthController::class,
        'logout'
    ]);

Route::middleware('auth:sanctum')
    ->get('/me', [
        AuthController::class,
        'me'
    ]);



Route::get(
    '/destinations',
    [DestinationController::class, 'index']
);

Route::get(
    '/destinations/{id}',
    [DestinationController::class, 'show']
);

Route::middleware([
    'auth:sanctum',
    'role:mitra'
])->group(function () {

    Route::post(
        '/destinations',
        [DestinationController::class, 'store']
    );

    Route::get(
        '/my-destinations',
        [DestinationController::class, 'myDestinations']
    );

    Route::put(
    '/destinations/{id}',
    [DestinationController::class,
     'update']
    );

    Route::delete(
        '/destinations/{id}',
        [DestinationController::class,
        'destroy']
    );

});

Route::middleware([
    'auth:sanctum',
    'role:admin'
])->group(function () {

    Route::get(
        '/admin/destinations/pending',
        [DestinationController::class, 'pending']
    );

    Route::patch(
        '/admin/destinations/{id}/approve',
        [DestinationController::class, 'approve']
    );

    Route::patch(
        '/admin/destinations/{id}/reject',
        [DestinationController::class, 'reject']
    );

});

Route::middleware([
    'auth:sanctum',
    'role:tourist'
])->group(function () {

    Route::post(
        '/wishlists',
        [WishlistController::class, 'store']
    );

    Route::get(
        '/wishlists',
        [WishlistController::class, 'index']
    );

    Route::delete(
        '/wishlists/{destinationId}',
        [WishlistController::class, 'destroy']
    );
});

Route::middleware([
    'auth:sanctum'
])->group(function () {

    Route::post(
        '/comments',
        [CommentController::class, 'store']
    );

});

Route::get(
    '/reviews/{id}/comments',
    [CommentController::class, 'reviewComments']
);

Route::middleware([
    'auth:sanctum',
    'role:tourist'
])->group(function () {

    Route::post(
        '/itineraries',
        [ItineraryController::class, 'store']
    );

    Route::get(
        '/itineraries',
        [ItineraryController::class, 'index']
    );

    Route::get(
        '/itineraries/{id}',
        [ItineraryController::class, 'show']
    );

    Route::post(
        '/itineraries/{id}/items',
        [ItineraryController::class, 'addItem']
    );

    Route::delete(
        '/itinerary-items/{id}',
        [ItineraryController::class, 'removeItem']
    );
});

Route::middleware([
    'auth:sanctum',
    'role:tourist'
])->group(function () {

    Route::post(
        '/reviews',
        [ReviewController::class, 'store']
    );

});

Route::get(
    '/destinations/{id}/reviews',
    [ReviewController::class,
     'destinationReviews']
);
