<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

use App\Http\Controllers\UtilisateurController;

Route::prefix('utilisateurs')->group(function () {
    Route::post('/', [UtilisateurController::class, 'create']);
    Route::put('/{id}', [UtilisateurController::class, 'update']);
    Route::post('/inscrire', [UtilisateurController::class, 'inscrireUtilisateur']);
    Route::post('/connexion', [UtilisateurController::class, 'connexion']);
    Route::get('/valider-compte', [UtilisateurController::class, 'validerCompte']);
    Route::post('/recuperer-compte', [UtilisateurController::class, 'recupererUtilisateur']);
    Route::get('/reinitialiser-tentative', [UtilisateurController::class, 'reinitialiserTentative']);
    Route::post('/valider-pin', [UtilisateurController::class, 'validationPin']);
});


Route::get('/test', function () {
    return response()->json(['message' => 'API fonctionne']);
});

use App\Http\Controllers\TokenController;

Route::post('/tokens/valider-token', [TokenController::class, 'validerToken']);
