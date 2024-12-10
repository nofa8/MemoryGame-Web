<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\MultiplayerGamesPlayedController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Specific for TAES
Route::get('/gamesTAES', [GameController::class, "indexTAES"]); // Get games
Route::post('/gamesTAES', [GameController::class, 'storeTAES']);




///////////////////////////////////
Route::post('/auth/login', [AuthController::class, "login"]);

Route::get('/boards', [BoardController::class, "index"]);
Route::get('/scoreboardGlobal', [GameController::class, 'indexScoreboardGlobal']);



Route::middleware(['auth:sanctum'])->group(function () {
    // Route::get('/users/me', function (Request $request) {return $request->user();});
    Route::get('/users/me', [UserController::class, 'showMe']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refreshtoken', [AuthController::class, 'refreshToken']);
    Route::get('/history', [GameController::class, 'indexHistory']);

    ///////////////////////////TAES
    Route::get('/historyTAES', [GameController::class, 'indexHistoryTAES']);

    ///////////////////////////////////////////////////////


    Route::get('/scoreboardPersonal', [GameController::class, 'indexScoreboardPersonal']);


    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::get('/transactions/{id}', [TransactionController::class, 'show']);
    Route::post('/transactions', [TransactionController::class, 'store']);

    Route::prefix('games')->group(function () {
        Route::post('/', [GameController::class, 'store']); // Create a new game
        Route::put('/{id}', [GameController::class, 'update']); // Update game status
    });

    Route::prefix('multiplayer-games')->group(function () {
        Route::post('/', [MultiplayerGamesPlayedController::class, 'store']);
        Route::patch('/{id}', [MultiplayerGamesPlayedController::class, 'update']);
    });
});
