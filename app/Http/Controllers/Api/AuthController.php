<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request) 
    {
      $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required',
      ]);

      if($validator->fails()) {
        return response()->json([
          'status' => 'error',
          'message' => $validator->errors()
        ], 422);
      };

      $user = User::where('email', $request->email)->first();

      if(!$user || !Hash::check($request->password, $user->password)) {
        return response()->json([
          'status' => 'error',
          'message' => 'password salah'
        ], 401);
      };

      $token = $user->createToken('auth_token')->plainTextToken;

      return response()->json([
        'success' => true,
        'message' => 'login berhasil',
        'access_token' => $token,
        'token_type' => 'Bearer',
      ]);
    }
}
