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
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @param  string  $allowedTypes  Comma-separated list of allowed user types
     * @return JsonResponse
     */
    public function handle(Request $request, Closure $next, string $allowedTypes): JsonResponse
    {
        $user = auth()->user();
        $userType = $user->user_type;

        $allowedTypesArray = array_map('trim', explode('|', $allowedTypes));

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
