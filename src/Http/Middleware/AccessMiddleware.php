<?php

namespace Hamzasgd\LaravelOps\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccessMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Only allow access in local environment or to authenticated users with specific permissions
        if (app()->environment('local') || 
            (Auth::check() && $this->userHasAccess(Auth::user()))) {
            return $next($request);
        }

        abort(403, 'Unauthorized access to LaravelOps');
    }
    
    protected function userHasAccess($user)
    {
        // Check if user is admin or has specific permissions
        // This can be customized based on your application's authorization system
        return method_exists($user, 'isAdmin') ? $user->isAdmin() : false;
    }
} 