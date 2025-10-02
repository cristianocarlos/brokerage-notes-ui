<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class TokenDataController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth:api');
    }

    public function protectedData(): JsonResponse {
        return response()->json([
            'success' => true,
            'data' => [
                'user' => auth('api')->user(),
                'message' => 'This is protected data from Laravel API',
                'secret' => 'You can only see this if you are authenticated!',
                'timestamp' => now()
            ]
        ]);
    }

    public function userData(): JsonResponse {
        return response()->json([
            'success' => true,
            'user' => auth('api')->user(),
            'message' => 'User data retrieved successfully'
        ]);
    }
}
