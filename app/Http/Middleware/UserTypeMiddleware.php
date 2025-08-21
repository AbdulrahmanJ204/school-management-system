<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Enums\UserType;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class UserTypeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param  \Closure(Request): (Response|RedirectResponse)  $next
     * @param  string  $allowedTypes  Comma-separated list of allowed user types
     * @return JsonResponse
     */
    public function handle(Request $request, Closure $next, string $allowedTypes): JsonResponse
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return response()->json([
                'message' => 'Unauthenticated',
                'status' => false
            ], HttpResponse::HTTP_UNAUTHORIZED);
        }

        $user = auth()->user();
        $userType = $user->user_type;

        // Parse allowed types (support for multiple types separated by | or comma)
        $allowedTypesArray = array_map('trim', explode('|', $allowedTypes));

        // Check if user type is in allowed types
        if (!in_array($userType, $allowedTypesArray)) {
            return response()->json([
                'message' => 'Access denied. Insufficient permissions.',
                'status' => false,
                'required_types' => $allowedTypesArray,
                'user_type' => $userType
            ], HttpResponse::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
