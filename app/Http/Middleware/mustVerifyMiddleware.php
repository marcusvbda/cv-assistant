<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class mustVerifyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $exceptions = [
            'filament.admin.auth.email-verification.verify',
        ];
        $routeName = $request->route()?->getName();
        if (auth()->check() && !auth()->user()->hasVerifiedEmail() && !in_array($routeName, $exceptions)) {
            return abort(403, 'Your email address is not verified. Please verify your email to access this resource.');
        }
        return $next($request);
    }
}
