<?php

namespace App\Exceptions;

use Exception;

class PermissionException extends Exception
{
    public function render()
    {
        return response()->json([
            'success' => false,
            'message' => __('messages.auth.permission_denied') // Use lang file for consistency
        ], 403); // Forbidden
    }
}
