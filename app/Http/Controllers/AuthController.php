<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController
{
    public function showLogin(Request $request)
    {
        if (Auth::check()) {
            return redirect('/');
        }

        return view('app', ['page' => 'Login', 'title' => 'Login']);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        $credentials['email'] = config('services.admin.email', 'admin@snev.dev');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return response()->json(['success' => true, 'redirect' => '/']);
        }

        return response()->json([
            'errors' => ['password' => 'The provided access key is invalid.'],
        ], 422);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect('/');
    }
}
