<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\County;
use Illuminate\Http\Request;

class CountyController extends Controller
{
    public function index()
    {
        return County::with('cities')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:counties,name'
        ]);

        $county = County::create($request->all());

        return response()->json($county, 201);
    }

    public function show(County $county)
    {
        return $county->load('cities.postalCodes');
    }

    public function update(Request $request, County $county)
    {
        $request->validate([
            'name' => 'required|string|unique:counties,name,' . $county->id
        ]);

        $county->update($request->all());

        return response()->json($county);
    }

    public function destroy(County $county)
    {
        $county->delete();

        return response()->json(['message' => 'County deleted successfully']);
    }
}