<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PrivilegeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$privileges): Response
    {
        if (! auth('api')->check()) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }

        /** @var User|null $user */
        $user = auth('api')->user();

        if ($user && $user->isSuspended()) {
            return response()->json([
                'message' => 'Your account has been suspended. Reason: '.$user->suspension_reason,
            ], 403);
        }

        if (empty($privileges)) {
            return $next($request);
        }

        // Check if user has any of the required privileges
        $hasPrivilege = false;
        foreach ($privileges as $privilege) {
            if ($user && $user->hasPrivilege($privilege)) {
                $hasPrivilege = true;
                break;
            }
        }

        if (! $hasPrivilege) {
            return response()->json([
                'message' => 'You do not have the required privilege to access this resource',
            ], 403);
        }

        return $next($request);
    }
}
