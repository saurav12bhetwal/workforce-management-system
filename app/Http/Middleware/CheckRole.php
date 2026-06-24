<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // 1. Ensure user is logged in
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 2. Check if the user has the required role
        if (!$user->hasRole($role)) {
            // Abort with a 403 Forbidden error if they try to access another role's route
            abort(403, 'Unauthorized action. You do not have the required role.');
        }

        return $next($request);
    }
}