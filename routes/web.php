<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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

Route::get('/', function () {
    return redirect('login');
});

Auth::routes();
Route::get('admincp/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('admincp-login');

Route::group(['middleware' => 'auth'], function () {
    Route::get('admincp', [App\Http\Controllers\Admin\DashboardController::class, 'dashboard']);

    Route::get('admincp/users', [App\Http\Controllers\Admin\UsersController::class, 'index']);
    Route::get('admincp/users/create', [App\Http\Controllers\Admin\UsersController::class, 'create']);
    Route::post('admincp/users/store', [App\Http\Controllers\Admin\UsersController::class, 'store']);
    Route::get('admincp/users/edit/{id}', [App\Http\Controllers\Admin\UsersController::class, 'show']);
    Route::post('admincp/users/edit/{id}', [App\Http\Controllers\Admin\UsersController::class, 'update']);
    Route::get('admincp/users/delete/{id}', [App\Http\Controllers\Admin\UsersController::class, 'delete']);

    Route::get('admincp/employee', [App\Http\Controllers\Admin\EmployeeController::class, 'index']);
    Route::get('admincp/employee/edit/{id}', [App\Http\Controllers\Admin\EmployeeController::class, 'show']);
    Route::get('admincp/employee/edit/{id}/export', [App\Http\Controllers\Admin\EmployeeController::class, 'export']);
    Route::post('admincp/employee/edit/{id}', [App\Http\Controllers\Admin\EmployeeController::class, 'update']);
    Route::post('admincp/employee/pay/{id}', [App\Http\Controllers\Admin\EmployeeController::class, 'pay']);

    Route::get('admincp/clocks', [App\Http\Controllers\Admin\ClocksController::class, 'index']);
    Route::get('admincp/clocks/create', [App\Http\Controllers\Admin\ClocksController::class, 'create']);
    Route::post('admincp/clocks/store', [App\Http\Controllers\Admin\ClocksController::class, 'store']);
    Route::get('admincp/clocks/get-event', [App\Http\Controllers\Admin\ClocksController::class, 'event']);
    Route::get('admincp/clocks/get-user', [App\Http\Controllers\Admin\ClocksController::class, 'user']);
    Route::get('admincp/clocks/edit/{id}', [App\Http\Controllers\Admin\ClocksController::class, 'show']);
    Route::post('admincp/clocks/edit/{id}', [App\Http\Controllers\Admin\ClocksController::class, 'update']);
    Route::get('admincp/clocks/delete/{id}', [App\Http\Controllers\Admin\ClocksController::class, 'delete']);
    Route::get('admincp/clocks/export', [App\Http\Controllers\Admin\ClocksController::class, 'export']);

    Route::post('admincp/receipt/edit/{id}', [App\Http\Controllers\Admin\ReceiptsController::class, 'update']);

    Route::get('admincp/breaks/edit/{id}', [App\Http\Controllers\Admin\BreaksController::class, 'show']);
    Route::post('admincp/breaks/edit/{id}', [App\Http\Controllers\Admin\BreaksController::class, 'update']);

    Route::get('admincp/change-clocks', [App\Http\Controllers\Admin\ChangeClocksController::class, 'index']);
    Route::get('admincp/change-clocks/edit/{id}', [App\Http\Controllers\Admin\ChangeClocksController::class, 'show']);
    Route::post('admincp/change-clocks/edit/{id}', [App\Http\Controllers\Admin\ChangeClocksController::class, 'update']);


});

Route::get('/clear', function() {
    // Artisan::call('optimize');
    Artisan::call('config:cache');
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('config:clear');
    // Artisan::call('route:clear');
    // exec('rm -f ' . storage_path('logs/laravel.log'));
    dd("Cache is cleared");
})->name('clear.cache');
