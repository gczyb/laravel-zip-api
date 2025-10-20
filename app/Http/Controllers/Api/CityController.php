<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    /**
     * @api {get} /api/cities List cities
     * @apiName GetCities
     * @apiGroup City
     *
     * @apiDescription
     * Returns a list of all cities with their county and postal codes.
     *
     * @apiSuccess {Object[]} cities List of cities.
     * @apiSuccess {Number} cities.id City unique ID.
     * @apiSuccess {String} cities.name City name.
     * @apiSuccess {Object} cities.county Parent county (id, name).
     * @apiSuccess {String[]} cities.postalCodes Array of postal code strings.
     */
    public function index()
    {
        return City::with(['county', 'postalCodes'])->get();
    }

    public function store(Request $request)
    {
        /**
         * @api {post} /api/cities Create city
         * @apiName CreateCity
         * @apiGroup City
         *
         * @apiParam {String} name City name.
         * @apiParam {Number} county_id Existing county id.
         *
         * @apiSuccess {Number} id Created city id.
         * @apiSuccess {String} name Created city name.
         * @apiSuccess {Object} county Parent county object.
         * @apiSuccess {String[]} postalCodes Array of postal codes (may be empty).
         */
        $request->validate([
            'name' => 'required|string',
            'county_id' => 'required|exists:counties,id'
        ]);

        $city = City::create($request->all());

        return response()->json($city->load(['county', 'postalCodes']), 201);
    }

    public function show(City $city)
    {
        /**
         * @api {get} /api/cities/:id Get city
         * @apiName GetCity
         * @apiGroup City
         *
         * @apiParam {Number} id City unique ID.
         *
         * @apiSuccess {Number} id City id.
         * @apiSuccess {String} name City name.
         * @apiSuccess {Object} county Parent county.
         * @apiSuccess {String[]} postalCodes City postal codes.
         */
        return $city->load(['county', 'postalCodes']);
    }

    public function update(Request $request, City $city)
    {
        /**
         * @api {put} /api/cities/:id Update city
         * @apiName UpdateCity
         * @apiGroup City
         *
         * @apiParam {Number} id City unique ID.
         * @apiParam {String} name City name.
         * @apiParam {Number} county_id Existing county id.
         *
         * @apiSuccess {Number} id City id.
         * @apiSuccess {String} name Updated city name.
         * @apiSuccess {Object} county Parent county.
         */
        $request->validate([
            'name' => 'required|string',
            'county_id' => 'required|exists:counties,id'
        ]);

        $city->update($request->all());

        return response()->json($city->load(['county', 'postalCodes']));
    }

    public function destroy(City $city)
    {
        /**
         * @api {delete} /api/cities/:id Delete city
         * @apiName DeleteCity
         * @apiGroup City
         *
         * @apiParam {Number} id City unique ID.
         *
         * @apiSuccess {String} message Success message.
         */
        $city->delete();

        return response()->json(['message' => 'City deleted successfully']);
    }
}