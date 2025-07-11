<?php

namespace App\Exceptions;

use App\Helpers\ResponseHelper;
use Exception;

class ImageUploadFailed extends Exception
{
    public function render()
    {
        return ResponseHelper::jsonResponse ([],
            __('messages.user.image_upload_failed'),
            404,
            false
        );
    }
}
