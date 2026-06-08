<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use App\ApiResponse;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
  // fungsi untuk login user
  public function login(Request $request): JsonResponse
  {
    // validasi input
    $validator = Validator::make($request->all(), [
      'identity' => 'required|string',
      'password' => 'required|string',
    ]);

    // jika validasi gagal, kembalikan response error
    if ($validator->fails()) {
      return response()->json([
        'status' => 'error',
        'message' => $validator->errors()->first()
      ], 422);
    };

    // ambil user berdasarkan email atau username
    $user = User::where('email', $request->identity)
      ->orWhere('username', $request->identity)
      ->first();

    if (!$user) {
      return $this->errorResponse('email atau username tidak ditemukan', 404);
    }

    // jika user tidak ditemukan atau password salah, kembalikan response error
    if (!Hash::check($request->password, $user->password)) {
      return $this->errorResponse('email atau password salah', 401);
    };

    // buat token akses untuk user yang berhasil login
    $token = $user->createToken('auth_token')->plainTextToken;

    // kembalikan response sukses dengan token akses
    return $this->successResponse(
      [
        'username' => $user['username'],
        'role' => $user['role'],
        'access_token' => $token,
        'token_type' => 'Bearer',
      ],
      'login berhasil',
      200
    );
  }

  // fungsi untuk registrasi user baru
  public function register(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'fullname' => 'required|string|max:255',
      'username' => 'required|string|unique:users',
      'email' => 'required|string|email|unique:users',
      'password' => 'required|string|min:6|confirmed',
      'role' => 'required|string|in:tourist,bisnis_owner',
    ]);

    if ($validator->fails()) {
      return response()->json([
        'status' => 'error',
        'message' => $validator->errors()->first()
      ], 422);
    };

    $validatedData = $validator->validated();
    $validatedData['password'] = Hash::make($validatedData['password']);

    $user = User::create($validatedData);

    return $this->successResponse([
      'id' => $user['id'],
      'username' => $user['username'],
      'role' => $user['role'],
    ], 'register berhasil', 201);
  }

  

  // fungsi untuk logout user
  public function logout(Request $request)
  {
    $request->user()->currentAccessToken()->delete();

    return $this->successResponse(null, 'logout berhasil', 200);
  }
}
