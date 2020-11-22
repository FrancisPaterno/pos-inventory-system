<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ItemController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [RegisterController::class, 'register']);

Route::post('/login', [LoginController::class, 'login']);

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth:sanctum');

Route::resource('/itemcategory', 'ItemCategoryController');

Route::resource('/itembrand', 'ItemBrandController');

Route::resource('/itemunit', 'ItemUnitController');

Route::resource('/itemstatus', 'ItemStatusController');

Route::resource('/item', 'ItemController');

Route::get('/itemImage', [ItemController::class, 'getItemImage']);

Route::resource('/customer', 'CustomerController');

Route::resource('/warehouse', 'WarehouseController');

Route::resource('/supplier', 'SupplierController');

Route::resource('/stockHeaders', 'StockHeadersController');
