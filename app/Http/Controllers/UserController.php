<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Services\StorageService;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{

    public function me(Request $request)
    {
        return $this->successResponse(
            data: [
                new UserResource($request->user()->load('bisnisOwner')),
            ],
            message: 'User ditemukan',
            code: 200
        );
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'username' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255', 'unique:users,email,' . $request->user()->id],
            'password' => ['sometimes', 'string', 'min:8'],
        ]);

        $user = $request->user();
        $user->username = $validated['username'] ?? $user->username;
        $user->email = $validated['email'] ?? $user->email;
        if (isset($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }
        $user->save();

        return $this->successResponse(
            data: [
                new UserResource($user),
            ],
            message: 'Profile updated successfully',
            code: 200
        );
    }

    /**
     * Upload a new profile picture for the authenticated user.
     */
    public function uploadProfilePicture(Request $request, StorageService $storage)
    {
        $request->validate(['file' => 'required|image|mimes:png,jpg,webp,jpeg|file|max:5120']);
        $user = $request->user();

        try {

            if ($user->profile_picture && $storage->exists($user->profile_picture, 'supabase_profile')) {
                $storage->delete($user->profile_picture, 'supabase_profile');
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
    public function dashboard() {
        
    }
}
