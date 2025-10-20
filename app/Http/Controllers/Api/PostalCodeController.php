<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PostalCode;
use Illuminate\Http\Request;

class PostalCodeController extends Controller
{
    public function index()
    {
        return PostalCode::with('city.county')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:4|unique:postal_codes,code',
            'city_id' => 'required|exists:cities,id'
        ]);

        $postalCode = PostalCode::create($request->all());

        return response()->json($postalCode->load('city.county'), 201);
    }

    public function show(PostalCode $postalCode)
    {
        return $postalCode->load('city.county');
    }

    public function update(Request $request, PostalCode $postalCode)
    {
        $request->validate([
            'code' => 'required|string|size:4|unique:postal_codes,code,' . $postalCode->id,
            'city_id' => 'required|exists:cities,id'
        ]);

        $postalCode->update($request->all());

        return response()->json($postalCode->load('city.county'));
    }

    public function destroy(PostalCode $postalCode)
    {
        $postalCode->delete();

        return response()->json(['message' => 'Postal code deleted successfully']);
    }
}