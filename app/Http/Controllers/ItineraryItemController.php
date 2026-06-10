<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ItineraryItem;
use App\Models\Itinerary;

class ItineraryItemController extends Controller
{
  public function index(Request $request, Itinerary $itinerary)
  {
    if ($itinerary->user_id !== $request->user()->id) {
      return $this->errorResponse('Anda tidak memiliki akses ke itinerary ini', 403);
    }

    $itineraryItems = $itinerary->itineraryItems()
      ->with('destination')
      ->orderBy('day')
      ->orderBy('sequence_order')
      ->get();

    return $this->successResponse($itineraryItems, 'Daftar item itinerary berhasil diambil', 200);
  }

  public function store(Request $request, Itinerary $itinerary)
  {
    $validated = $request->validate([
      'destination_id' => 'required|exists:destinations,id',
      'day' => 'required|integer|min:1',
      'sequence_order' => 'required|integer|min:1',
      'start_time' => 'required|date_format:H:i',
      'end_time' => 'required|date_format:H:i|after:start_time',
    ]);

    if ($itinerary->user_id !== $request->user()->id) {
      return $this->errorResponse('Anda tidak memiliki akses ke itinerary ini', 403);
    }

    $itineraryItem = $itinerary->itineraryItems()->create([
      'destination_id' => $validated['destination_id'],
      'day' => $validated['day'],
      'sequence_order' => $validated['sequence_order'],
      'start_time' => $validated['start_time'],
      'end_time' => $validated['end_time'],
    ]);

    return $this->successResponse($itineraryItem, 'Item itinerary berhasil ditambahkan', 201);
  }

  public function destroy(Request $request, Itinerary $itinerary, ItineraryItem $itineraryItem)
  {
    if ($itinerary->user_id !== $request->user()->id) {
      return $this->errorResponse('Anda tidak memiliki akses ke itinerary ini', 403);
    }

    if ($itineraryItem->itinerary_id !== $itinerary->id) {
      return $this->errorResponse('Item itinerary tidak ditemukan pada itinerary ini', 404);
    }

    if ($itineraryItem->itinerary->user_id !== $request->user()->id) {
      return $this->errorResponse('Anda tidak memiliki akses ke item itinerary ini', 403);
    }

    $itineraryItem->delete();

    return $this->successResponse(null, 'Item itinerary berhasil dihapus', 200);
  }
}
