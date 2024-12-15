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
Route::post('auth/register', [AuthController::class, "register"]);


Route::get('/boards', [BoardController::class, "index"]);
Route::get('/scoreboardGlobal', [GameController::class, 'indexScoreboardGlobal']);



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

    ///////////////////////////TAES
    Route::get('/historyTAES', [GameController::class, 'indexHistoryTAES']);
    Route::get('/gamesPersonalTAES', [GameController::class, 'indexScoreboardPersonalTAES']);
    Route::post('/gamesTAES/hintNboard', [GameController::class, 'storeHintANDBoardTAES']);
    Route::post('gamesTAES/bonusBrainCoins', [GameController::class, 'bonusBrainCoinsTAES']);
    
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
