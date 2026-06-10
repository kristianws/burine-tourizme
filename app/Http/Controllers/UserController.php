<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
  public function me(Request $request) {
    return $this->successResponse(
      data: new UserResource($request->user()->load('bisnisOwner')),
      message: 'User ditemukan',
      code: 200
    );
  }

  public function update(Request $request) {
    $validated = $request->validate([
      'name' => ['sometimes', 'string', 'max:255'],
      'email' => ['sometimes', 'string', 'email', 'max:255', 'unique:users,email,' . $request->user()->id],
      'password' => ['sometimes', 'string', 'min:8'],
      'profile_picture' => ['sometimes', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
    ]);

    $user = $request->user();

    try {
      $file = $request->file('profile_picture');
      $filename = "user_id_{$user->id}_" . time() . '.' . $file->getClientOriginalExtension();
      $folder_path = "profile/{$user->id}";

      if($user->profile_picture && storage::disk('supabase_profile')->exists($user->profile_picture)) {
        storage::disk('supabase_profile')->delete($user->profile_picture);
      }

      $path = $file->storeAs($folder_path, $filename, 'supabase_profile');
      $user = $user->update([
        'fullname' => $validated['name'] ?? $user->fullname,
        'email' => $validated['email'] ?? $user->email,
        'password' => $validated['password'] ?? $user->password,
        'profile_picture' => $folder_path . '/' . $filename,
      ]);

      $image_url = Storage::disk('supabase_profile')->url($path);

      return $this->successResponse(
        data: [
          'user' => new UserResource($user->load('bisnisOwner')),
          'profile_picture_url' => $image_url
        ],
        message: 'Profil berhasil diperbarui',
        code: 200
      );
    }
    catch (\Exception $e) {
      return $this->errorResponse(
        message: 'Gagal memperbarui profil: ' . $e->getMessage(),
        code: 500
      );
    }
  }
}