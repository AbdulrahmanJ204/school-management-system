<?php

namespace App\Exceptions;

use Exception;

class ImageUploadFailed extends Exception
{
    public function render()
    {
        return response()->json([
            'success' => false,
            'message' => __('messages.user.image_upload_failed') // Use lang file for consistency
        ], 404);
    }
}
