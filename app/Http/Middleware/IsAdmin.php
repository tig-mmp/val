<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

final class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request):Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return to_route('login')
                ->with('error', 'Please log in to access this area.');
        }

        abort_unless(Auth::user()->isAdmin(), 403, 'Access denied. Admin privileges required.');

        return $next($request);
    }
}
