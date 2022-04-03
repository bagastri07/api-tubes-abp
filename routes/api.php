<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\OwnerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// Auth Routes For Cashier
Route::post('/auth/login-cashier', [AuthController::class, 'loginCashier']);
Route::post('/auth/login-owner', [AuthController::class, 'loginOwner']);

// Owner ROutes
Route::post('/owners', [OwnerController::class, 'store']);
Route::get('/owners', [OwnerController::class, 'show'])->middleware(['auth:sanctum', 'ability:owner']);

// Cashier Routes
Route::get('/cashiers/current', [CashierController::class, 'showCurrent'])->middleware(['auth:sanctum', 'ability:cashier']);
Route::get('/cashiers', [CashierController::class, 'index'])->middleware(['auth:sanctum', 'ability:owner']);
Route::post('/cashiers', [CashierController::class, 'store'])->middleware(['auth:sanctum', 'ability:owner']);
Route::put('/cashiers/{id}', [CashierController::class, 'update'])->middleware(['auth:sanctum', 'ability:owner']);
Route::get('/cashiers/{id}', [CashierController::class, 'show'])->middleware(['auth:sanctum', 'ability:owner']);
Route::delete('/cashiers/{id}', [CashierController::class, 'destroy'])->middleware(['auth:sanctum', 'ability:owner']);