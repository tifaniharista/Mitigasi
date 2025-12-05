<?php
// [file name]: CheckUserVerification.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserVerification
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Skip middleware untuk route welcome
        if ($request->routeIs('welcome')) {
            return $next($request);
        }

        // Jika user adalah viewer dan belum terverifikasi
        if ($user && $user->isViewer()) {
            if ($user->isPending()) {
                return redirect()->route('waiting-verification');
            }

            if ($user->isRejected()) {
                return redirect()->route('rejection.show');
            }
        }

        return $next($request);
    }
}
