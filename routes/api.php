<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OssController;
use App\Http\Controllers\RabbitMqController;
use App\Http\Controllers\Api\NotifyController;
use App\Http\Controllers\Api\AlipayController;
use App\Http\Controllers\Api\WechatController;
use App\Http\Controllers\Api\CustomerController;
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

Route::post('upload', [OssController::class, 'upload']);
