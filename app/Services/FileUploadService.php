<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{
    /**
     * Upload file to disk with consistent naming.
     */
    public static function upload(
        UploadedFile $file,
        string $directory,
        ?string $filename = null,
        string $disk = 'public'
    ): string {
        $filename = $filename ?: time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();

        return $file->storeAs($directory, $filename, $disk);
    }

    /**
     * Upload and replace old file.
     */
    public static function uploadReplace(
        UploadedFile $file,
        string $directory,
        ?string $oldPath = null,
        string $disk = 'public'
    ): string {
        // Delete old file
        if ($oldPath && self::exists($oldPath, $disk)) {
            self::delete($oldPath, $disk);
        }

        return self::upload($file, $directory, disk: $disk);
    }

    /**
     * Upload multiple files.
     */
    public static function uploadMultiple(
        array $files,
        string $directory,
        string $disk = 'public'
    ): array {
        return array_map(
            fn ($file) => self::upload($file, $directory, disk: $disk),
            $files
        );
    }

    /**
     * Check if file exists.
     */
    public static function exists(string $path, string $disk = 'public'): bool
    {
        return Storage::disk($disk)->exists($path);
    }

    /**
     * Delete file.
     */
    public static function delete(string $path, string $disk = 'public'): bool
    {
        if (self::exists($path, $disk)) {
            return Storage::disk($disk)->delete($path);
        }

        return false;
    }

    /**
     * Get URL for file.
     */
    public static function url(string $path, string $disk = 'public'): string
    {
        return Storage::disk($disk)->url($path);
    }

    // =========================================================
    // Convenience methods for specific upload types
    // =========================================================

    public static function uploadProductImage(UploadedFile $file, ?string $oldPath = null): string
    {
        return self::uploadReplace($file, 'produk_gambar', $oldPath);
    }

    public static function uploadVendorLogo(UploadedFile $file, ?string $oldPath = null): string
    {
        return self::uploadReplace($file, 'vendors_logo', $oldPath);
    }

    public static function uploadLinktreeAvatar(UploadedFile $file, ?string $oldPath = null): string
    {
        return self::uploadReplace($file, 'linktree/avatars', $oldPath);
    }

    public static function uploadLinktreeBanner(UploadedFile $file, ?string $oldPath = null): string
    {
        return self::uploadReplace($file, 'linktree/banners', $oldPath);
    }

    public static function uploadLinktreeQris(UploadedFile $file, ?string $oldPath = null): string
    {
        return self::uploadReplace($file, 'linktree/qris', $oldPath);
    }

    public static function uploadProofFile(UploadedFile $file): string
    {
        return self::upload($file, 'proofs');
    }

    public static function uploadAuctionFile(UploadedFile $file): string
    {
        return self::upload($file, 'auctions');
    }
}
