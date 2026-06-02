<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use Illuminate\Http\Request;
use App\Models\DestinationImage;
class DestinationController extends Controller
{

   public function index()
    {
        $destinations = Destination::with([
            'mitra',
            'category',
            'images'
        ])
        ->withAvg('reviews', 'rating')
        ->withCount('reviews')
        ->where('status', 'approved')
        ->get();

        return response()->json($destinations);
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'business_license_number' => 'required|string|max:255',
            'open_time' => 'required',
            'close_time' => 'required',
            'thumbnail' => 'required|image|max:4096'
        ]);

        $thumbnailPath = $request
        ->file('thumbnail')
        ->store('destinations/thumbnails', 'public');

        $destination = Destination::create([
            ...$validated,

            'thumbnail' => $thumbnailPath,

            'mitra_id' => $request->user()->id,

            'status' => 'pending'
        ]);

        return response()->json([
            'message' => 'Destination berhasil dibuat',
            'data' => $destination
        ], 201);
    }

   public function show($id)
    {
        $destination = Destination::with([
            'mitra',
            'category',
            'images',
            'reviews.user'
        ])
        ->withAvg('reviews', 'rating')
        ->withCount('reviews')
        ->where('id', $id)
        ->where('status', 'approved')
        ->firstOrFail();

        return response()->json($destination);
    }

    public function myDestinations(Request $request)
    {
        $destinations = Destination::with([
            'category',
            'images'
        ])
        ->withAvg('reviews', 'rating')
        ->withCount('reviews')
        ->where(
            'mitra_id',
            $request->user()->id
        )
        ->get();

        return response()->json($destinations);
    }

    public function pending()
    {
        $destinations = Destination::with([
            'mitra',
            'category'
        ])
        ->where('status', 'pending')
        ->get();

        return response()->json($destinations);
    }

    public function approve($id)
    {
        $destination = Destination::findOrFail($id);

        $destination->update([
            'status' => 'approved',
            'approved_at' => now()
        ]);

        return response()->json([
            'message' => 'Destination approved'
        ]);
    }

    public function reject($id)
    {
        $destination = Destination::findOrFail($id);

        $destination->update([
            'status' => 'rejected'
        ]);

        return response()->json([
            'message' => 'Destination rejected'
        ]);
    }

    public function uploadImages(
        Request $request,
        $destinationId
    )
    {
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|max:4096'
        ]);

        $destination = Destination::findOrFail(
            $destinationId
        );

        $this->checkOwnership(
            $destination,
            $request
        );

        foreach (
            $request->file('images')
            as $image
        ) {

            $path = $image->store(
                'destinations/gallery',
                'public'
            );

            DestinationImage::create([
                'destination_id' =>
                    $destination->id,

                'image_url' =>
                    $path
            ]);
        }

        return response()->json([
            'message' =>
                'Gallery berhasil ditambahkan'
        ]);
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

    public function update(
        Request $request,
        $id
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

            'business_license_number' =>
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

    public function destroy(
        Request $request,
        $id
    )
    {
        $destination =
            Destination::findOrFail($id);

        $this->checkOwnership(
            $destination,
            $request
        );

        $destination->delete();

        return response()->json([
            'message' =>
                'Destination berhasil dihapus'
        ]);
    }
    
}