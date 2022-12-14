<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\UserController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

/**
 * Auth users route
 */
Route::middleware(['api.request', 'api.response', 'api', 'trimStrings'])
    ->prefix('v1/user/')
    ->controller(AuthController::class)
    ->group(function () {
        Route::post('create', 'register')->name('register');
        Route::post('login', 'login')->name('login');
        Route::middleware(['jwt.verify'])->get('logout', 'logout')->name('logout');
        Route::post('forgot-password', 'forgotPassword')->name('forgot-password');
        Route::post('reset-password-token', 'resetPasswordToken')->name('reset-password-token');
    });

/**
 * users Routes
 */
Route::middleware(['api.request', 'jwt.verify', 'api.response'])
    ->prefix('v1/')
    ->controller(UserController::class)
    ->group(function () {
        Route::get('user', 'show')->name('view-user-account');
        Route::delete('user/{$uuid}', 'destroy')->name('delete-user-account'); //role based
        Route::get('user/orders', 'orders')->name('list-user-orders'); //need orders table
        Route::put('user/edit', 'update')->name('update-user');
    });

/**
 * products routes
 */
Route::middleware(['api.request', 'api.response'])
    ->prefix('v1/')
    ->controller(ProductController::class)
    ->group(function () {
        Route::get('products', 'index')->name('products.index');
        Route::get('product/{uuid}', 'show')->name('products.show');
        Route::middleware(['jwt.verify'])->post('product/create', 'store')->name('products.store');
        Route::middleware(['jwt.verify'])->put('product/{uuid}', 'update')->name('products.update');
        Route::middleware(['jwt.verify'])->delete('product/{uuid}', 'destroy')->name('products.delete');
    });
