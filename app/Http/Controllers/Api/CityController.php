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
        // 1. Megkeressük a várost
        $city = City::find($id);

        if (!$city) {
            return response()->json(['message' => 'City not found'], 404);
        }

        // 2. Validálás
        try {
            $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'county_id' => 'sometimes|required|exists:counties,id'
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Validációs hiba: ' . $e->getMessage()], 422);
        }

        // 3. MENTÉS (Itt van a "csapda")
        try {
            // Megpróbáljuk frissíteni
            $city->update($request->only(['name', 'county_id']));
            
            // Ha sikerült, visszaküldjük
            return new CityResource($city->load(['county', 'postalCodes']));

        } catch (\Exception $e) {
            // HA HIBA VAN: Visszaküldjük a pontos hibaüzenetet a Frontendnek!
            return response()->json([
                'message' => 'Szerver hiba történt a mentéskor!',
                'error_details' => $e->getMessage(), // Itt lesz a lényeg!
                'file' => $e->getFile(),
                'line' => $e->getLine()
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