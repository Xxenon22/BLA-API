<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MaterialType;
use Illuminate\Http\Request;

class MaterialTypeController extends Controller
{
    // public function index()
    // {
    //     return response()->json(MaterialType::all(), 200);
    // }

    // public function store(Request $request)
    // {
    //     $request->validate(['name' => 'required|string|max:255']);
    //     $materialType = MaterialType::create(['name' => $request->name]);

    //     return response()->json($materialType, 201);
    // }

    // public function show($id)
    // {
    //     $materialType = MaterialType::findOrFail($id);
    //     return response()->json($materialType, 200);
    // }

    // public function update(Request $request, $id)
    // {
    //     $request->validate(['name' => 'required|string|max:255']);
    //     $materialType = MaterialType::findOrFail($id);
    //     $materialType->update(['name' => $request->name]);

    //     return response()->json($materialType, 200);
    // }

    // public function destroy($id)
    // {
    //     $materialType = MaterialType::findOrFail($id);
    //     $materialType->delete();

    //     return response()->json(['message' => 'Material type deleted successfully'], 200);
    // }

    public function index()
    {
        $materialTypes = MaterialType::all();

        return response()->json([
            'success' => true,
            'data' => $materialTypes
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:material_types,name',
        ]);

        $materialType = new MaterialType();
        $materialType->name = $request->name;
        $materialType->save();

        return response()->json($materialType, 201);
    }

    public function show($id)
    {
        $materialType = MaterialType::findOrFail($id);
        return response()->json($materialType);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|unique:material_types,name,' . $id,
        ]);

        $materialType = MaterialType::findOrFail($id);
        $materialType->name = $request->name;
        $materialType->save();

        return response()->json($materialType);
    }

    public function destroy($id)
    {
        $materialType = MaterialType::findOrFail($id);
        $materialType->delete();
        return response()->json(null, 204);
    }
}
