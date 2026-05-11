<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $user = auth('sanctum')->user();

        // 1. Pastikan user login (Pencegahan Error 500 & 401)
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // 2. Logika Role:
        // Jika Super Admin, tampilkan semua brand.
        // Jika Admin biasa, HANYA tampilkan brand miliknya sendiri.
        if ($user->isSuperAdmin()) {
            $brands = Brand::latest()->get();
        } else {
            if (!$user->brand_id) {
                return response()->json([
                    'message' => 'User tidak punya brand_id'
                ], 400);
            }

            $brands = Brand::where('id', $user->brand_id)->latest()->get();
        }

        return response()->json($brands);
    }

    public function store(Request $request)
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Hanya Super Admin yang boleh membuat Brand baru
        if (!$user->isSuperAdmin()) {
            return response()->json(['message' => 'Forbidden - Only Super Admin can create brands'], 403);
        }

        $request->validate([
            'name' => 'required|unique:brands,name'
        ]);

        $brand = Brand::create($request->all());

        return response()->json($brand, 201);
    }

    public function show(Request $request, $id)
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $brand = Brand::findOrFail($id);

        // Jika bukan Super Admin, pastikan brand yang dilihat adalah miliknya
        if (!$user->isSuperAdmin() && $user->brand_id !== $brand->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return response()->json($brand);
    }

    public function update(Request $request, $id)
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Hanya Super Admin yang boleh mengubah nama Brand
        if (!$user->isSuperAdmin()) {
            return response()->json(['message' => 'Forbidden - Only Super Admin can edit brands'], 403);
        }

        $brand = Brand::findOrFail($id);

        $request->validate([
            'name' => 'required|unique:brands,name,' . $id
        ]);

        $brand->update($request->all());

        return response()->json($brand);
    }

    public function destroy(Request $request, $id)
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Hanya Super Admin yang boleh menghapus Brand
        if (!$user->isSuperAdmin()) {
            return response()->json(['message' => 'Forbidden - Only Super Admin can delete brands'], 403);
        }

        $brand = Brand::findOrFail($id);
        $brand->delete();

        return response()->json(['message' => 'Brand Deleted Successfully']);
    }
}
