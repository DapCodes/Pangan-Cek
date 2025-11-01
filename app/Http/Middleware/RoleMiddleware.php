<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        if (!Auth::check()) abort(403);

        $userRole = strtoupper(Auth::user()->role ?? '');
        $allowed  = collect($roles)
            ->flatMap(fn($r) => explode(',', $r))
            ->map(fn($r) => strtoupper(trim($r)))
            ->filter()
            ->values()
            ->all();

        if (!in_array($userRole, $allowed, true)) abort(403);

        return $next($request);
    }
}
