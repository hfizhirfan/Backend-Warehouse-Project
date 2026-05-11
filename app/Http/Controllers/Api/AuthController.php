<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * 🔐 LOGIN (pakai username)
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $username = strtolower($request->username);

        if (!Auth::attempt([
            'username' => $username,
            'password' => $request->password
        ])) {
            return response()->json([
                'message' => 'Username atau password salah'
            ], 401);
        }

        $user = Auth::user();

        // ✅ load relasi brand
        $user->load('brand');

        // hapus token lama
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'role' => $user->role,
                'brand_id' => $user->brand_id,

                // ✅ konsisten object
                'brand' => $user->brand ? [
                    'id' => $user->brand->id,
                    'name' => $user->brand->name,
                ] : null,
            ]
        ]);
    }

    /**
     * 🚪 LOGOUT
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil'
        ]);
    }
}
