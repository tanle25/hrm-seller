<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\WebhookController;


Route::post('/webhook', [WebhookController::class, 'handleWebhook']);

Route::get('/',[HomeController::class,'index']);
Route::get('sign', [HomeController::class,'callApi']);
Route::get('product', [HomeController::class,'searchProduct']);
Route::get('shop', [HomeController::class,'getShop']);
Route::post('api/save-product',[HomeController::class,'updateProduct']);
Route::get('order',[OrderController::class,'index']);
Route::post('get-shipping-document',[OrderController::class,'getShippingDocument']);
Route::post('shipping-package',[OrderController::class,'shippingPackage']);
Route::get('shop-info', [HomeController::class,'getShopInfo']);
