<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

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
Route::post('/register', [UserController::class, 'register']);

// ROUTE TO LOGIN A REGISTERED PLAYER
Route::post('/login', [UserController::class, 'login']);

// ROUTE TO LOGOUT A LOGGED LAYER
Route::post('logout', [UserController::class, 'logout']);

// ROUTE TO UPDATE A REGISTER PLAYER NAME
Route::put('/user/{note}', [UserController::class, 'update'])->middleware('auth:api');

// ROUTE TO GET A PLAYER LIST
Route::get('/user', [UserController::class, 'index'])->middleware('auth:api');





