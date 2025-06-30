<?php

namespace App\Exceptions;

use Exception;

class InvalidPasswordException extends Exception
{
    public function render()
    {
        return response()->json([
            'success' => false,
            'message' => __('messages.auth.invalid_password') // Use lang file for consistency
        ], 401);
    }
}
