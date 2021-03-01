<?php

use App\Http\Controllers\PassportAuthController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\RoleController;
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
Route::post('register', [PassportAuthController::class, 'register']);
Route::post('login', [PassportAuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::prefix('properties')->name('properties.')->group(function () {
        Route::post('/create', [PropertyController::class, 'store'])->name('create');
        Route::get('/{id}/show', [PropertyController::class, 'show'])->where('id', '[0-9]+')->name('show');
        Route::get('/{id}/owner', [PropertyController::class, 'owner'])->where('id', '[0-9]+')->name('owner');
        Route::get('/all', [PropertyController::class, 'all'])->name('all');
    });
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/{id}', [RoleController::class, 'userRole'])->where('id', '[0-9]+')->name('user');
        Route::get('/myRole', [RoleController::class, 'myRole'])->name('mine');
        Route::get('/all', [RoleController::class, 'all'])->name('all');
    });
});
