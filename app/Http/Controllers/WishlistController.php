<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Http\Requests\WishlistRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{

  /**
   * Store a newly created resource in storage.
   */
  public function store(WishlistRequest $request): JsonResponse
  {
    //
    $validated = $request->validated();

    $user = $request->user();

    $wishlist = Wishlist::firstOrCreate([
      'user_id' => $user->id,
      'destination_id' => $validated['destination_id']
    ]);

    return $this->successResponse('Destinasi Masuk ke Wishlist');
  }

  public function show(Request $request): JsonResponse
  {

    $user = $request->user();

    $wishlist = Wishlist::with('user', 'destination')
      ->where('user_id', $user->id)
      ->get();

    return $this->successResponse(
      [
        'id' => $wishlist->id,
        'user' => [
          'id' => $wishlist->user_id,
          'username' => $wishlist->user->username,
          'email' => $wishlist->user->email,
        ],
        'destination' => [
          'id' => $wishlist->destination_id,
          'name' => $wishlist->destination->name,
          'location' => $wishlist->destination->location,
          'price' => $wishlist->destination->price,
          'thumbnail' => $wishlist->destination->thumbnail,
          'rating' => $wishlist->destination->reviews()->avg('rating'),
        ],
      ]
    );
  }

  public function destroy(Wishlist $wishlist): JsonResponse
  {
    $wishlist->delete();

    return $this->successResponse('Destinasi berhasil dihapus dari wishlist');
  }
}
