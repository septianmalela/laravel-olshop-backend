<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class IsUser
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('api')->check() && Auth::guard('api')->user()->role === 'user') {
            return $next($request);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Unauthorized (User only)',
        ], 403);
    }
}
