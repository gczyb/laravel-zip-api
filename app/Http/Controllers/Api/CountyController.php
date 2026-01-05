<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\County;
use Illuminate\Http\Request;

class CountyController extends Controller
{
    public function index()
    {
        return response()->json(['data' => County::all()]);
    }

    public function show($id)
    {
        $county = County::find($id);
        if (!$county) return response()->json(['message' => 'Not found'], 404);
        return response()->json(['data' => $county]);
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $county = County::create($request->all());
        return response()->json(['data' => $county], 201);
    }

    public function update(Request $request, $id)
    {
        $county = County::find($id);
        if (!$county) return response()->json(['message' => 'Not found'], 404);

        $request->validate(['name' => 'required|string|max:255']);
        
        $county->update($request->all());
        
        return response()->json(['data' => $county]);
    }

    public function destroy($id)
    {
        $county = County::find($id);
        if (!$county) return response()->json(['message' => 'Not found'], 404);

        if ($county->cities()->exists()) {
            return response()->json(['message' => 'Nem törölhető: A megyéhez városok tartoznak!'], 400);
        }

        $county->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}