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
Route::post('auth/register', [AuthController::class, "register"]);

Route::get('/boards', [BoardController::class, "index"]); // Get Boards

Route::middleware(['auth:sanctum'])->group(function () {
    // Route::get('/users/me', function (Request $request) {return $request->user();});
    Route::put('/users/me', [UserController::class, 'updateProfile']); 
    Route::get('/users/me', [UserController::class, 'showMe']);
    Route::delete('/users/me', [UserController::class, 'deleteProfile']);
    
    Route::get('/users', [UserController::class, 'indexAll']);
    Route::post('users/image', [UserController::class, 'updateProfilePicture']);
    Route::patch('/users/{nickname}', [UserController::class, 'blockOrUnblockAccount']);
    

    Route::get('/history', [GameController::class, 'indexHistory']);

    Route::patch('/auth/admin/{nickname}', [UserController::class, 'restore']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refreshtoken', [AuthController::class, 'refreshToken']);
    Route::post('/auth/admin', [AuthController::class, 'createAdmin']);
    Route::delete('/auth/admin/{nickname}', [UserController::class, 'deleteUserAsAdmin']);

    Route::prefix('games')->group(function () {
        Route::post('/', [GameController::class, 'store']); // Create a new game
        Route::get('/{id}', [GameController::class, 'show']); // Get game details
        Route::put('/{id}', [GameController::class, 'update']); // Update game status
        Route::delete('/{id}', [GameController::class, 'delete']); // Delete a game
    });

    Route::prefix('multiplayer-games')->group(function () {
        Route::post('/', [MultiplayerGamesPlayedController::class, 'store']); 
        Route::patch('/{id}', [MultiplayerGamesPlayedController::class, 'update']); 
        Route::get('/{id}/players', [MultiplayerGamesPlayedController::class, 'listPlayers']); // Get players in a multiplayer game
    });

});
