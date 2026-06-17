<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
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
            data: $user,
            message: 'Profile updated successfully',
            code: 200
        );
    }
}
