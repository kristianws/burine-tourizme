<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class StorageService
{
    /**
     * Delete file dari Supabase Storage
     */
    public function delete(string $path, string $disk): bool
    {
        return Storage::disk($disk)->delete($path);
    }

    /**
     * Generate public URL
     */
    public function getPublicUrl(string $path, string $bucket): string
    {
        $baseUrl = config('services.supabase.url');

        return "{$baseUrl}/storage/v1/object/public/{$bucket}/{$path}";
    }

    /**
     * Cek apakah file exists
     */
    public function exists(string $path, string $disk): bool
    {
        return Storage::disk($disk)->exists($path);
    }
}