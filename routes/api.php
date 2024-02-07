<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RollController;

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

    // ROUTE TO REGISTER A PLAYER
Route::post('/players', [UserController::class, 'register']); // Grant permission to: All

    // ROUTE TO LOGIN A REGISTERED USER (VALID FOR ADMIN & PLAYERS)
Route::post('/login', [UserController::class, 'login']); // Grant permission to: All

    // ROUTES PROTECTED WITH PASSPORT
Route::middleware('auth:api')->group(function () {

        // ROUTE TO LOGOUT A LOGGED LAYER
    Route::post('/logout', [UserController::class, 'logout']); // Grant permission to: All

        // ROUTE TO UPDATE THE NAME OF A REGISTERED PLAYER/USER (VALID FOR ADMIN & PLAYERS)
    Route::put('/players/{id}', [UserController::class, 'update']); // Grant permission to: All

        // ROUTE TO GET THE ALL PLAYER LIST WITH WIN RATE
    Route::get('/players', [UserController::class, 'index'])->middleware('can:players.index'); // Grant permission to: Admin

        // ROUTE TO GET THE PLAYER RANKING ORDERED BY DESCENDING WIN RATE
    Route::get('/players/ranking', [UserController::class, 'ranking'])->middleware('can:players.ranking'); // Grant permission to: Admin

        // ROUTE TO GET THE PLAYER WITH HIGHEST RANKING
    Route::get('/players/ranking/winner', [UserController::class, 'winner'])->middleware('can:players.winner'); // Grant permission to: Admin

        // ROUTE TO GET THE PLAYER WITH LOWEST RANKING
    Route::get('/players/ranking/loser', [UserController::class, 'loser'])->middleware('can:players.loser'); // Grant permission to: Admin

        // ROUTE TO EXECUTE A ROLL DICE OF A SPECIFIC PLAYER
    Route::post('/players/{id}/games', [RollController::class, 'store'])->middleware('can:players.store'); // Grant permission to: Player

        // ROUTE TO DELETE ALL ROLLS OF A SPECIFIC PLAYER
    Route::delete('/players/{id}/games', [RollController::class, 'destroy'])->middleware('can:players.destroy'); // Grant permission to: Player

        // ROUTE TO SHOW ALL ROLLS OF A SPECIFIC PLAYER
    Route::get('/players/{id}/games', [RollController::class, 'show'])->middleware('can:players.show'); // Grant permission to: Player

        // ROUTE TO SHOW THE WIN RATE OF A SPECIFIC PLAYER
    Route::get('/players/{id}/average', [RollController::class, 'getWinRate'])->middleware('can:players.getWinRate'); // Grant permission to: Player

});





