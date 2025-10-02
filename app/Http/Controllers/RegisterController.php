<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController
{
    /**
     * Handle an authentication attempt.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $data = request()->validate([
            'name' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $data['password'] = bcrypt($data['password']);
        $user = User::create($data);

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
