<?php

use App\Events\PenjualanSampahNotification;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\PenjualanSampahController;
use App\Http\Controllers\SaldoController;
use App\Http\Controllers\SampahController;
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
        Route::get('profile', [AuthController::class, 'getPenggunaProfile']);
        Route::put('update-lokasi', [AuthController::class, 'updateLokasiPengguna']);
        Route::get('home', [PenggunaController::class, 'index']);
        Route::post('jual-sampah', [PenjualanSampahController::class, 'jualSampah']);
        Route::get('riwayat-pencairan-saldo', [SaldoController::class, 'getRiwayatPencairanSaldo']);
        Route::post('cairkan-saldo', [SaldoController::class, 'cairkanSaldo']);
        Route::get('riwayat-penjualan-sampah', [PenjualanSampahController::class, 'getRiwayatPenjualanSampah']);
        Route::get('current-penjualan', [PenjualanSampahController::class, 'getPenggunaCurrentPenjualan']);
    });
});

Route::group(['prefix' => 't'], function () {
    Route::post('register', [AuthController::class, 'registerTrashpicker']);
    Route::post('login', [AuthController::class, 'loginTrashpicker']);

    Route::group(['middleware' => ['auth:trashpicker', 'scopes:trashpicker']], function () {
        Route::get('profile', [AuthController::class, 'getTrashpickerProfile']);
        Route::put('update-lokasi', [AuthController::class, 'updateLokasiTrashpicker']);
        Route::get('daftar-permintaan', [PenjualanSampahController::class, 'getDaftarPermintaanPenjemputan']);
        Route::get('daftar-permintaan/{id}', [PenjualanSampahController::class, 'getDetailPermintaanPenjemputan']);
        Route::get('daftar-permintaan/{id}/{status}', [PenjualanSampahController::class, 'ubahStatusPenjualan']);
        Route::post('edit-data-sampah/{id}', [PenjualanSampahController::class, 'editDataSampah']);
        Route::get('update-status/{status}', [AuthController::class, 'updateStatusTrashpicker']);
        Route::get('current-penjemputan', [PenjualanSampahController::class, 'getTrashpickerCurrentPenjemputan']);
    });
});

Route::get('daftar-sampah', [SampahController::class, 'getSampah']);

Route::get('send-otp/{phoneNumber}', [AuthController::class, 'sendPhoneNumberOTP']);
Route::post('verify-otp', [AuthController::class, 'verifyPhoneNumberOTP']);
Route::post('lokasi', [AuthController::class, 'getLokasi']);
