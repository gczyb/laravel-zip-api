<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\CountyController;
use App\Http\Controllers\Api\PostalCodeController;
use App\Http\Controllers\Api\AuthController; // <--- EZT PÓTOLTUK!

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// --- PUBLIKUS ÚTVONALAK (Nem kell bejelentkezés) ---

Route::get('/test', function () {
    return response()->json(['message' => 'API is working!']);
});

// Bejelentkezés és Regisztráció (EZ HIÁNYZOTT!)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Adatok olvasása (Index és Show) maradhat publikus, ha akarod
Route::get('/counties', [CountyController::class, 'index']);
Route::get('/counties/{id}', [CountyController::class, 'show']);
Route::get('/cities', [CityController::class, 'index']);
Route::get('/cities/{id}', [CityController::class, 'show']);
Route::get('/cities/search', [CityController::class, 'search']);
Route::get('/cities/first-letters/{county}', [CityController::class, 'getFirstLetters']);
Route::get('/cities/by-letter/{county}/{letter}', [CityController::class, 'getCitiesByLetter']);
Route::get('/postal-codes', [PostalCodeController::class, 'index']);
Route::get('/postal-codes/{id}', [PostalCodeController::class, 'show']);


// --- VÉDETT ÚTVONALAK (Csak bejelentkezéssel) ---
// Minden módosítást (létrehozás, szerkesztés, törlés, kijelentkezés) ide teszünk
Route::middleware('auth:sanctum')->group(function () {

    // Kijelentkezés
    Route::post('/logout', [AuthController::class, 'logout']);

    // Megyék módosítása
    Route::post('/counties', [CountyController::class, 'store']);
    Route::put('/counties/{id}', [CountyController::class, 'update']);
    Route::delete('/counties/{id}', [CountyController::class, 'destroy']);

    // Városok módosítása
    Route::post('/cities', [CityController::class, 'store']);
    Route::put('/cities/{id}', [CityController::class, 'update']);
    Route::delete('/cities/{id}', [CityController::class, 'destroy']);

    // Irányítószámok módosítása
    Route::post('/postal-codes', [PostalCodeController::class, 'store']);
    Route::put('/postal-codes/{id}', [PostalCodeController::class, 'update']);
    Route::delete('/postal-codes/{id}', [PostalCodeController::class, 'destroy']);
    
    // Felhasználó lekérése
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});