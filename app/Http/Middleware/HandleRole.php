<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Check if user's class matches the required role
        $userClass = strtolower(class_basename($user));
        $requiredRole = strtolower($role);

        if ($userClass !== $requiredRole) {
            return response()->json(['error' => 'Forbidden - Insufficient permissions'], 403);
        }

        return $next($request);
    }
}
