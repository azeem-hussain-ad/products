<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

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


Route::post('/import-products', [ProductController::class, 'importProducts']);
Route::get('/check-product/product/{product_id}/user/{user_id}', [ProductController::class, 'checkProduct']);
Route::put('/update-quantity/product/{product_id}/quantity/{new_quantity}', [ProductController::class, 'updateQuantity']);



