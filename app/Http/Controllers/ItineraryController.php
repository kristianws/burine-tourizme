<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Itinerary;
use App\Models\ItineraryItem;

class ItineraryController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'travel_date' => 'required|date'
        ]);

        $itinerary = Itinerary::create([
            'user_id' => $request->user()->id,
            'name' => $validated['name'],
            'travel_date' => $validated['travel_date']
        ]);

        return response()->json([
            'message' => 'Itinerary berhasil dibuat',
            'data' => $itinerary
        ], 201);
    }

    public function index(Request $request)
    {
        $itineraries = Itinerary::where(
            'user_id',
            $request->user()->id
        )
        ->latest()
        ->get();

        return response()->json(
            $itineraries
        );
    }

    public function addItem(
        Request $request,
        $itineraryId
    )
    {
        $validated = $request->validate([
            'destination_id' =>
                'required|exists:destinations,id',

            'visit_order' =>
                'required|integer|min:1'
        ]);

        $itinerary = Itinerary::findOrFail(
            $itineraryId
        );

        if (
            $itinerary->user_id !==
            $request->user()->id
        ) {
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }

        $exists = ItineraryItem::where(
            'itinerary_id',
            $itinerary->id
        )
        ->where(
            'destination_id',
            $validated['destination_id']
        )
        ->exists();

        if ($exists) {
            return response()->json([
                'message' =>
                    'Destination sudah ada'
            ], 422);
        }

        $item = ItineraryItem::create([
            'itinerary_id' =>
                $itinerary->id,

            'destination_id' =>
                $validated['destination_id'],

            'visit_order' =>
                $validated['visit_order']
        ]);

        return response()->json([
            'message' =>
                'Destination berhasil ditambahkan',
            'data' => $item
        ]);
    }

    public function show(
        Request $request,
        $id
    )
    {
        $itinerary = Itinerary::with([
            'items.destination.category'
        ])
        ->where('id', $id)
        ->where(
            'user_id',
            $request->user()->id
        )
        ->firstOrFail();

        return response()->json(
            $itinerary
        );
    }

    public function removeItem(
        Request $request,
        $itemId
    )
    {
        $item = ItineraryItem::with(
            'itinerary'
        )->findOrFail($itemId);

        if (
            $item->itinerary->user_id !==
            $request->user()->id
        ) {
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }

        $item->delete();

        return response()->json([
            'message' =>
                'Destination dihapus'
        ]);
    }
}
