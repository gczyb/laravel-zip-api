<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Http\Resources\CityResource;
use Illuminate\Http\Request;

class CityController extends Controller
{

    public function index()
    {
        $cities = City::with(['county', 'postalCodes'])->get();
        return CityResource::collection($cities);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'county_id' => 'required|exists:counties,id'
        ]);

        $city = City::create($request->all());

        if ($request->has('postal_code')) {
            $city->postalCodes()->create(['code' => $request->postal_code]);
        }

        return new CityResource($city->load(['county', 'postalCodes']));
    }

    public function show($id)
    {
        $city = City::with(['county', 'postalCodes'])->find($id);

        if (!$city) {
            return response()->json(['message' => 'City not found'], 404);
        }

        return new CityResource($city);
    }

    public function update(Request $request, $id)
    {
        $city = City::find($id);
        
        if (!$city) {
            return response()->json(['message' => 'City not found'], 404);
        }

        try {
            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'county_id' => 'sometimes|required|exists:counties,id'
            ]);
            
            $city->update($validated);

            return new CityResource($city->load(['county', 'postalCodes']));

        } catch (\Exception $e) {
            \Log::error('Mentési hiba: ' . $e->getMessage());
            return response()->json([
                'message' => 'Szerver hiba történt a mentéskor!',
                'error_details' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $city = City::find($id);

        if (!$city) {
            return response()->json(['message' => 'City not found'], 404);
        }

        $city->delete();

        return response()->json(['message' => 'City deleted successfully']);
    }

    public function getFirstLetters($countyId)
    {
        $letters = City::where('county_id', $countyId)
            ->selectRaw('DISTINCT UPPER(SUBSTRING(name, 1, 1)) as letter')
            ->orderBy('letter')
            ->pluck('letter');

        return response()->json(['data' => $letters]);
    }

    public function getCitiesByLetter($countyId, $letter)
    {
        $cities = City::with('postalCodes', 'county')
            ->where('county_id', $countyId)
            ->whereRaw('UPPER(SUBSTRING(name, 1, 1)) = ?', [strtoupper($letter)])
            ->orderBy('name')
            ->get();

        return CityResource::collection($cities);
    }
}