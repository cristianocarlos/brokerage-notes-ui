<?php

namespace App\Components;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie as SymfonyCookie;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

// TODO:
// tem como retornar null no decode? refreshTokenValidate em caso de token invalido retorna oq?

class JWTHelper
{
    const string REFRESH_TOKEN_NAME = 'refresh_token';
    const string REFRESH_COOKIE_NAME = self::REFRESH_TOKEN_NAME;
    const string REFRESH_COOKIE_PATH = 'api/auth';
    const int REFRESH_COOKIE_DURATION_IN_DAYS = 14;

    private static function refreshCookieSet(string|null $value): SymfonyCookie {
        return cookie(
            name: static::REFRESH_COOKIE_NAME,
            value: $value,
            minutes: $value ? 60 * 24 * static::REFRESH_COOKIE_DURATION_IN_DAYS : -1,
            path: static::REFRESH_COOKIE_PATH,
            domain: config('session.domain', null),
            secure: config('session.secure', false),
            httpOnly: true, // httpOnly
            raw: false,
            sameSite: config('session.same_site', 'lax'),
        );
    }

    static function refreshTokenGenerate(int $userId): string {
        $payload = [
            'iss' => config('app.url'),
            'iat' => now()->timestamp,
            'exp' => now()->addDays(static::REFRESH_COOKIE_DURATION_IN_DAYS)->timestamp,
            'sub' => $userId,
            'type' => static::REFRESH_TOKEN_NAME,
            'jti' => bin2hex(random_bytes(16)),
            'rot' => now()->timestamp,
        ];
        return JWTAuth::getJWTProvider()->encode($payload);
    }

    static function refreshTokenValidate(string $refreshToken): array {
        try {
            $payload = JWTAuth::getJWTProvider()->decode($refreshToken);
            if (($payload['exp'] ?? 0) < now()->timestamp) return [];
            if (($payload['type'] ?? '') !== static::REFRESH_TOKEN_NAME) return [];
            if (array_any(['iss', 'sub', 'jti', 'rot'], fn($claim) => !isset($payload[$claim]))) {
                return [];
            }
            return $payload;
        } catch (JWTException $e) {
            return [];
        }
    }

    static function respondJsonWithExpiredCookie(string $message, int $httpStatusCode = 401): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $httpStatusCode)->withCookie(JWTHelper::refreshCookieSet(null));
    }

    static function respondJsonWithAccessTokenAndCookie(string $accessToken, User $user, string $refreshToken): JsonResponse {
        return response()->json([
            'success' => true,
            'access_token' => $accessToken,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => $user,
            '__debug_new_refresh_token' => substr($refreshToken, -8),
            '__debug_old_refresh_token' => substr(request()->cookie('refresh_token'), -8),
        ])->withCookie(JWTHelper::refreshCookieSet($refreshToken));
    }
}
