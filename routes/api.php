<?php

use App\Http\Controllers\CashierController;
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


Route::get('/cashiers', [CashierController::class, 'index']);
Route::post('/cashiers', [CashierController::class, 'store']);
Route::put('/cashiers/{id}', [CashierController::class, 'update']);