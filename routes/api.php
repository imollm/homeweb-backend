<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CountryController;
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

/*-----------------------------------------------------------------------------------*/
/*-------------------------------------PUBLIC ROUTES---------------------------------*/
/*-----------------------------------------------------------------------------------*/
Route::prefix('/auth')->name('auth.public.')->group(function () {
    Route::post('/register', [PassportAuthController::class, 'register']);
    Route::post('/login', [PassportAuthController::class, 'login']);
});

Route::prefix('properties')->name('properties.')->group(function () {
    Route::get('/all', [PropertyController::class, 'all'])->name('all');
    Route::get('/{id}/show', [PropertyController::class, 'show'])->where('id', '[0-9]+')->name('show');
    Route::post('/showByFilter', [PropertyController::class, 'showByFilter'])->name('showByFilter');
});

Route::prefix('categories')->name('categories.')->group(function () {
    Route::get('/all', [CategoryController::class, 'all'])->name('all');
    Route::get('/{id}/show', [CategoryController::class, 'show'])->where('id', '[0-9]+')->name('show');
});

Route::prefix('countries')->name('countries.')->group(function () {
    Route::get('/index', [CountryController::class, 'index'])->name('index');
    Route::get('/{id}/show', [CountryController::class, 'show'])->where('id', '[0-9]+')->name('show');
});
/*-----------------------------------------------------------------------------------*/
/*---------------------------------END PUBLIC ROUTES---------------------------------*/
/*-----------------------------------------------------------------------------------*/



/*-----------------------------------------------------------------------------------*/
/*-------------------------------------AUTH ROUTES-----------------------------------*/
/*-----------------------------------------------------------------------------------*/

Route::middleware('auth:api')->group(function () {
    // Properties auth routes
    Route::prefix('properties')->name('properties.')->group(function () {
        Route::post('/create', [PropertyController::class, 'create'])->name('create');
        Route::put('/{id}/update', [PropertyController::class, 'update'])->where('id', '[0-9]+')->name('update');
        Route::get('/{id}/setActive/{status}', [PropertyController::class, 'setActive'])->where(['id' => '[0-9]+', 'status' => '[0-1]{1}'])->name('setActive');
        Route::get('/{id}/owner', [PropertyController::class, 'owner'])->where('id', '[0-9]+')->name('owner');
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
        Route::put('/{id}/update', [CategoryController::class, 'update'])->where('id', '[0-9]+')->name('update');
        Route::delete('/{id}/delete', [CategoryController::class, 'delete'])->where('id', '[0-9]+')->name('delete');
    });
    // User auth routes
    Route::prefix('/auth')->name('auth.private')->group(function () {
        Route::get('logout', [PassportAuthController::class, 'logout'])->name('logout');
        Route::get('user', [PassportAuthController::class, 'user'])->name('user');
    });

    Route::prefix('countries')->name('countries.')->group(function () {
        Route::post('/store', [CountryController::class, 'store'])->name('store');
        Route::put('/update', [CountryController::class, 'update'])->name('update');
        Route::delete('/{id}/delete', [CountryController::class, 'destroy'])->where('id', '[0-9]+')->name('delete');
    });
});
/*-----------------------------------------------------------------------------------*/
/*---------------------------------END AUTH ROUTES-----------------------------------*/
/*-----------------------------------------------------------------------------------*/
