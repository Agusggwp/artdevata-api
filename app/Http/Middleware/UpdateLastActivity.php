<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdateLastActivity
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('admin')->check()) {
            $admin = Auth::guard('admin')->user();
            // Update last_activity_at if more than 1 minute has passed
            if (!$admin->last_activity_at || $admin->last_activity_at->diffInMinutes(now()) >= 1) {
                $admin->timestamps = false;
                $admin->last_activity_at = now();
                $admin->save();
            }
        }

        return $next($request);
    }
}
