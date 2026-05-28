<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
  // fungsi untuk login user
  public function login(Request $request)
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

    if(!$user) {
      return response()->json(
        [
          'status' => 'error',
          'message' => 'email atau username tidak ditemukan'
        ], 404
      );
    }

    // jika user tidak ditemukan atau password salah, kembalikan response error
    if (!Hash::check($request->password, $user->password)) {
      return response()->json([
        'status' => 'error',
        'message' => 'email atau password salah'
      ], 401);
    };

    // buat token akses untuk user yang berhasil login
    $token = $user->createToken('auth_token')->plainTextToken;

    // kembalikan response sukses dengan token akses
    return response()->json([
      'success' => true,
      'message' => 'login berhasil',
      'access_token' => $token,
      'token_type' => 'Bearer',
    ], 200);
  }

  // fungsi untuk registrasi user baru
  public function register(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'full_name' => 'required|string|max:255',
      'username' => 'required|string|unique:users',
      'email' => 'required|string|email|unique:users',
      'password' => 'required|string|min:6|confirmed',
    ]);

    if ($validator->fails()) {
      return response()->json([
        'status' => 'error',
        'message' => $validator->errors()->first()
      ], 422);
    };

    $user = User::create([
      'full_name' => $request->full_name,
      'username' => $request->username,
      'email' => $request->email,
      'password' => Hash::make($request->password),
    ]);

    return response()->json([
      'success' => true,
      'message' => 'register berhasil',
      'data' => $user
    ], 201);
  }

  // fungsi untuk logout user
  public function logout(Request $request)
  {
    $request->user()->currentAccessToken()->delete();

    return response()->json([
      'success' => true,
      'message' => 'logout berhasil'
    ], 200);
  }
}
