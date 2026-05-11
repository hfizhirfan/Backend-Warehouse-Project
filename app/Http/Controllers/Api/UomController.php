<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Uom;
use Illuminate\Http\Request;

class UomController extends Controller
{
    public function index()
    {
        return Uom::latest()->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:uoms,name'
        ]);

        return Uom::create($request->all());
    }

    public function show($id)
    {
        return Uom::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $uom = Uom::findOrFail($id);
        $uom->update($request->all());

        return $uom;
    }

    public function destroy($id)
    {
        Uom::destroy($id);

        return response()->json(['message' => 'Deleted']);
    }
}
