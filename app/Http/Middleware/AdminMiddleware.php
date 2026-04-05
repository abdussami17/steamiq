<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Agar user logged in nahi hai
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        // Admin users → full access
        if ($user->hasRole('admin')) {
            return $next($request);
        }

        // Users with at least one role → access allowed
        if ($user->roles->count() > 0) {
            return $next($request);
        }

        // Users with no roles → redirect to dashboard
        return redirect()->route('dashboard.index')->with('error', 'Access restricted! You do not have permission to view this page.');
    }
}