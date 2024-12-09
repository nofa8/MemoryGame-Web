<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/auth/login', [AuthController::class, "login"]);

Route::get('/boards', [BoardController::class, "index"]);
Route::get('/games', [GameController::class, "indexFinished"]);
Route::post('/games', [GameController::class, 'store']);

Route::middleware(['auth:sanctum'])->group(function () {
    // Route::get('/users/me', function (Request $request) {return $request->user();});
    Route::get('/users/me', [UserController::class, 'showMe']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refreshtoken', [AuthController::class, 'refreshToken']);
    Route::get('/history', [GameController::class, 'indexHistory']);


    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::get('/transactions/{id}', [TransactionController::class, 'show']);
    Route::post('/transactions', [TransactionController::class, 'store']);




});