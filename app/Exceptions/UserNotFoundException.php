<?php

namespace App\Exceptions;

use Exception;

class UserNotFoundException extends Exception
{
    public function render()
    {
        return response()->json([
            'success' => false,
            'message' => __('messages.user.not-found') // Use lang file for consistency
        ], 404);
    }
}
