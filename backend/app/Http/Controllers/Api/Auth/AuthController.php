<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\TwoFactorCodeMail;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $throttleKey = 'login_attempts:' . $request->ip();
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($throttleKey, 10)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'email' => ['زۆر هەوڵت داوە. تکایە دوای ' . $seconds . ' چرکەی تر هەوڵ بدەرەوە.'],
            ]);
        }

        $user = User::where('email', $request->email)->first();

        if ($user) {
            if ($user->is_locked) {
                \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 60);
                throw ValidationException::withMessages([
                    'email' => ['ئەم ئەکاونتە بەهۆی هەڵەی زۆرەوە داخراوە. تکایە پەیوەندی بە بەڕێوەبەرەوە بکە.'],
                ]);
            }

            if (! Hash::check($request->password, $user->password)) {
                $user->increment('failed_login_attempts');
                \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 60);

                if ($user->failed_login_attempts >= 5) {
                    $user->update([
                        'is_locked' => true,
                        'locked_at' => now(),
                    ]);
                    throw ValidationException::withMessages([
                        'email' => ['ئەم ئەکاونتە بەهۆی هەڵەی زۆرەوە داخراوە. تکایە پەیوەندی بە بەڕێوەبەرەوە بکە.'],
                    ]);
                }

                throw ValidationException::withMessages([
                    'email' => ['وشەی تێپەڕ هەڵەیە. ' . (5 - $user->failed_login_attempts) . ' هەوڵت ماوە پێش ئەوەی ئەکاونتەکەت دابخرێت.'],
                ]);
            }

            // Success -> reset attempts
            if ($user->failed_login_attempts > 0) {
                $user->update(['failed_login_attempts' => 0]);
            }
            \Illuminate\Support\Facades\RateLimiter::clear($throttleKey);
        } else {
            \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 60);
            throw ValidationException::withMessages([
                'email' => ['ئەم ئیمەیڵە بوونی نییە یان زانیارییەکان هەڵەیە.'],
            ]);
        }

        $code = rand(100000, 999999);
        $user->update([
            'two_factor_code' => $code,
            'two_factor_expires_at' => now()->addMinutes(10),
        ]);

        Mail::to($user->email)->send(new TwoFactorCodeMail($code));

        return response()->json([
            'two_factor_required' => true,
            'message' => 'کۆدی دڵنیایی بۆ ئیمەیڵەکەت نێردرا.',
            'email' => $user->email
        ]);
    }

    public function verify2FA(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required',
        ]);

        $user = User::where('email', $request->email)
            ->where('two_factor_code', $request->code)
            ->where('two_factor_expires_at', '>', now())
            ->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'code' => ['کۆدەکە هەڵەیە یان کاتەکەی بەسەرچووە.'],
            ]);
        }

        $user->update([
            'two_factor_code' => null,
            'two_factor_expires_at' => null,
        ]);

        // Enforce Single Device Login: Revoke all existing tokens before creating a new one
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user->load(['roles', 'permissions'])
        ]);
    }

    public function me(Request $request)
    {
        return $request->user()->load(['roles', 'permissions']);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'بە سەرکەوتوویی چوویتە دەرەوە.']);
    }
}
