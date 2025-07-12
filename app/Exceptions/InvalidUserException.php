<?php

namespace App\Exceptions;

use App\Helpers\ResponseHelper;
use Exception;

class InvalidUserException extends Exception
{
    protected $message;
    public function __construct(string $message)
    {
        parent::__construct();
        $this->message = $message;
    }

    public function render()
    {
        return ResponseHelper::jsonResponse(
            null,
            $this->message,
            400,
            false
        );
    }
}
