<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthRedirectMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('custom.login', ['id' => 143, 'name' => 'qr']);
        }

        return $next($request);
    }
}
