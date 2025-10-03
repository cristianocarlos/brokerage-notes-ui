<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;

class RefreshTokenMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $refreshToken = $request->cookie('refresh_token');

        if (!$refreshToken) {
            return response()->json([
                'success' => false,
                'message' => 'Refresh token not found'
            ], 401);
        }

        try {
            $payload = JWTAuth::getJWTProvider()->decode($refreshToken);

            if (($payload['exp'] ?? 0) < now()->timestamp) {
                $this->clearRefreshTokenCookie();
                return response()->json([
                    'success' => false,
                    'message' => 'Refresh token expired'
                ], 401);
            }

            if (($payload['type'] ?? '') !== 'refresh_token') {
                $this->clearRefreshTokenCookie();
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid token type'
                ], 401);
            }

            $request->attributes->set('refresh_token_payload', $payload);

        } catch (JWTException $e) {
            $this->clearRefreshTokenCookie();
            return response()->json([
                'success' => false,
                'message' => 'Invalid refresh token'
            ], 401);
        }

        return $next($request);
    }

    private function clearRefreshTokenCookie(): void
    {
        Cookie::queue(Cookie::forget('refresh_token'));
    }
}
