<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\CountyController;
use App\Http\Controllers\Api\PostalCodeController;

Route::get('/test', function () {
    return response()->json(['message' => 'API is working!']);
});

Route::get('/counties', [CountyController::class, 'index']);
Route::get('/counties/{id}', [CountyController::class, 'show']);
Route::post('/counties', [CountyController::class, 'store']);
Route::put('/counties/{id}', [CountyController::class, 'update']);
Route::delete('/counties/{id}', [CountyController::class, 'destroy']);


Route::get('/cities/search', [CityController::class, 'search']);
Route::get('/cities/first-letters/{county}', [CityController::class, 'getFirstLetters']);
Route::get('/cities/by-letter/{county}/{letter}', [CityController::class, 'getCitiesByLetter']);

Route::get('/cities', [CityController::class, 'index']);
Route::get('/cities/{id}', [CityController::class, 'show']);
Route::post('/cities', [CityController::class, 'store']);
Route::put('/cities/{id}', [CityController::class, 'update']);
Route::delete('/cities/{id}', [CityController::class, 'destroy']);

Route::get('/postal-codes', [PostalCodeController::class, 'index']);
Route::get('/postal-codes/{id}', [PostalCodeController::class, 'show']);
Route::post('/postal-codes', [PostalCodeController::class, 'store']);
Route::put('/postal-codes/{id}', [PostalCodeController::class, 'update']);
Route::delete('/postal-codes/{id}', [PostalCodeController::class, 'destroy']);