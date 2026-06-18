<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Services\StorageService;
use App\Models\User;
use App\Models\BisnisOwner;
use App\Models\Destination;
use App\Models\Review;

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
            data: [
                new UserResource($user),
            ],
            message: 'Profile updated successfully',
            code: 200
        );
    }

    /**
     * Upload a new profile picture for the authenticated user.
     */
    public function uploadProfilePicture(Request $request, StorageService $storage)
    {
        $request->validate(['file' => 'required|image|mimes:png,jpg,webp,jpeg|file|max:5120']);
        $user = $request->user();

        try {

            if ($user->profile_picture && $storage->exists($user->profile_picture, 'supabase_profile')) {
                $storage->delete($user->profile_picture, 'supabase_profile');
            }

            $file      = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $fileName  = $user->id . '_' . now()->timestamp . '.' . $extension;
            $path      = $user->id . '/' . $fileName;

            $result = Storage::disk('supabase_profile')->put(
                $path,
                file_get_contents($file),
                'public'
            );

            $user->update(['profile_picture' => $path]);

            return response()->json([
                'put_result'  => $result,
                'path'        => $path,
                'file_name'   => $fileName,
                'uploaded_by' => $user->username,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function dashboard()
    {
        try {
            // Statistik User
            $totalUsers = User::count();
            $totalTourists = User::where('role', 'tourist')->count();
            $totalAdmins = User::where('role', 'admin')->count();

            // Statistik Bisnis Owner
            $totalBisnisOwners = BisnisOwner::count();
            $pendingBisnisOwners = BisnisOwner::where('status', 'pending')->count();
            $approvedBisnisOwners = BisnisOwner::where('status', 'approved')->count();
            $rejectedBisnisOwners = BisnisOwner::where('status', 'rejected')->count();

            // Statistik Destinasi
            $totalDestinations = Destination::count();
            $pendingDestinations = Destination::where('status', 'pending')->count();
            $approvedDestinations = Destination::where('status', 'approved')->count();
            $rejectedDestinations = Destination::where('status', 'rejected')->count();

            // Statistik Review
            $totalReviews = Review::count();

            // Data pending bisnis owners terbaru (untuk tabel)
            $recentPendingBisnisOwners = BisnisOwner::where('status', 'pending')
                ->with('user:id,fullname,username,email')
                ->latest()
                ->take(5)
                ->get();

            // Data pending destinasi terbaru (untuk tabel)
            $recentPendingDestinations = Destination::where('status', 'pending')
                ->with('bisnisOwner.user:id,fullname,username')
                ->latest()
                ->take(5)
                ->get();

            return $this->successResponse(
                data: [
                    'users' => [
                        'total' => $totalUsers,
                        'tourists' => $totalTourists,
                        'admins' => $totalAdmins,
                    ],
                    'bisnis_owners' => [
                        'total' => $totalBisnisOwners,
                        'pending' => $pendingBisnisOwners,
                        'approved' => $approvedBisnisOwners,
                        'rejected' => $rejectedBisnisOwners,
                    ],
                    'destinations' => [
                        'total' => $totalDestinations,
                        'pending' => $pendingDestinations,
                        'approved' => $approvedDestinations,
                        'rejected' => $rejectedDestinations,
                    ],
                    'reviews' => [
                        'total' => $totalReviews,
                    ],
                    'recent_pending_bisnis_owners' => $recentPendingBisnisOwners,
                    'recent_pending_destinations' => $recentPendingDestinations,
                ],
                message: 'Dashboard data berhasil diambil',
                code: 200
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error Internal Server: ' . $e->getMessage(), 500);
        }
    }
}
