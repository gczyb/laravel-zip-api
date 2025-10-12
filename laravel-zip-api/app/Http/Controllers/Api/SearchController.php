<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PostalCode;
use App\Models\City;
use App\Models\County;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q');

        if (!$query) {
            return response()->json(['error' => 'Search query is required'], 400);
        }

        $postalCodes = PostalCode::where('code', 'LIKE', "%{$query}%")
            ->with('city.county')
            ->limit(10)
            ->get();

        $cities = City::where('name', 'LIKE', "%{$query}%")
            ->with(['county', 'postalCodes'])
            ->limit(10)
            ->get();

        $counties = County::where('name', 'LIKE', "%{$query}%")
            ->with('cities')
            ->limit(10)
            ->get();

        return response()->json([
            'postal_codes' => $postalCodes,
            'cities' => $cities,
            'counties' => $counties
        ]);
    }
}