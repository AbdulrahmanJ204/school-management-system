<?php

namespace App\Helpers;
class ResponseHelper
{
    public static function jsonResponse($data = null, string $message = '', int $statusCode = 200, bool $successful = true, int $pageCount = null): \Illuminate\Http\JsonResponse
    {
        $responseData = [
            'successful' => $successful,
            'message' => $message,
            'data' => $data,
            'page_count' => $pageCount,
            'status_code' => $statusCode,
        ];

        if (is_null($data) || (is_array($data) && empty($data))) {
            unset($responseData['data']);
        }

        if (is_null($pageCount) || (is_array($pageCount) && empty($pageCount))) {
            unset($responseData['page_count']);
        }
        return response()->json($responseData, $statusCode);
    }
}

