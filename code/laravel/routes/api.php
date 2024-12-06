<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\MultiplayerGamesPlayedController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Specific for TAES
Route::get('/gamesTAES', [GameController::class, "index"]); // Get games
Route::post('/gamesTAES', [GameController::class, 'storeTAES']);




///////////////////////////////////
Route::post('/auth/login', [AuthController::class, "login"]);

Route::get('/boards', [BoardController::class, "index"]); // Get Boards








Route::middleware(['auth:sanctum'])->group(function () {
    // Route::get('/users/me', function (Request $request) {return $request->user();});
    Route::get('/users/me', [UserController::class, 'showMe']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refreshtoken', [AuthController::class, 'refreshToken']);


    Route::prefix('games')->group(function () {
        Route::post('/', [GameController::class, 'store']); // Create a new game
        Route::get('/{id}', [GameController::class, 'show']); // Get game details
        Route::put('/{id}', [GameController::class, 'update']); // Update game status
        Route::delete('/{id}', [GameController::class, 'delete']); // Delete a game
    });

    Route::prefix('multiplayer-games')->group(function () {
        Route::post('/', [MultiplayerGamesPlayedController::class, 'addPlayer']); // Add player to a multiplayer game
        Route::get('/{id}/players', [MultiplayerGamesPlayedController::class, 'listPlayers']); // Get players in a multiplayer game
    });

});
