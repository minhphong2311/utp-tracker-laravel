<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\NewPasswordController;
use App\Http\Controllers\Api\ClockController;
use App\Http\Controllers\Api\BreakController;
use App\Http\Controllers\Api\HistoryController;
use App\Http\Controllers\Api\ChangeClockController;
use App\Http\Controllers\Api\ReceiptController;

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
Route::group(['middleware' => 'auth_secret_key'], function() {
    Route::post('auth/register',[AuthController::class,'register']);
    Route::post('auth/login',[AuthController::class,'login']);
    Route::post('auth/forgot-password',[NewPasswordController::class,'forgotPassword']);
    Route::post('auth/reset-password',[NewPasswordController::class,'resetPassword']);
});
Route::middleware('auth:sanctum')->group(function () {
    Route::get('auth/me',[AuthController::class,'getMe']);
    Route::post('auth/update-profile',[AuthController::class,'updateProfile']);
    Route::post('auth/change-password',[AuthController::class,'changePassword']);
    Route::get('auth/logout', [AuthController::class, 'logout']);

    Route::post('clockin',[ClockController::class,'clockIn']);
    Route::post('clockout',[ClockController::class,'clockOut']);

    Route::post('startbreak',[BreakController::class,'startBreak']);
    Route::post('endbreak',[BreakController::class,'endBreak']);

    Route::get('history/{user_wp_id}/{day}',[HistoryController::class,'historyDay']);
    Route::get('history-week/{user_wp_id}/{startday}/{endday}',[HistoryController::class,'historyWeek']);
    Route::get('history-month/{user_wp_id}/{year}-{month}',[HistoryController::class,'historyMonth']);

    Route::post('change-clock',[ChangeClockController::class,'changeClock']);
    Route::post('change-clock/{id}',[ChangeClockController::class,'changeClockCancell']);

    Route::get('receipts/{clock_id}',[ReceiptController::class,'index']);
    Route::post('receipt',[ReceiptController::class,'receipt']);

    // Route::post('clock-offline',[OfflineController::class,'clockOffline']);
    // Route::post('break-offline',[OfflineController::class,'breakOffline']);
});


