<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cookie;
use Inertia\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Cookie as SymfonyCookie;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class AuthController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('refresh.token', only: ['refresh']),
            new Middleware('auth:api', except: ['login', 'register', 'refresh', 'tokenLogin']),
        ];
    }

    public function tokenLogin(): Response
    {
        return inertia('TokenLogin');
    }

    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password'
            ], 401);
        }

        /** @var User $user */
        $user = auth('api')->user();

        $refreshToken = $this->generateRefreshToken($user);
        $refreshCookie = $this->createRefreshTokenCookie($refreshToken);

        return $this->respondWithToken($token, $user)->withCookie($refreshCookie);
    }

    public function refresh(Request $request): JsonResponse
    {
        try {
            $refreshToken = $request->cookie('refresh_token');

            if (!$refreshToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'Refresh token not found'
                ], 401);
            }

            $payload = $this->validateRefreshToken($refreshToken);

            if (!$payload) {
                $clearCookie = Cookie::forget('refresh_token');
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid refresh token'
                ], 401)->withCookie($clearCookie);
            }

            $user = User::find($payload['sub']);

            if (!$user) {
                $clearCookie = Cookie::forget('refresh_token');
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404)->withCookie($clearCookie);
            }

            // Generate new tokens with rotation
            $newAccessToken = auth('api')->login($user);
            $newRefreshToken = $this->generateRefreshToken($user);

            $refreshCookie = $this->createRefreshTokenCookie($newRefreshToken);

            return response()->json([
                'success' => true,
                'access_token' => $newAccessToken,
                'token_type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60
            ])->withCookie($refreshCookie);

        } catch (\Exception $e) {
            $clearCookie = Cookie::forget('refresh_token');
            return response()->json([
                'success' => false,
                'message' => 'Token refresh failed: ' . $e->getMessage()
            ], 401)->withCookie($clearCookie);
        }
    }

    public function user(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'user' => $request->user()
        ]);
    }

    public function logout(): JsonResponse
    {
        auth('api')->logout();

        $clearCookie = Cookie::forget('refresh_token');

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out'
        ])->withCookie($clearCookie);
    }

    public function me(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'user' => auth('api')->user()
        ]);
    }

    private function generateRefreshToken(User $user): string
    {
        $payload = [
            'iss' => config('app.url'),
            'iat' => now()->timestamp,
            'exp' => now()->addDays(14)->timestamp,
            'sub' => $user->id,
            'type' => 'refresh_token',
            'jti' => bin2hex(random_bytes(16)),
            'rot' => now()->timestamp
        ];

        return JWTAuth::getJWTProvider()->encode($payload);
    }

    private function validateRefreshToken(?string $token): ?array
    {
        if (!$token) {
            return null;
        }

        try {
            $payload = JWTAuth::getJWTProvider()->decode($token);

            if (($payload['exp'] ?? 0) < now()->timestamp) {
                return null;
            }

            if (($payload['type'] ?? '') !== 'refresh_token') {
                return null;
            }

            $requiredClaims = ['iss', 'sub', 'jti', 'rot'];
            foreach ($requiredClaims as $claim) {
                if (!isset($payload[$claim])) {
                    return null;
                }
            }

            return $payload;
        } catch (JWTException $e) {
            return null;
        }
    }

    private function createRefreshTokenCookie(string $token): SymfonyCookie
    {
        return cookie(
            'refresh_token',
            $token,
            60 * 24 * 14, // 14 days in minutes
            '/api/auth',
            config('session.domain', null),
            config('session.secure', false),
            true, // httpOnly
            false,
            config('session.same_site', 'lax')
        );
    }

    private function respondWithToken(string $token, User $user): JsonResponse
    {
        return response()->json([
            'success' => true,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => $user
        ]);
    }
}
