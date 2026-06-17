<?php

declare(strict_types=1);

namespace App\Traits;

use RuntimeException;

/**
 * Reusable file-upload logic for models that have image fields.
 *
 * Usage: add `use Uploadable;` to any model that handles file uploads.
 */
trait Uploadable
{
    /**
     * Validate and move an uploaded file, returning its stored relative path.
     *
     * @param  array<string, mixed>  $fileDescriptor  Single entry from $_FILES (e.g. $request->file('product_image')).
     * @param  string                $subdirectory    Folder inside the uploads dir (e.g. 'products').
     * @param  int                   $maxSizeBytes    Maximum allowed file size in bytes.
     *
     * @return string  Relative path stored in the DB (e.g. 'products/abc123.jpg').
     *
     * @throws RuntimeException  If validation fails or the file cannot be moved.
     */
    public function uploadImage(
        array $fileDescriptor,
        string $subdirectory,
        int $maxSizeBytes = 2_097_152
    ): string {
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

        if ($fileDescriptor['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('File upload error code: ' . $fileDescriptor['error']);
        }

        if ($fileDescriptor['size'] > $maxSizeBytes) {
            throw new RuntimeException('File exceeds maximum size of ' . ($maxSizeBytes / 1024 / 1024) . ' MB.');
        }

        $detectedMime = mime_content_type($fileDescriptor['tmp_name']);

        if (!in_array($detectedMime, $allowedMimeTypes, true)) {
            throw new RuntimeException('Invalid file type. Allowed: JPEG, PNG, WebP, GIF.');
        }

        $fileExtension    = pathinfo($fileDescriptor['name'], PATHINFO_EXTENSION);
        $uniqueFileName   = bin2hex(random_bytes(16)) . '.' . strtolower($fileExtension);
        $targetDirectory  = UPLOAD_DIR . '/' . $subdirectory;

        if (!is_dir($targetDirectory)) {
            mkdir($targetDirectory, 0755, true);
        }

        $targetFilePath = $targetDirectory . '/' . $uniqueFileName;

        if (!move_uploaded_file($fileDescriptor['tmp_name'], $targetFilePath)) {
            throw new RuntimeException('Failed to move uploaded file to destination.');
        }

        return $subdirectory . '/' . $uniqueFileName;
    }

    /**
     * Delete a previously uploaded file from disk.
     *
     * @param  string|null  $relativePath  Path stored in the DB (e.g. 'products/abc123.jpg').
     *
     * @return void
     */
    public function deleteUploadedFile(?string $relativePath): void
    {
        if ($relativePath === null || $relativePath === '') {
            return;
        }

        $absolutePath = UPLOAD_DIR . '/' . $relativePath;

        if (file_exists($absolutePath)) {
            unlink($absolutePath);
        }
    }
}
