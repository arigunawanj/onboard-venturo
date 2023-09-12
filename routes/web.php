<?php

use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Web\AppController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\TestsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/log', [AppController::class, 'log'])->name('log');
Route::get('/test', [SaleController::class, 'testingSales']);

Route::get('/{any}', [AppController::class, 'index'])->where('any', '^(?!print|excel|pdf|assets|storage).*$');
