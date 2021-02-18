<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Balance\BalanceController;
use App\Http\Controllers\Usuario\UsuarioController;
use App\Http\Controllers\Transactions\TransactionController;

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

Route::post('/register', [UsuarioController::class, 'registro']);

/**
 * 
 * Qeue notifications
 * 
 */

Route::post('/transfer', [TransactionController::class, 'transaction']);
Route::post('/addBalance', [BalanceController::class, 'addBalance']);
