<?php

namespace App\Http\Middleware;

use App\Services\LoggingService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogErrors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $response = $next($request);

            // Log 4xx and 5xx errors
            if ($response->getStatusCode() >= 400) {
                $this->logErrorResponse($request, $response);
            }

            return $response;
        } catch (\Throwable $exception) {
            // Log the exception
            $this->logException($request, $exception);

            // Re-throw the exception to let Laravel handle it
            throw $exception;
        }
    }

    /**
     * Log an error response.
     */
    private function logErrorResponse(Request $request, Response $response): void
    {
        try {
            $loggingService = app(LoggingService::class);

            // Create a custom exception for logging purposes
            $exception = new \Exception(
                "HTTP {$response->getStatusCode()}: {$response->getContent()}",
                $response->getStatusCode()
            );

            $loggingService->logError($exception, $request);
        } catch (\Exception $e) {
            // Don't let logging errors break the application
            \Log::error('Failed to log error response: ' . $e->getMessage());
        }
    }

    /**
     * Log an exception.
     */
    private function logException(Request $request, \Throwable $exception): void
    {
        try {
            $loggingService = app(LoggingService::class);
            $loggingService->logError($exception, $request);
        } catch (\Exception $e) {
            // Don't let logging errors break the application
            \Log::error('Failed to log exception: ' . $e->getMessage());
        }
    }
}
