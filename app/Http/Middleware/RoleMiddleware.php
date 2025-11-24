<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! auth()->check()) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }

        $user = auth()->user();

        if ($user->isSuspended()) {
            return response()->json([
                'message' => 'Your account has been suspended. Reason: '.$user->suspension_reason,
            ], 403);
        }

        if (empty($roles)) {
            return $next($request);
        }

        if (! $user->hasAnyRole($roles)) {
            return response()->json([
                'message' => 'You do not have the required role to access this resource',
            ], 403);
        }

        return $next($request);
    }
}
