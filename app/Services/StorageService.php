<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StorageService
{
    protected string $disk = 'supabase_profile';

    /**
     * Upload file ke Supabase Storage
     */
    public function upload(UploadedFile $file, string $folder = 'uploads'): array
    {
        $fileName  = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path      = "{$folder}/{$fileName}";

        Storage::disk('supabase_profile')->putFileAs('images', $file, $file->getClientOriginalName());

;

        return [
            'path' => $path,
            'url'  => $this->getPublicUrl($path),
            'name' => $fileName,
            'size' => $file->getSize(),
            'mime' => $file->getMimeType(),
        ];
    }

    /**
     * Delete file dari Supabase Storage
     */
    public function delete(string $path): bool
    {
        return Storage::disk($this->disk)->delete($path);
    }

    /**
     * Generate public URL
     */
    public function getPublicUrl(string $path): string
    {
        $baseUrl = env('SUPABASE_URL');
        $bucket  = env('AWS_BUCKET');

        return "{$baseUrl}/storage/v1/object/public/{$bucket}/{$path}";
    }

    /**
     * Cek apakah file exists
     */
    public function exists(string $path): bool
    {
        return Storage::disk($this->disk)->exists($path);
    }
}