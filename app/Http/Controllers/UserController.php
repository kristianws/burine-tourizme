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
      data: [new UserResource($request->user()->load('bisnisOwner')),
            'profile_url :' => $request->user()->profile_picture ? Storage::disk('supabase_profile')->url($request->user()->profile_picture) : null],
      message: 'User ditemukan',
      code: 200
    );
  }

  public function update(Request $request) 
  {
    $validated = $request->validate([
        'name' => ['sometimes', 'string', 'max:255'],
        'email' => ['sometimes', 'string', 'email', 'max:255', 'unique:users,email,' . $request->user()->id],
        'password' => ['sometimes', 'string', 'min:8'],
        'profile_picture' => ['sometimes', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
    ]);

    $user = $request->user();

    try {
        // Inisialisasi variabel path dengan data lama sebagai default
        $file_path = $user->profile_picture; 

        // Cek jika ada file baru yang di-upload
        if ($request->hasFile('profile_picture') && $request->file('profile_picture')->isValid()) {
            $file = $request->file('profile_picture');
            $filename = "user_id_{$user->id}_" . time() . '.' . $file->getClientOriginalExtension();
            $file_path = "profile/user_{$user->id}/{$filename}";

            // Hapus foto lama di Supabase jika ada (Gunakan 'Storage' huruf besar)
            if ($user->profile_picture && Storage::disk('supabase_profile')->exists($user->profile_picture)) {
                Storage::disk('supabase_profile')->delete($user->profile_picture);
            }

            // Upload foto baru
            Storage::disk('supabase_profile')->put($file_path, file_get_contents($file), 'public');
        }

        // Eksekusi update (Jangan timpa variabel $user dengan hasil update)
        $user->update([
            'fullname' => $validated['name'] ?? $user->fullname,
            'email' => $validated['email'] ?? $user->email,
            'password' => isset($validated['password']) ? bcrypt($validated['password']) : $user->password,
            'profile_picture' => $file_path, 
        ]);

        // Generate URL full untuk dikirim ke frontend
        $image_url = Storage::disk('supabase_profile')->url($user->profile_picture);

        return $this->successResponse(
            data: [
                'user' => new UserResource($user->load('bisnisOwner')),
                'profile_picture_url' => $image_url // Sekarang variabel ini sudah aman terdefinisi
            ],
            message: 'Profil berhasil diperbarui',
            code: 200
        );

    } catch (\Exception $e) {
        return $this->errorResponse(
            message: 'Gagal memperbarui profil: ' . $e->getMessage(),
            code: 500
        );
    }
  }
}