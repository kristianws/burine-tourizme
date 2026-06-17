<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\StorageService;

class FileController extends Controller
{

    public function __construct(protected StorageService $storage) {}

    public function uploadProfilePicture(Request $request)
    {
        $request->validate(['file' => 'required|image|mimes:png,jpg,webp,jpeg|file|max:5120']);
        $user = $request->user();

        try {
            
            if ($user->profile_picture && $this->storage->exists($user->profile_picture)) {
                $this->storage->delete($user->profile_picture);
            }

            $file      = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $fileName  = $user->id . '_' . now()->timestamp . '.' . $extension;
            $path      = $user->id . '/' . $fileName;

            $result = Storage::disk('supabase_profile')->put(
                $path,
                file_get_contents($file),
                'public'
            );

            $user->update(['profile_picture' => $path]);

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
    public function uploadDestinationThumbnail(Request $request)
    {
        $request->validate(['file' => 'required|image|mimes:png,jpg,webp,jpeg|file|max:5120']);
        $user = $request->user();

        try {
            
            if ($user->profile_picture && $this->storage->exists($user->profile_picture)) {
                $this->storage->delete($user->profile_picture);
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
}
