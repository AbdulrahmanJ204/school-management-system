<?php

namespace App\Exceptions;

use App\Helpers\ResponseHelper;
use Exception;

class InvalidTargetException extends Exception
{
    public function __construct($message = "Invalid quiz target")
    {
        parent::__construct($message);
    }

    public function render($request)
    {
        return ResponseHelper::jsonResponse ([],
            $this->getMessage(),
            404,
            false
        );
    }
}
