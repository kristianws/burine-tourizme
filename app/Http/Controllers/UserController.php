<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{

  public function me(Request $request) {
    return $this->successResponse(
      data: [new UserResource($request->user()->load('bisnisOwner')),
            'profile_url :' => $request->user()->profile_picture ? Storage::disk('supabase_profile')->url($request->user()->profile_picture) : null],
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
      data: $user,
      message: 'Profile updated successfully',
      code: 200
    );
  }

  public function updatePicture(Request $request)
    {
      $request->validate([
      'profile_picture' => ['required', 'image', 'mimes:jpeg,png,webp,jpg', 'max:2048'],
      ]);

      if (!$request->hasFile('profile_picture')) {
          return $this->successResponse(
              data: [],
              message: 'Tidak ada file yang diupload',
              code: 200
          );
      }

      $user = $request->user();

      // Hapus gambar lama jika ada
      if ($user->profile_picture) {
          Storage::disk('supabase_profile')->delete($user->profile_picture);
      }

      $file      = $request->file('profile_picture');
      $extension = $file->getClientOriginalExtension();
      $filename  = Str::uuid() . '.' . $extension;

      $path = Storage::disk('supabase_profile')->putFileAs(
          '/',
          $file,
          $filename,
          'public'
      );

      $user->profile_picture = ltrim($path, '/'); // bersihkan leading slash jika ada
      $user->save();

      $supabaseUrl = 'https://upvdjamlioioilqhlytv.supabase.co/storage/v1/object/public/';
      $bucketName  = 'profile';

      return $this->successResponse(
          data: ['profile_url' => $user->profile_picture
              ? $supabaseUrl . $bucketName . '/' . $user->profile_picture
              : null
          ],
          message: 'Profile picture updated successfully',
          code: 200
      );
  }
}