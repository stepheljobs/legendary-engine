<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsTeamMember
{
    /**
     * Handle an incoming request.
     *
     * Ensures the authenticated user is a member of the current tenant.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Check if user has a team member record in the current tenant
        if (!$request->user()->teamMember) {
            abort(403, 'You are not a member of this organization.');
        }

        return $next($request);
    }
}
