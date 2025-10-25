<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminOrOwner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Admin can access everything
        if ($user->isAdmin()) {
            return $next($request);
        }

        // For specific resource access, check ownership
        if ($request->route('client')) {
            $client = $request->route('client');
            if ($client->user_id === $user->id) {
                return $next($request);
            }
        }

        abort(403, 'Unauthorized access.');
    }
}
