<?php

namespace App\Http\Middleware;

use Closure;
use Exception;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        $role = $request->header('role');
        if (!$role) {
            return response()->json([
                'error' => 'Role not provided'
            ], 401);
        }
        if (in_array($role, $roles)) {
            return $next($request);
        }

        return response()->json(['error' => 'cant access'], 400);
    }
}
