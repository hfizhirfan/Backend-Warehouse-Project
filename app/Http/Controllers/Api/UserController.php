<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * 📄 GET /users
     * Ambil semua user
     */
    public function index()
    {
        $users = User::select('id', 'name', 'username', 'role', 'created_at')
            ->latest()
            ->get();

        return response()->json($users);
    }

    /**
     * ➕ POST /users
     * Tambah user baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:4',
            'role' => 'required|in:super_admin,admin,guest',
            'brand_id' => 'nullable|exists:brands,id'
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => strtolower($request->username),
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'brand_id' => $request->role === 'super_admin'
                ? null
                : $request->brand_id // 🔥 INI FIX UTAMA
        ]);

        return response()->json([
            'message' => 'User berhasil dibuat',
            'data' => $user
        ], 201);
    }

    /**
     * 🔍 GET /users/{id}
     */
    public function show($id)
    {
        $user = User::findOrFail($id);

        return response()->json($user);
    }

    /**
     * ✏️ PUT /users/{id}
     * Update user
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => "required|string|max:255|unique:users,username,$id",
            'password' => 'nullable|string|min:4',
            'role' => 'required|in:admin,guest'
        ]);

        $user->update([
            'name' => $request->name,
            'username' => strtolower($request->username),
            'role' => $request->role,
            'brand_id' => $request->role === 'super_admin'
                ? null
                : $request->brand_id,
            'password' => $request->password
                ? Hash::make($request->password)
                : $user->password
        ]);

        return response()->json([
            'message' => 'User berhasil diupdate',
            'data' => $user
        ]);
    }

    /**
     * ❌ DELETE /users/{id}
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // 🔥 Optional: cegah hapus diri sendiri
        if (auth()->id() === $user->id) {
            return response()->json([
                'message' => 'Tidak bisa menghapus akun sendiri'
            ], 403);
        }

        $user->delete();

        return response()->json([
            'message' => 'User berhasil dihapus'
        ]);
    }
}
