<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index()
    {
        return City::with(['county', 'postalCodes'])->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'county_id' => 'required|exists:counties,id'
        ]);

        $city = City::create($request->all());

        return response()->json($city->load(['county', 'postalCodes']), 201);
    }

    public function show(City $city)
    {
        return $city->load(['county', 'postalCodes']);
    }

    public function update(Request $request, City $city)
    {
        $request->validate([
            'name' => 'required|string',
            'county_id' => 'required|exists:counties,id'
        ]);

        $city->update($request->all());

        return response()->json($city->load(['county', 'postalCodes']));
    }

    public function destroy(City $city)
    {
        $city->delete();

        return response()->json(['message' => 'City deleted successfully']);
    }
}