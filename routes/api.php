<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\FeatureController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\PassportAuthController;
use App\Http\Controllers\PriceHistoryController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\RangePriceController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\TourController;
use App\Http\Controllers\UserController;
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
    Route::get('/index', [PropertyController::class, 'index'])->name('index');
    Route::get('/{id}/show', [PropertyController::class, 'show'])->where('id', '[0-9]+')->name('showById');
    Route::get('/showByFilter', [PropertyController::class, 'showByFilter'])->name('showByFilter');
    Route::get('/last', [PropertyController::class, 'last'])->name('last');
    Route::get('/active', [PropertyController::class, 'active'])->name('active');
    Route::get('/lastActive', [PropertyController::class, 'lastActive'])->name('lastActive');
});

Route::prefix('categories')->name('categories.')->group(function () {
    Route::get('/index', [CategoryController::class, 'index'])->name('index');
    Route::get('/{id}/show', [CategoryController::class, 'show'])->where('id', '[0-9]+')->name('show');
    Route::get('/name/{name}/properties', [CategoryController::class, 'getPropertiesByCategoryName'])->where('name', '[a-zA-Z]+')->name('propertiesByCategoryName');
    Route::get('/id/{id}/properties', [CategoryController::class, 'getPropertiesByCategoryId'])->where('id', '[0-9]+')->name('propertiesByCategoryId');
});

Route::prefix('countries')->name('countries.')->group(function () {
    Route::get('/index', [CountryController::class, 'index'])->name('index');
    Route::get('/{id}/show', [CountryController::class, 'show'])->where('id', '[0-9]+')->name('show');
});

Route::prefix('cities')->name('cities.')->group(function () {
    Route::get('/index', [CityController::class, 'index'])->name('index');
    Route::get('/{id}/show', [CityController::class, 'show'])->where('id', '[0-9]+')->name('show');
});

Route::prefix('features')->name('features.')->group(function () {
    Route::get('/index', [FeatureController::class, 'index'])->name('index');
    Route::get('/{id}/show', [FeatureController::class, 'show'])->where('id', '[0-9]+')->name('show');
});

Route::get('rangePrice/index', [RangePriceController::class, 'index'])->name('rangePrice');

Route::get('contact/send', [ContactController::class, 'send'])->name('contact.send');

Route::prefix('image')->name('image.')->group(function () {
    Route::get('/categories/{id}', [FileController::class, 'categories'])->where('id', '[a-zA-Z0-9]+[.][a-z]+')->name('categories');
    Route::get('/properties/{id}', [FileController::class, 'properties'])->where('id', '[a-zA-Z0-9]+[.][a-z]+')->name('properties');
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
        Route::put('/update', [PropertyController::class, 'update'])->name('update');
        Route::get('/{id}/setActive/{status}', [PropertyController::class, 'setActive'])->where(['id' => '[0-9]+', 'status' => '[0-1]{1}'])->name('setActive');
        Route::get('/{id}/owner', [PropertyController::class, 'owner'])->where('id', '[0-9]+')->name('owner');
        Route::delete('/{id}/delete', [PropertyController::class, 'delete'])->where('id', '[0-9]+')->name('delete');
    });
    // Roles auth routes
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/{id}', [RoleController::class, 'userRole'])->where('id', '[0-9]+')->name('user');
        Route::get('/myRole', [RoleController::class, 'myRole'])->name('mine');
        Route::get('/all', [RoleController::class, 'all'])->name('all');
    });
    // Categories auth routes
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::post('/create', [CategoryController::class, 'store'])->name('store');
        Route::put('/update', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{id}/delete', [CategoryController::class, 'delete'])->where('id', '[0-9]+')->name('delete');
    });
    // User auth routes
    Route::prefix('/auth')->name('auth.private')->group(function () {
        Route::get('logout', [PassportAuthController::class, 'logout'])->name('logout');
        Route::get('user', [PassportAuthController::class, 'user'])->name('user');
    });
    // Countries auth routes
    Route::prefix('countries')->name('countries.')->group(function () {
        Route::post('/store', [CountryController::class, 'store'])->name('store');
        Route::put('/update', [CountryController::class, 'update'])->name('update');
        Route::delete('/{id}/delete', [CountryController::class, 'destroy'])->where('id', '[0-9]+')->name('delete');
    });
    //Cities auth routes
    Route::prefix('cities')->name('cities.')->group(function () {
        Route::post('/store', [CityController::class, 'store'])->name('store');
        Route::put('/update', [CityController::class, 'update'])->name('update');
        Route::delete('/{id}/delete', [CityController::class, 'destroy'])->where('id', '[0-9]+')->name('delete');
    });
    // Price History auth routes
    Route::prefix('priceHistory')->name('priceHistory.')->group(function () {
        Route::get('/index', [PriceHistoryController::class, 'index'])->name('index');
        Route::get('/{propertyId}/show', [PriceHistoryController::class, 'show'])->where('propertyId', '[0-9]+')->name('show');
        Route::post('/store', [PriceHistoryController::class, 'store'])->name('store');
    });
    // Tours auth routes
    Route::prefix('tours')->name('tours.')->group(function () {
        Route::get('/index', [TourController::class, 'index'])->name('index');
        Route::get('/show', [TourController::class, 'show'])->name('show');
        Route::get('/{hashId}/showByHashId', [TourController::class, 'showByHashId'])->where('hash', '[0-9a-zA-Z]+')->name('hashId.show');
        Route::get('/property/{propertyId}/show', [TourController::class, 'showByPropertyId'])->where('propertyId', '[0-9]+')->name('property.show');
        Route::post('/store', [TourController::class, 'store'])->name('store');
        Route::put('/update', [TourController::class, 'update'])->name('update');
        Route::delete('/{hashId}/delete', [TourController::class, 'destroy'])->where('hashId', '[0-9a-zA-Z]+')->name('delete');
    });
    // Sales auth routes
    Route::prefix('sales')->name('sales.')->group(function () {
        Route::post('/store', [SaleController::class, 'store'])->name('store');
        Route::get('/index', [SaleController::class, 'index'])->name('index');
        Route::get('/{hashId}/showByHashId', [SaleController::class, 'showByHashId'])->where('hash', '[0-9a-zA-Z]+')->name('hashId.show');
        Route::get('/actualYear', [SaleController::class, 'getSalesOfActualYear'])->name('actualYear');
    });
    // Features auth routes
    Route::prefix('features')->name('features.')->group(function () {
        Route::post('/create', [FeatureController::class, 'store'])->name('create');
        Route::put('/update', [FeatureController::class, 'update'])->name('update');
        Route::delete('/{id}/delete', [FeatureController::class, 'destroy'])->where('id', '[0-9]+')->name('delete');
    });
    // Users auth routes
    Route::prefix('users')->name('users.')->group(function () {
        Route::put('/update', [UserController::class, 'update'])->name('update');
        Route::get('/owners', [UserController::class, 'owners'])->name('owners');
    });
});
/*-----------------------------------------------------------------------------------*/
/*---------------------------------END AUTH ROUTES-----------------------------------*/
/*-----------------------------------------------------------------------------------*/
