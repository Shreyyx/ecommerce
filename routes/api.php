<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OrderController;

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

Route::controller(AuthController::class)->group(function(){
    Route::post('login','login');
    Route::post('register','register');
});


Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::post('/createOrder', [OrderController::class, 'createOrder']);
Route::get('orders/{order_id}', [OrderController::class, 'retrieveOrder']);
Route::delete('/orders/{order_id}', [OrderController::class, 'deleteOrder']);
Route::post('/orders/{order_id}/items', [OrderController::class, 'addOrderItem']);
Route::put('/orders/{order_id}/items/{order_item_seq_id}', [OrderController::class, 'updateOrderItem']);
Route::delete('/orders/{order_id}/items/{order_item_seq_id}', [OrderController::class, 'deleteOrderItem']);
Route::put('/orders/{order_id}', [OrderController::class, 'updateOrder']);