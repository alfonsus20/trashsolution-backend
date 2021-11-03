<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PenggunaController;
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


Route::group(['prefix' => 'p'], function () {
    Route::post('register', [AuthController::class, 'registerPengguna']);
    Route::post('login', [AuthController::class, 'loginPengguna']);

    Route::group(['middleware' => ['auth:pengguna', 'scopes:pengguna']], function () {
        Route::get('home', [PenggunaController::class, 'index']);
    });
});

Route::group(['prefix' => 't'], function () {
    Route::post('register', [AuthController::class, 'registerTrashpicker']);
    Route::post('login', [AuthController::class, 'loginTrashpicker']);
});
