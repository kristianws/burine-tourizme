<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

}
