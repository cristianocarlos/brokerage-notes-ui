<?php

namespace App\Http\Controllers;

use App\Components\JWTHelper;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Response;
use Illuminate\Routing\Controllers\HasMiddleware;

class AuthController extends Controller implements HasMiddleware
{
    public static function middleware(): array {
        return [
            // new Middleware('auth:api', except: ['login', 'register', 'refresh', 'tokenLogin']),
        ];
    }

    public function tokenLogin(): Response {
        return inertia('TokenLogin');
    }

    public function login(Request $request): JsonResponse {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $credentials = $request->only('email', 'password');
        if (!$accessToken = auth('api')->attempt($credentials)) {
            return response()->json(['success' => false, 'message' => 'Invalid email or password'], 401);
        }

        /** @var User $user */
        $user = auth('api')->user();
        $refreshToken = JWTHelper::refreshTokenGenerate($user->id);

        return JWTHelper::respondJsonWithAccessTokenAndCookie($accessToken, $user, $refreshToken);
    }

    public function refresh(Request $request): JsonResponse {
        try {
            $refreshToken = $request->cookie('refresh_token');
            if (!$refreshToken) {
                return response()->json(['success' => false, 'message' => 'Refresh token not found'], 401);
            }

            $payload = JWTHelper::refreshTokenValidate($refreshToken);
            if (empty($payload)) return JWTHelper::respondJsonWithExpiredCookie('Invalid refresh token');

            /** @var User $user */
            $user = User::find($payload['sub']);
            if (!$user) return JWTHelper::respondJsonWithExpiredCookie('User not found');
            $newAccessToken = auth('api')->login($user);
            $newRefreshToken = JWTHelper::refreshTokenGenerate($user->id);

            return JWTHelper::respondJsonWithAccessTokenAndCookie($newAccessToken, $user, $newRefreshToken);

        } catch (\Exception $e) {
            return JWTHelper::respondJsonWithExpiredCookie('Token refresh failed: ' . $e->getMessage());
        }
    }

    public function logout(): JsonResponse {
        auth('api')->logout();
        return JWTHelper::respondJsonWithExpiredCookie('Successfully logged out');
    }
}
