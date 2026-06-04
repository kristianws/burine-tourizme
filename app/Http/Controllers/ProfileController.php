<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\DestinationImage;
use App\Models\Destination;

class ProfileController extends Controller

{
  public function uploadAvatar(Request $request)
  {
    $request->validate([
      'avatar' => 'required|image|max:2048'
    ]);

    $path = $request
      ->file('avatar')
      ->store('avatars', 'public');

    $request->user()->update([
      'avatar' => $path
    ]);

    return response()->json([
      'message' => 'Avatar berhasil diupload',
      'path' => $path
    ]);
  }

  public function uploadKtp(Request $request)
  {
    $request->validate([
      'ktp' => 'required|image|max:4096'
    ]);

    $path = $request
      ->file('ktp')
      ->store('ktp', 'public');

    $request->user()->update([
      'ktp_image' => $path
    ]);

    return response()->json([
      'message' => 'KTP berhasil diupload'
    ]);
  }

  public function update(Request $request)
  {
    $user = Auth::user();

    $rules = [
      'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
      'fullname' => ['required', 'string', 'max:255'],
      'email'    => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
      'nik'      => ['required', 'string', 'size:16', Rule::unique('users')->ignore($user->id)],

      // Validasi file: pastikan gambar dan ukurannya masuk akal (maks 2MB)
      'avatar'    => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
      'ktp_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],

      // Password bersifat opsional. Jika diisi, harus minimal 8 karakter dan terkonfirmasi
      // Artinya di frontend harus ada input "password_confirmation"
      'password' => ['nullable', 'string', 'min:8', 'confirmed'],
    ];

    $validatedData = $request->validate($rules);
    
    if($request->filled('password')) {
      $validatedData['password'] = Hash::make($validatedData['password']);
    } else {
      unset($validatedData['password']);
    }
  }
}
