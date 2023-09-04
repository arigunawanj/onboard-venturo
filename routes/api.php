<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PromoController;
use App\Http\Controllers\Api\DiskonController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\VoucherController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\ReportSalesController;
use App\Http\Controllers\Api\ProductCategoryController;

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

Route::prefix('v1')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);

    Route::get('/roles', [RoleController::class, 'index']); // selesai
    Route::get('/roles/{id}', [RoleController::class, 'show']); // selesai
    Route::post('/roles', [RoleController::class, 'store']); // selesai
    Route::put('/roles', [RoleController::class, 'update']);
    Route::delete('/roles/{id}', [RoleController::class, 'destroy']); // Berhasil

    Route::get('/customers', [CustomerController::class, 'index']);
    Route::get('/customers/{id}', [CustomerController::class, 'show']);
    Route::post('/customers', [CustomerController::class, 'store']);
    Route::put('/customers', [CustomerController::class, 'update']);
    Route::delete('/customers/{id}', [CustomerController::class, 'destroy']);

    Route::get('/categories', [ProductCategoryController::class, 'index']);
    Route::get('/categories/{id}', [ProductCategoryController::class, 'show']);
    Route::post('/categories', [ProductCategoryController::class, 'store']);
    Route::put('/categories', [ProductCategoryController::class, 'update']);
    Route::delete('/categories/{id}', [ProductCategoryController::class, 'destroy']);

    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);

    Route::get('/promo', [PromoController::class, 'index']);
    Route::get('/promo/{id}', [PromoController::class, 'show']);
    Route::post('/promo', [PromoController::class, 'store']);
    Route::put('/promo', [PromoController::class, 'update']);
    Route::delete('/promo/{id}', [PromoController::class, 'destroy']);

    Route::get('/vouchers', [VoucherController::class, 'index']);
    Route::get('/vouchers/{id}', [VoucherController::class, 'show']);
    Route::post('/vouchers', [VoucherController::class, 'store']);
    Route::put('/vouchers', [VoucherController::class, 'update']);
    Route::delete('/vouchers/{id}', [VoucherController::class, 'destroy']);

    Route::get('/discount', [DiskonController::class, 'index']);
    Route::post('/discount', [DiskonController::class, 'store']);
    Route::put('/discount', [DiskonController::class, 'update']);
    Route::put('/discount/{id}', [DiskonController::class, 'show']);

    Route::get('/sale', [SaleController::class, 'index']);
    Route::post('/sale', [SaleController::class, 'store']);

    Route::get('/report/sales-promo', [ReportSalesController::class, 'viewSalesPromo']);
    Route::get('/report/sales-menu', [ReportSalesController::class, 'viewSalesCategories']);
    Route::get('/download/sales-category', [ReportSalesController::class, 'viewSalesCategories']);
    Route::get('/sale-transaction', [SaleController::class, 'saleTransaction']);


    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::get('/auth/profile', [AuthController::class, 'profile'])->middleware(['auth.api']);
});

Route::get('/', function () {
    return response()->failed(['Endpoint yang anda minta tidak tersedia']);
});

/**
 * Jika Frontend meminta request endpoint API yang tidak terdaftar
 * maka akan menampilkan HTTP 404
 */
Route::fallback(function () {
    return response()->failed(['Endpoint yang anda minta tidak tersedia']);
});
