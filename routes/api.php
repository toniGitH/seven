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

// RUTA PARA REGISTRAR UN USUARIO:
Route::post('/register', [UserController::class, 'register']);

// RUTA PARA LOGEAR A UN USUARIO YA REGISTRADO:
Route::post('/login', [UserController::class, 'login']);

// RUTA PARA DESLOGEAR A UN USUARIO LOGEADO
Route::post('logout', [UserController::class, 'logout']);

// RUTA PARA ACTUALIZAR EL NOMBRE DEL USUARIO
Route::put('/user/{id}', [UserController::class, 'update']);
/* Route::put('/user/{note}', [UserController::class, 'update'])->middleware('auth:api'); */


