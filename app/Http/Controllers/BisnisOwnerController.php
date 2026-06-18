<?php

namespace App\Http\Controllers;

use App\Models\BisnisOwner;
use App\Http\Requests\RegisBisnisOwner;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class BisnisOwnerController extends Controller
{

    // fungsi untuk menyetujui bisnis owner
    public function approvedBisnisOwner(BisnisOwner $bisnisOwner)
    {
        if ($bisnisOwner->status !== 'pending') {
            return $this->successResponse('Bisnis owner sudah diproses sebelumnya', 400);
        }

        $bisnisOwner->update([
            'status' => 'approved',
            'verification_status' => true,
            'verification_at' => now(),
            'verification_notes' => null
        ]);

        return $this->successResponse(
            $bisnisOwner,
            'Bisnis owner berhasil disetujui',
            200
        );
    }

    // fungsi untuk menolak bisnis owner
    public function rejectBisnisOwner(BisnisOwner $bisnisOwner, Request $request)
    {
        if ($bisnisOwner->status !== 'pending') {
            return $this->successResponse('Bisnis owner sudah diproses sebelumnya', 400);
        }

        $request->validate([
            'verification_notes' => 'required|string'
        ]);

        $bisnisOwner->update([
            'status' => 'rejected',
            'verification_status' => false,
            'verification_at' => now(),
            'verification_notes' => $request->verification_notes
        ]);

        return $this->successResponse(
            $bisnisOwner,
            'Bisnis owner ditolak',
            200
        );
    }

    // fungsi untuk register bisnis owner
    public function RegisterBisnisOwner(RegisBisnisOwner $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('ktp_photo')) {
            $ktpPhoto = $request->file('ktp_photo')->store('uploads/ktp_images', 'public');

            $url = Storage::url($ktpPhoto);

            $validated['ktp_photo'] = $url;
            $validated['user_id']   = $request->user()->id; // always use authenticated user

            $bisnisOwner = BisnisOwner::create($validated);

            return $this->successResponse(
                data: $bisnisOwner,
                message: 'Bisnis owner registered successfully',
                code: 201
            );
        }

        return $this->errorResponse(
            message: 'Gagal Upload Foto KTP',
            code: 500
        );
    }
}
