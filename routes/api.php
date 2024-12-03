<?php

use App\Http\Controllers\BrandsController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\ShopsController;
use App\Http\Controllers\UserController;
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

Route::get('/roles',[RolesController::class,'index_api']);
Route::post('/create_seller',[UserController::class,'store_seller']);
//===================
Route::post('/login_seller',[UserController::class, 'login_sellers']);

Route::resource('users', UserController::class);
Route::middleware(['auth:sanctum'])->group(function () {
    Route::resource('shop',ShopsController::class);
    Route::post('shop/{id}',[ShopsController::class,'update']);
    Route::get('/logout_seller',[UserController::class, 'logout_seller']);
    Route::resource('brands',BrandsController::class);
    Route::resource('categories',CategoriesController::class);
});

