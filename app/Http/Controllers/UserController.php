<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BisnisOwner;
use App\Http\Requests\RegisBisnisOwner;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
  public function RegisterBisnisOwner(RegisBisnisOwner $request)
  {
    $validated = $request->validated();

    if ($request->hasFile('ktp_photo')) {
      $ktpPhoto = $request->file('ktp_photo')->store('uploads/ktp_images', 'public');

      $url = Storage::url($ktpPhoto);

      $validated['ktp_photo'] = $url;

      $bisnisOwner = BisnisOwner::create($validated);

      return $this->successResponse(
        data: $bisnisOwner,
        message: 'Bisnis owner registered successfully',
        code: 201
      );
    }

    return $this->errorResponse(
      message: 'Wajib Upload Foto KTP',
      code: 400
    );
  }
}
