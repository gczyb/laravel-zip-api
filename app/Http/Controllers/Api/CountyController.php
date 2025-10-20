<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\County;
use Illuminate\Http\Request;

class CountyController extends Controller
{
    /**
     * @api {get} /api/counties List counties
     * @apiName GetCounties
     * @apiGroup County
     *
     * @apiDescription
     * Returns a list of all counties with their cities.
     *
     * @apiSuccess {Object[]} counties List of counties.
     * @apiSuccess {Number} counties.id County unique ID.
     * @apiSuccess {String} counties.name County name.
     * @apiSuccess {Object[]} counties.cities Array of related cities (id, name).
     *
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * [
     *   {
     *     "id": 1,
     *     "name": "Budapest",
     *     "cities": [ { "id": 10, "name": "District 1" } ]
     *   }
     * ]
     */
    public function index()
    {
        return County::with('cities')->get();
    }

    public function store(Request $request)
    {
        /**
         * @api {post} /api/counties Create county
         * @apiName CreateCounty
         * @apiGroup County
         *
         * @apiParam {String} name County name (unique).
         *
         * @apiSuccess {Number} id Created county id.
         * @apiSuccess {String} name Created county name.
         *
         * @apiSuccessExample {json} Created:
         * HTTP/1.1 201 Created
         * { "id": 5, "name": "New County" }
         *
         * @apiError (400) ValidationError Returned when validation fails.
         */
        $request->validate([
            'name' => 'required|string|unique:counties,name'
        ]);

        $county = County::create($request->all());

        return response()->json($county, 201);
    }

    public function show(County $county)
    {
        /**
         * @api {get} /api/counties/:id Get county
         * @apiName GetCounty
         * @apiGroup County
         *
         * @apiParam {Number} id County unique ID.
         *
         * @apiSuccess {Number} id County id.
         * @apiSuccess {String} name County name.
         * @apiSuccess {Object[]} cities Cities belonging to the county, each with postalCodes.
         *
         * @apiSuccessExample {json} Success-Response:
         * HTTP/1.1 200 OK
         * { "id": 1, "name": "Budapest", "cities": [ { "id": 10, "name": "District 1", "postalCodes": ["1000"] } ] }
         */
        return $county->load('cities.postalCodes');
    }

    public function update(Request $request, County $county)
    {
        /**
         * @api {put} /api/counties/:id Update county
         * @apiName UpdateCounty
         * @apiGroup County
         *
         * @apiParam {Number} id County unique ID.
         * @apiParam {String} name County name (unique).
         *
         * @apiSuccess {Number} id County id.
         * @apiSuccess {String} name Updated county name.
         *
         * @apiError (400) ValidationError Returned when validation fails.
         */
        $request->validate([
            'name' => 'required|string|unique:counties,name,' . $county->id
        ]);

        $county->update($request->all());

        return response()->json($county);
    }

    public function destroy(County $county)
    {
        /**
         * @api {delete} /api/counties/:id Delete county
         * @apiName DeleteCounty
         * @apiGroup County
         *
         * @apiParam {Number} id County unique ID.
         *
         * @apiSuccess {String} message Success message.
         *
         * @apiSuccessExample {json} Success-Response:
         * HTTP/1.1 200 OK
         * { "message": "County deleted successfully" }
         */
        $county->delete();

        return response()->json(['message' => 'County deleted successfully']);
    }
}