<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use Illuminate\Http\Request;
use App\Http\Resources\DestinationSearchResource;
use App\Http\Resources\DestinationResource;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\MessagesRequest;
use Illuminate\Support\Facades\Storage;


class DestinationController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    $destinations = Destination::with([
      'category',
      'bisnisOwner',
      'imageGaleries',
      'reviews',
    ])->withAvg('reviews', 'rating')
    ->paginate(6);

    $destinations = DestinationResource::collection($destinations);

    return $this->successResponse([$destinations], 'Data Destinasi Wisata Ditemukan', 200);
  }

  public function show(Destination $destination): JsonResponse
  {
    $destination->load([
      'category:id,name',
      'bisnisOwner:id,name',
      'imageGaleries:id,destination_id,path',
      'reviews:id,destination_id,rating',
    ]);

    $resource = new DestinationResource($destination);

    return $this->successResponse($resource, 'Data Destinasi Wisata Ditemukan', 200);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $validated = $request->validate([
      'category_name' => ['required', 'integer', 'exists:categories,id,name'],
      'name' => ['required', 'string', 'max:255'],
      'gmaps' => ['required', 'string', 'max:255'],
      'location' => ['required', 'string', 'max:255'],
      'price' => ['required', 'numeric'],
      'description' => ['required', 'string'],
      'open_time' => ['required', 'date_format:H:i:s'],
      'close_time' => ['required', 'date_format:H:i:s'],
      'thumbnail' => ['required', 'string', 'max:255', 'mimes:jpg,jpeg,png'],
    ]);

    $bisnisOwner = $request->user()->bisnisOwner->bisnis_owner_id;

    if($request->hasFile('thumbnail')) {
      $file = $request->file('thumbnail');

      $filename = time() . '_' . $file->getClientOriginalName();
      $folderPath = 'thumbnail/destinations';

      $path = Storage::disk('supabase_thumbnail')->putFileAs($folderPath, $file, $filename, 'public');

      $thumbnailPath = Storage::url($path);

      $validated['thumbnail'] = $thumbnailPath;

      $destination = Destination::create(
        [
          'bisnis_owner_id' => $bisnisOwner,
          ...$validated,
        ]
        );

      return $this->successResponse(
        data: $destination,
        message: 'Destinasi wisata berhasil dibuat',
        code: 201
      );
    }

    return $this->errorResponse(
      message: 'gagal mengunggah thumbnail',
      code: 500
    );
  }

  private function checkOwnership(
        Destination $destination,
        Request $request
    )
    {
        if (
            $destination->mitra_id !==
            $request->user()->id
        ) {
            abort(403, 'Forbidden');
        }
    }

  /**
   * Update the specified resource in storage.
   */
  public function update(
        Request $request,
        int $id
    )
    {
        $destination =
            Destination::findOrFail($id);

        $this->checkOwnership(
            $destination,
            $request
        );

        $validated = $request->validate([
            'category_id' =>
                'required|exists:categories,id',

            'name' =>
                'required|string|max:255',

            'description' =>
                'required|string',

            'location' =>
                'required|string|max:255',

            'open_time' =>
                'required',

            'close_time' =>
                'required'
        ]);

        $destination->update($validated);

        return response()->json([
            'message' =>
                'Destination berhasil diupdate',
            'data' =>
                $destination->fresh()
        ]);
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
      ->paginate(6);

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

  public function pending(int $id): JsonResponse
  {
    $destination = Destination::findOrFail($id);

    $destination->status = 'pending';
    $destination->save();

    return $this->successResponse('Destinasi Wisata Telah Dikembalikan ke Status Pending');
  }

}
