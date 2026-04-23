<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\TwoFactorCodeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AuthController extends Controller
{
    /**
     * Initial Login Attempt
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'زانیارییەکان هەڵەن.'
            ], 401);
        }

        // 1. Single Session Enforcement: Delete all previous tokens
        $user->tokens()->delete();

        // 2. Generate 6-digit code
        $code = rand(100000, 999999);
        $user->two_factor_code = $code;
        $user->two_factor_expires_at = Carbon::now()->addMinutes(10);
        $user->save();

        // 3. Send Email (Premium Template)
        try {
            Mail::to($user->email)->send(new TwoFactorCodeMail($code));
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'کێشەیەک لە ناردنی ئیمەیڵدا هەیە: ' . $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'کۆدی دڵنیایی بۆ ئیمەیڵەکەت نێردرا.',
            'two_factor_required' => true,
            'email' => $user->email
        ]);
    }

    /**
     * Verify 2FA Code and Issue Token
     */
    public function verify2FA(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string',
        ]);

        $code = trim($request->code);

        $user = User::where('email', $request->email)
            ->where('two_factor_code', $code)
            ->where('two_factor_expires_at', '>', Carbon::now())
            ->first();

        if (!$user) {
            // Check if it exists but expired
            $exists = User::where('email', $request->email)
                ->where('two_factor_code', $code)
                ->first();
                
            if ($exists) {
                return response()->json([
                    'message' => 'کۆدەکە بەسەرچووە، تکایە دانەیەکی نوێ داوا بکەرەوە.'
                ], 422);
            }

            return response()->json([
                'message' => 'کۆدەکە هەڵەیە.'
            ], 422);
        }

        // Clear the code
        $user->two_factor_code = null;
        $user->two_factor_expires_at = null;
        $user->save();

        // Issue Sanctum Token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->getRoleNames(),
                'permissions' => $user->getAllPermissions()->pluck('name'),
            ]
        ]);
    }

    /**
     * Get Authenticated User Info
     */
    public function me(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $user->getRoleNames(),
            'permissions' => $user->getAllPermissions()->pluck('name'),
        ]);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'بە سەرکەوتوویی چوویتە دەرەوە.']);
    }
}
