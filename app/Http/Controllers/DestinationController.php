<?php

namespace App\Http\Controllers;

use App\Http\Resources\DestinationSearchResource;
use App\Http\Resources\DestinationResource;
use App\Http\Requests\MessagesRequest;
use App\Models\Destination;
use App\Services\StorageService;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;


class DestinationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $destinations = Destination::with([
                'category',
                'bisnisOwner',
                'imageGaleries',
                'reviews',
            ])->withAvg('reviews', 'rating')
                ->paginate(6);
    
            $destinations = DestinationResource::collection($destinations);

            if ($destinations->isEmpty()) {
                return $this->errorResponse('Data Destinasi Tidak Ditemukan', 404);
            }
    
            return $this->successResponse($destinations, 'Data Destinasi Berhasil Diambil', 200);
        } catch (\Exception $e) {
            return $this->errorResponse('Error Internal Server', 500);
        }
    }

    /**
     * Display destinations belonging to the authenticated bisnis_owner.
     */
    public function myDestinations(Request $request)
    {
        try {
            $bisnisOwner = $request->user()->bisnisOwner;

            if (!$bisnisOwner) {
                return $this->errorResponse('Akun mitra tidak ditemukan', 404);
            }

            $destinations = Destination::with([
                'category',
                'imageGaleries',
                'reviews',
            ])->withAvg('reviews', 'rating')
                ->where('bisnis_owner_id', $bisnisOwner->id)
                ->latest()
                ->paginate(12);

            $result = DestinationResource::collection($destinations);

            if ($result->isEmpty()) {
                return $this->errorResponse('Belum ada destinasi', 404);
            }

            return $this->successResponse($result, 'Destinasi berhasil diambil', 200);
        } catch (\Exception $e) {
            return $this->errorResponse('Error Internal Server: ' . $e->getMessage(), 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Destination $destination): JsonResponse
    {
        try {
            $destination->load([
                'category:id,name',
                'bisnisOwner:id,name',
                'imageGaleries:id,destination_id,path',
                'reviews:id,destination_id,rating',
            ]);
    
            $resource = new DestinationResource($destination);

            if (!$resource) {
                return $this->errorResponse('Destinasi Wisata Tidak Ditemukan', 404);
            }
    
            return $this->successResponse($resource, 'Destinasi Wisata Ditemukan', 200);
        } catch (\Exception $e) {
            return $this->errorResponse('Error Internal Server', 500);
        }
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
            'thumbnail' => ['required', 'image', 'mimes:png, jpg, webp, jpeg', 'file', 'max:5120'],
        ]);

        $bisnisOwner = $request->user()->bisnisOwner->id;

        try {  
            $file = $request->file('thumbnail');
            $extensionn = $file->getClientOriginalExtension();
            $filename = $validated['name'] . '_' . time() . '.' . $extensionn;
            $path= $validated['name'] . '/' . $filename;

            $results = Storage::disk('supabase_thumbnail')->put(
                $path,
                file_get_contents($file),
                'public'
            );

            if (!$results) {
                return $this->errorResponse('Gagal mengunggah thumbnail', 500);
            }

            $validated['thumbnail'] = $path;
            $validated['category_id'] = $validated['category_name'];

            $destination = Destination::create(
                [
                    'bisnis_owner_id' => $bisnisOwner,
                    ...$validated,
                ]
            );

            if (!$destination) {
                return $this->errorResponse('Gagal membuat destinasi wisata', 500);
            }

            return $this->successResponse(
                data: $destination,
                message: 'Destinasi wisata berhasil dibuat',
                code: 201
            );

        } catch (\Exception $e) {
            return $this->errorResponse('Error Internal Server', 500);
        }
    }

    private function checkOwnership(
        Destination $destination,
        Request $request
    ) {
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
        Destination $destination
    ) {
        try {
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

            $isSuccess = $destination->update($validated);

            if (!$isSuccess) {
                return $this->errorResponse('Gagal mengupdate destinasi wisata', 500);
            }

            return $this->successResponse(
                data: $destination,
                message: 'Destinasi wisata berhasil diupdate',
                code: 200
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error Internal Server', 500);
        }
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

        if ($resource->isEmpty()) {
            return $this->errorResponse($message, 404);
        }

        return $this->successResponse($resource, $message);
    }

    public function approved(Destination $destination): JsonResponse
    {
        $destination->status = 'approved';
        $destination->notes = null;
        $destination->save();

        return $this->successResponse('Destinasi Wisata Telah Disetujui');
    }

    public function rejected(Destination $destination, MessagesRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $destination->status = 'rejected';
        $destination->notes = $validated['notes'];
        $destination->save();

        return $this->successResponse('Destinasi Wisata Telah Ditolak');
    }

    public function deleted(Destination $destination, MessagesRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $destination->status = 'deleted';
        $destination->notes = $validated['notes'];
        $destination->deleted_at = now();
        $destination->save();

        return $this->successResponse('Destinasi Wisata Telah Dihapus');
    }

    public function pending(Destination $destination): JsonResponse
    {
        $destination->status = 'pending';
        $destination->save();

        return $this->successResponse('Destinasi Wisata Telah Dikembalikan ke Status Pending');
    }

    public function uploadDestinationThumbnail(Request $request, Destination $destination, StorageService $storage): JsonResponse
    {
        $request->validate(['file' => 'required|image|mimes:png,jpg,webp,jpeg|file|max:5120']);

        try {
            if ($destination->thumbnail && $storage->exists($destination->thumbnail, 'supabase_thumbnail')) {
                $storage->delete($destination->thumbnail, 'supabase_thumbnail');
            }

            $file      = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $fileName  = $destination->name . '_' . now()->timestamp . '.' . $extension;
            $path      = $destination->name . '/' . $fileName;

            Storage::disk('supabase_thumbnail')->put(
                $path,
                file_get_contents($file),
                'public'
            );

            $destination->update(['thumbnail' => $path]);

            return response()->json([
                'path'        => $path,
                'file_name'   => $fileName,
                'uploaded_by' => $destination->bisnisOwner->username,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    // public function getImages(Destination $destination): JsonResponse
    // {
    //     $images = $destination->imageGaleries()->get

    //     return $this->successResponse($images, 'Gambar Destinasi Wisata Ditemukan', 200);
    // }
}
