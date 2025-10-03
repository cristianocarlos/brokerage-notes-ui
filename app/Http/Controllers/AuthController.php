<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class AuthController
{
  function token(): JsonResponse {
      $curl = curl_init();

      curl_setopt_array($curl, [
        CURLOPT_URL => 'http://127.0.0.1:8000/auth/token',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode([
            'secret_key' => '197b2c37c391bed93fe80344fe73b806947a65e36206e05a1a23c2fa12702fe3',
        ]),
        CURLOPT_HTTPHEADER => [
          'Content-Type: application/json'
        ],
      ]);

      $response = curl_exec($curl);

      curl_close($curl);

      return response()->json(json_decode($response));
  }
}
