<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Exception;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users',
            'fullname' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role' => 'required|in:tourist,mitra,admin'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        try {
          $validated['password'] = Hash::make($validated['password']);

          $user = User::create($validated);

          if(!$user) {
              throw new \Exception('Gagal membuat user');
          }

          return response()->json([
            'success' => true,
            'message' => 'Register berhasil',
            'user' => $user
          ], 201);  
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat Registrasi',
                'error' => $e->getMessage()
            ], 500);
        }

    }

    public function login(Request $request)
    {
      $validator = Validator::make($request->all(), [
        'identity'=> 'required|string',
        'password' => 'required|string',
      ]);

      if ($validator->fails()) {
        return response()->json([
          'success' => false,
          'message' => 'Validasi gagal',
          'errors' => $validator->errors()->first()
        ], 422);
      }

      try {
        $identity = $request->input('identity');
        $password = $request->input('password');

        $fieldType = filter_var($identity, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
          $fieldType => $identity,
          'password' => $password
        ];

        if (!Auth::attempt($credentials)) {
          return response()->json([
            'success' => false,
            'message' => 'Email/Username atau password salah'
          ], 401);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
          'success' => true,
          'message' => 'Login berhasil',
          'access_token' => $token,
          'bearer_token' => 'Bearer ',
          'user' => $user
        ]);

      } catch (Exception $e) {
        return response()->json([
          'success' => false,
          'message' => 'Terjadi kesalahan saat login',
          'error' => $e->getMessage()
        ], 500);
      }
    }

    public function logout(Request $request)
    {
        $request->user()
            ->currentAccessToken()  
            ->delete();

        return response()->json([
          'success' => true,
            'message' => 'Logout berhasil'
        ], 200);
    }

    public function me(Request $request)
    {
        return response()->json(
            $request->user()
        );
    }
}