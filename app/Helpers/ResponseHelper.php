<?php

namespace App\Helpers;
class ResponseHelper
{
    public static function jsonResponse($data = null, string $message = '', int $statusCode = 200, bool $successful = true): \Illuminate\Http\JsonResponse
    {
        $responseData = [
            'successful' => $successful,
            'message' => $message,
            'data' => $data,
            'status_code' => $statusCode,
        ];

        if (is_null($data) || (is_array($data) && empty($data))) {
            unset($responseData['data']);
        }

        return response()->json($responseData, $statusCode);
    }
}

