<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\RegisBisnisOwner;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
  public function userById(User $user) {
    return $this->successResponse(
      data: $user,
      message: 'User ditemukan',
      code: 200
    );
  }
}
