<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Itinerary;

class ItineraryController extends Controller
{
  public function store(Request $request)
  {
    
    $validated = $request->validate([
      'title' => 'required|string|max:255',
      'start_date' => 'required|date',
      ]);
      
    $user = $request->user()->itineraries()->create($validated);

    return $this->successResponse($user, 'Itinerary berhasil dibuat', 201);
  }

  /**
   * Display a listing of the resource.
   */
  public function index(Request $request)
  {
    $itineraries = $request->user()
      ->itineraries()
      ->latest()
      ->get();

    return $this->successResponse($itineraries, 'Daftar itinerary berhasil diambil', 200);
  }

  public function show(Request $request, Itinerary $itinerary)
  {
    if ($itinerary->user_id !== $request->user()->id) {
      return $this->errorResponse('Anda tidak memiliki akses ke itinerary ini', 403);
    }

    return $this->successResponse($itinerary, 'Detail itinerary berhasil diambil', 200);
  }

  public function update(Request $request, Itinerary $itinerary)
  {
    if ($itinerary->user_id !== $request->user()->id) {
      return $this->errorResponse('Anda tidak memiliki akses ke itinerary ini', 403);
    }

    $validated = $request->validate([
      'title' => 'sometimes|required|string|max:255',
      'start_date' => 'sometimes|required|date',
    ]);

    $itinerary->update($validated);

    return $this->successResponse($itinerary, 'Itinerary berhasil diperbarui', 200);
  }

  public function destroy(Request $request, Itinerary $itinerary)
  {
    if ($itinerary->user_id !== $request->user()->id) {
      return $this->errorResponse('Anda tidak memiliki akses ke itinerary ini', 403);
    }

    $itinerary->delete();

    return $this->successResponse(null, 'Itinerary berhasil dihapus', 200);
  }
}
