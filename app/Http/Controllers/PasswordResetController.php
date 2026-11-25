<?php

namespace App\Http\Controllers;

use App\Http\Requests\PasswordResetRequestRequest;
use App\Http\Requests\PasswordResetUpdateRequest;
use App\Mail\PasswordResetMail;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PasswordResetController
{
    public function request(PasswordResetRequestRequest $request)
    {
        $adminEmail = config('services.admin.email');
        $token = Str::random(64);

        Cache::put(
            $this->cacheKey($token),
            ['email' => $adminEmail],
            now()->addMinutes(60)
        );

        Mail::to($adminEmail)->send(new PasswordResetMail(url('/inchecken/reset-password/'.$token)));

        return response()->json([
            'success' => true,
            'message' => 'Password reset email sent successfully.',
        ]);
    }

    public function show(string $token)
    {
        return view('app', [
            'page' => 'ResetPassword',
            'title' => 'Reset password',
            'props' => ['token' => $token],
        ]);
    }

    public function update(PasswordResetUpdateRequest $request)
    {
        $data = $request->validated();
        $payload = Cache::pull($this->cacheKey($data['token']));

        if (! $payload) {
            throw ValidationException::withMessages([
                'token' => 'The reset link is invalid or has expired.',
            ]);
        }

        User::updateOrCreate(
            ['email' => $payload['email'] ?? config('services.admin.email')],
            ['name' => 'Snevver', 'password' => $data['password']]
        );

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully.',
            'redirect' => '/inchecken',
        ]);
    }

    protected function cacheKey(string $token): string
    {
        return 'password-reset:'.hash('sha256', $token);
    }
}
