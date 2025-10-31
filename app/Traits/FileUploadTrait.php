<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait FileUploadTrait
{
    /**
     * Handle file upload.
     *
     * @param UploadedFile $file
     * @param string $folder
     * @param string|null $oldFile
     * @return string|null
     */
    public function uploadFile(UploadedFile $file, string $folder = 'uploads', string $oldFile = null): ?string
    {
        // Delete the old file if it exists
        if ($oldFile) {
            Storage::disk('public')->delete($oldFile);
        }

        // Store the new file and return its path
        return $file->store($folder, 'public');
    }
}
