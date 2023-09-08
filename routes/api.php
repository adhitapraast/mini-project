<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'v1', 'middleware' => 'access-role'], function () {


    Route::group(['prefix' => 'products'], function () {
        Route::get('/list', [App\Http\Controllers\ProductsController::class, 'list']);
        Route::get('/show/{id}', [App\Http\Controllers\ProductsController::class, 'show']);
        Route::post('/store', [App\Http\Controllers\ProductsController::class, 'store']);
        Route::patch('/update/{id}', [App\Http\Controllers\ProductsController::class, 'update']);
        Route::delete('/delete/{id}', [App\Http\Controllers\ProductsController::class, 'destroy']);
    });
    
    Route::group(['prefix' => 'carts'], function () {
        Route::get('/list', [App\Http\Controllers\CartsController::class, 'list']);
        Route::post('/add/{productId}', [App\Http\Controllers\CartsController::class, 'addToCart']);
        Route::post('/checkout', [App\Http\Controllers\CartsController::class, 'checkout']);
        Route::patch('/update/{id}', [App\Http\Controllers\CartsController::class, 'updateCart']);
        Route::delete('/remove/{id}', [App\Http\Controllers\CartsController::class, 'removeFromCart']);
    });

    Route::group(['prefix' => 'orders'], function () {
        Route::get('/list/{status?}', [App\Http\Controllers\OrdersController::class, 'list']);
        Route::get('/show/{id}', [App\Http\Controllers\OrdersController::class, 'show']);
    });

});