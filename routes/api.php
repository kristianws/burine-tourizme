<?php

use App\Http\Controllers\Api\AuthController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * Routes API sebelum login  
 */
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

/**
 * Routes API setelah login
 */
Route::middleware('auth:sanctum')->group(function() {
  Route::post('/logout', [AuthController::class, 'logout']);
});


