<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use Illuminate\Http\Request;
use App\Http\Resources\DestinationSearchResource;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\MessagesRequest;


class DestinationController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    $destinations = Destination::all();

    return response()->json(['data' => $destinations], 200);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $validated = $request->validate([
      'bisnis_owner_id' => ['required', 'integer', 'exists:bisnis_owners,id'],
      'category_id' => ['required', 'integer', 'exists:categories,id'],
      'name' => ['required', 'string', 'max:255'],
      'gmaps' => ['required', 'string', 'max:255'],
      'location' => ['required', 'string', 'max:255'],
      'price' => ['required', 'numeric'],
      'description' => ['required', 'string'],
      'open_time' => ['required', 'date_format:H:i:s'],
      'close_time' => ['required', 'date_format:H:i:s'],
      'thumbnail' => ['nullable', 'string', 'max:255'],
    ]);

    $destination = Destination::create($validated);

    return response()->json(['data' => $destination], 201);
  }

  /**
   * Display the specified resource.
   */
  public function show(Destination $destination)
  {
    return response()->json(['data' => $destination]);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, Destination $destination)
  {
    $validated = $request->validate([
      'bisnis_owner_id' => ['sometimes', 'integer', 'exists:bisnis_owners,id'],
      'category_id' => ['sometimes', 'integer', 'exists:categories,id'],
      'name' => ['sometimes', 'string', 'max:255'],
      'gmaps' => ['sometimes', 'string', 'max:255'],
      'location' => ['sometimes', 'string', 'max:255'],
      'price' => ['sometimes', 'numeric'],
      'description' => ['sometimes', 'string'],
      'open_time' => ['sometimes', 'date_format:H:i:s'],
      'close_time' => ['sometimes', 'date_format:H:i:s'],
      'thumbnail' => ['nullable', 'string', 'max:255'],
      'status' => ['sometimes', 'in:pending,approved,rejected,deleted'],
      'moderation_notes' => ['nullable', 'string'],
      'deleted_at' => ['nullable', 'date'],
    ]);

    $destination->update($validated);

    return response()->json($destination);
  }

  /**
   * Mengambil semua data destinasi berdasarkan id tertentu.
   */
  public function getById(Destination $destination): JsonResponse
  {

    $destination = Destination::with(['category:id,name', 'bisnisOwner:id,name', ''])
      ->withAvg('reviews', 'rating')
      ->findOrFail($destination->id);

    return $this->successResponse($destination);
  }

  public function search(Request $request): JsonResponse
  {
    $searchQuery = $request->input('query');

    $destinations = Destination::query()
      ->with('category:id,name')
      ->withAvg('reviews', 'rating')
      ->when($searchQuery, function ($query, $searchQuery) {
        $query->where(function ($q) use ($searchQuery) {
          $q->where('name', 'ilike', "%{$searchQuery}%")
            ->orWhere('location', 'ilike', "%{$searchQuery}%");
        });
      })
      ->paginate(10);

    $resource = DestinationSearchResource::collection($destinations);


    $message = $resource->isEmpty() ? 'Destinasi Wisata Tidak Ditemukan' : 'Data Destinasi Wisata Ditemukan';

    return $this->successResponse($resource, $message);
  }

  public function approved(int $id): JsonResponse
  {
    $destination = Destination::findOrFail($id);
    $destination->status = 'approved';
    $destination->notes = null;
    $destination->save();

    return $this->successResponse('Destinasi Wisata Telah Disetujui');
  }

  public function rejected(int $id, MessagesRequest $request): JsonResponse
  {
    $destination = Destination::findOrFail($id);

    $validated = $request->validated();

    $destination->status = 'rejected';
    $destination->notes = $validated['notes'];
    $destination->save();

    return $this->successResponse('Destinasi Wisata Telah Ditolak');
  }

  public function deleted(int $id, MessagesRequest $request): JsonResponse
  {
    $destination = Destination::findOrFail($id);

    $validated = $request->validated();

    $destination->status = 'deleted';
    $destination->notes = $validated['notes'];
    $destination->deleted_at = now();
    $destination->save();

    return $this->successResponse('Destinasi Wisata Telah Dihapus');
  }

  public function restore(int $id): JsonResponse
  {
    $destination = Destination::findOrFail($id);

    $destination->status = 'approved';
    $destination->deleted_at = null;
    $destination->save();

    return $this->successResponse('Destinasi Wisata Telah Dipulihkan');
  }

  public function pending(int $id): JsonResponse
  {
    $destination = Destination::findOrFail($id);

    $destination->status = 'pending';
    $destination->save();

    return $this->successResponse('Destinasi Wisata Telah Dikembalikan ke Status Pending');
  }
}
