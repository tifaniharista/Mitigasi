<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // Jangan redirect ke login jika mengakses halaman welcome
        if ($request->is('/') || $request->routeIs('welcome')) {
            return null;
        }

        return $request->expectsJson() ? null : route('login');
    }
}
