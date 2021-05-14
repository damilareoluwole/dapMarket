<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\landingController;

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

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
   
});


Route::group(['middleware' => ['web']], function () {
    Route::prefix('dapmarket')->group(function() {
        Route::get('/category', [landingController::class, 'category']);
        Route::get('/brand', [landingController::class, 'brand']);
        Route::get('/homepage', [landingController::class, 'home']);
        Route::get('/favcollection', [landingController::class, 'homeCollection']);
        Route::get('/ads', [landingController::class, 'Ads']);
        Route::get('/products', [landingController::class, 'products']);
        Route::get('/shops', [landingController::class, 'shops']);
    });
});


