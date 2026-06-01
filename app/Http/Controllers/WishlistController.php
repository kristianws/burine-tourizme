<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'destination_id' => 'required|exists:destinations,id'
        ]);

        $wishlist = Wishlist::firstOrCreate([
            'user_id' => $request->user()->id,
            'destination_id' => $validated['destination_id']
        ]);

        return response()->json([
            'message' => 'Destination ditambahkan ke wishlist',
            'data' => $wishlist
        ]);
    }

    public function index(Request $request)
    {
        $wishlists = Wishlist::with([
            'destination.category',
            'destination.mitra'
        ])
        ->where(
            'user_id',
            $request->user()->id
        )
        ->get();

        return response()->json($wishlists);
    }

    public function destroy($destinationId, Request $request)
    {
        Wishlist::where(
            'user_id',
            $request->user()->id
        )
        ->where(
            'destination_id',
            $destinationId
        )
        ->delete();

        return response()->json([
            'message' => 'Wishlist dihapus'
        ]);
    }
}