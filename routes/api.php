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
Route::post('/players', [UserController::class, 'register']); // NO SE PROTEGE => ESTÁ OK

// ROUTE TO LOGIN A REGISTERED PLAYER
Route::post('/login', [UserController::class, 'login']); // NO SE PROTEGE => ESTÁ OK

// ROUTE TO LOGOUT A LOGGED LAYER
Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:api');

// ROUTE TO UPDATE THE NAME OF A REGISTERED PLAYER
Route::put('/players/{id}', [UserController::class, 'update'])->middleware('auth:api'); // PROTEGIDA => ESTÁ OK

// ROUTE TO GET THE ALL PLAYER LIST WITH WIN RATE
Route::get('/players', [UserController::class, 'index'])->middleware('auth:api'); // SOLO PERMITIR A ROLL ADMIN => PENDIENTE

// ROUTE TO GET THE PLAYER RANKING ORDERED BY DESCENDING WIN RATE (FROM HIGHEST TO LOWEST)
Route::get('/players/ranking', [UserController::class, 'ranking'])->middleware('auth:api'); // SOLO PERMITIR A ROLL ADMIN => PENDIENTE

// ROUTE TO EXECUTE A ROLL DICE OF A SPECIFIC PLAYER
Route::post('/players/{id}/games', [RollController::class, 'store'])->middleware('auth:api'); // SOLO PERMITIR A ROLL PLAYER => PENDIENTE

// ROUTE TO DELETE ALL ROLLS OF A SPECIFIC PLAYER
Route::delete('/players/{id}/games', [RollController::class, 'destroy'])->middleware('auth:api'); // SOLO PERMITIR A ROLL PLAYER => PENDIENTE

// ROUTE TO SHOW ALL ROLLS OF A SPECIFIC PLAYER
Route::get('/players/{id}/games', [RollController::class, 'show'])->middleware('auth:api'); // SOLO PERMITIR A ROLL PLAYER => PENDIENTE

// ROUTE TO SHOW THE WIN RATE OF A SPECIFIC PLAYER
Route::get('/players/{id}/average', [RollController::class, 'getWinRate'])->middleware('auth:api'); // SOLO PERMITIR A ROLL PLAYER => PENDIENTE

