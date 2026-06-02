<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use App\Models\Destination;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'destination_id' =>
                'required|exists:destinations,id',

            'rating' =>
                'required|integer|min:1|max:5',

            'description' =>
                'required|string'
        ]);

        $exists = Review::where(
            'user_id',
            $request->user()->id
        )
        ->where(
            'destination_id',
            $validated['destination_id']
        )
        ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Anda sudah memberi review untuk destinasi ini'
            ], 422);
        }

        $destination = Destination::findOrFail(
            $validated['destination_id']
        );

        if ($destination->status !== 'approved') {
            return response()->json([
                'message' =>
                    'Destination belum tersedia untuk direview'
            ], 422);
        }

        $review = Review::create([
            'user_id' =>
                $request->user()->id,

            'destination_id' =>
                $validated['destination_id'],

            'rating' =>
                $validated['rating'],

            'description' =>
                $validated['description']
        ]);

        return response()->json([
            'message' => 'Review berhasil dibuat',
            'data' => $review
        ], 201);
    }

    public function destinationReviews($destinationId)
    {
        $reviews = Review::with(['user:id,fullname,avatar'])
            ->where(
                'destination_id',
                $destinationId
            )
            ->latest()
            ->get();

        return response()->json($reviews);
    }


}