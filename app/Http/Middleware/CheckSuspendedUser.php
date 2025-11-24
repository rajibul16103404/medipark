<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSuspendedUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth('api')->check()) {
            return $next($request);
        }

        $user = auth('api')->user();

        if ($user && $user->isSuspended()) {
            return response()->json([
                'message' => 'Your account has been suspended. Reason: '.$user->suspension_reason,
            ], 403);
        }

        return $next($request);
    }
}
