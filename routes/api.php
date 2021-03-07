<?php

use App\Http\Controllers\CategoryController;
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
Route::prefix('/auth')->name('auth.public.')->group(function () {
    Route::post('/register', [PassportAuthController::class, 'register']);
    Route::post('/login', [PassportAuthController::class, 'login']);
});

// Unauthenticated routes
Route::get('/categories/all', [CategoryController::class, 'all'])->name('all');

Route::middleware('auth:api')->group(function () {
    // Properties auth routes
    Route::prefix('properties')->name('properties.')->group(function () {
        Route::post('/create', [PropertyController::class, 'create'])->name('create');
        Route::get('/{id}/show', [PropertyController::class, 'show'])->where('id', '[0-9]+')->name('show');
        Route::put('/{id}/update', [PropertyController::class, 'update'])->where('id', '[0-9]+')->name('update');
        Route::get('/{id}/setActive/{status}', [PropertyController::class, 'setActive'])->where(['id' => '[0-9]+', 'status' => '[0-1]{1}'])->name('setActive');
        Route::get('/{id}/owner', [PropertyController::class, 'owner'])->where('id', '[0-9]+')->name('owner');
        Route::get('/all', [PropertyController::class, 'all'])->name('all');
    });
    // Roles auth routes
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/{id}', [RoleController::class, 'userRole'])->where('id', '[0-9]+')->name('user');
        Route::get('/myRole', [RoleController::class, 'myRole'])->name('mine');
        Route::get('/all', [RoleController::class, 'all'])->name('all');
    });
    // Categories auth routes
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::post('/create', [CategoryController::class, 'create'])->name('create');
    });
    // User auth routes
    Route::prefix('/auth')->name('auth.private')->group(function () {
        Route::get('logout', [PassportAuthController::class, 'logout'])->name('logout');
        Route::get('user', [PassportAuthController::class, 'user'])->name('user');
    });
});
