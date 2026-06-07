<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Http\Requests\WishlistRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
      
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(WishlistRequest $request) : JsonResponse
    {
        //
        $validated = $request->validated();

        $wishlist = Wishlist::firstOrCreate([
          'user_id' => $validated['user_id'],
          'destination_id' => $validated['destination_id']
        ]); 

        return $this->successResponse('Destinasi Masuk ke Wishlist');
    }

    /**
     * Display the specified resource.
     */
    public function show(Wishlist $wishlist)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Wishlist $wishlist)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Wishlist $wishlist)
    {
        //
    }
}
