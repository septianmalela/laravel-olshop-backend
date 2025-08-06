<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenBlacklistedException;

class HandleJwtExceptions
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            return $next($request);
        } catch (TokenExpiredException $e) {
            return response()->json(['status' => 'error', 'message' => 'Token has expired'], 401);
        } catch (TokenInvalidException $e) {
            return response()->json(['status' => 'error', 'message' => 'Invalid token'], 401);
        } catch (TokenBlacklistedException $e) {
            return response()->json(['status' => 'error', 'message' => 'Token has been blacklisted'], 401);
        } catch (JWTException $e) {
            return response()->json(['status' => 'error', 'message' => 'JWT error: ' . $e->getMessage()], 401);
        }
    }
}
