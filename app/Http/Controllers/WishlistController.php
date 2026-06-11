<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Http\Resources\WishlistResource;
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

    return $this->successResponse(WishlistResource::collection($wishlist), 'Wishlist berhasil diambil', 200);
  }

  public function destroy(Wishlist $wishlist): JsonResponse
  {
    $wishlist->delete();

    return $this->successResponse('Destinasi berhasil dihapus dari wishlist');
  }
}
