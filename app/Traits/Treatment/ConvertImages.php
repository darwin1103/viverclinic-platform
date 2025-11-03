<?php

namespace App\Traits\Treatment;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait ConvertImages
{

    public function convertImages($treatment)
    {

        $base64Images = $this->extractBase64Images($treatment->terms_conditions);

        foreach ($base64Images as $base64Image) {
            // Decode and save the image
            $imageUrl = $this->saveBase64Image($base64Image);

            // Replace base64 string with the new image URL while preserving other attributes
            $newImgTag = '<img ' . $base64Image['pre_attributes'] . 'src="' . $imageUrl . '"' . $base64Image['post_attributes'] . '>';
            $treatment->terms_conditions = str_replace($base64Image['full_match'], $newImgTag, $treatment->terms_conditions);
        }
    }

    public function extractBase64Images($terms_conditions)
    {
        $pattern = '/<img\s+([^>]*?)src="data:image\/(.*?);base64,(.*?)"(.*?)>/';
        preg_match_all($pattern, $terms_conditions, $matches, PREG_SET_ORDER);

        $base64Images = [];
        foreach ($matches as $match) {
            $base64Images[] = [
                'full_match' => $match[0],
                'pre_attributes' => $match[1],
                'mime_type' => $match[2],
                'base64_data' => $match[3],
                'post_attributes' => $match[4],
            ];
        }

        return $base64Images;
    }

    public function saveBase64Image($base64Image)
    {

        $imageData = base64_decode($base64Image['base64_data']);
        $mimeType = $base64Image['mime_type'];
        $extension = $this->getExtensionFromMimeType($mimeType);
        $fileName = Str::random(10) . '.' . $extension;

        // Save the image to public storage
        $filePath = 'treatments/images/' . $fileName;
        Storage::disk('public')->put($filePath, $imageData);

        // Return the URL to the image
        return Storage::url($filePath);
    }

    public function getExtensionFromMimeType($mimeType)
    {
        $mimeToExt = [
            'jpeg' => 'jpg',
            'png' => 'png',
            'gif' => 'gif',
            'webp' => 'webp',
            // Add other mime types and their extensions as needed
        ];

        return $mimeToExt[$mimeType] ?? 'png';
    }

}
