<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Services\StorageService;
use App\Models\Destination;
use App\Models\ImageGalery;

class FileController extends Controller
{

    public function __construct(protected StorageService $storage) {}

    
    public function uploadDestinationThumbnail(Request $request)
    {
        $request->validate(['file' => 'required|image|mimes:png,jpg,webp,jpeg|file|max:5120']);
        $user = $request->user();

        try {

            if ($user->profile_picture && $this->storage->exists($user->profile_picture, 'supabase_thumbnail')) {
                $this->storage->delete($user->profile_picture, 'supabase_thumbnail');
            }

            $file      = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $fileName  = $user->bisnis_owner->destination->name . '_' . now()->timestamp . '.' . $extension;
            $path      = $user->bisnis_owner->destination->name . '/' . $fileName;

            $result = Storage::disk('supabase_thumbnail')->put(
                $path,
                file_get_contents($file),
                'public'
            );

            $user->bisnis_owner->destination->update(['thumbnail' => $path]);

            return response()->json([
                'put_result'  => $result,
                'path'        => $path,
                'file_name'   => $fileName,
                'uploaded_by' => $user->username,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function uploadDestinationImage(Destination $destination, Request $request)
    {
        $request->validate(
            ['file' => 'required|image|mimes:png,jpg,webp,jpeg|file|max:5120']
        );

        if ($destination->bisnis_owner_id !== $request->user()->bisnisOwner->id) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        try {
            $file      = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $fileName  = now()->timestamp . '.' . $extension;
            $path      = $destination->name . '/' . $fileName;

            $result = Storage::disk('supabase_image_galery')->put(        
                $path,
                file_get_contents($file),
                'public'
            );

            ImageGalery::create([
                'destination_id' => $destination->id,
                'path'           => $path,
            ]);

            return response()->json([
                'path'        => $path,
                'file_name'   => $fileName,
                'uploaded_by' => $destination->bisnisOwner->username,
                'url'         => $this->storage->getPublicUrl($path, 'destination_images'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
