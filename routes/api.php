<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\ContactController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::controller(UserController::class)->group(function () {
    Route::post('/users','register');
    Route::post('/users/login','login');

    Route::middleware('apiAuth')->group(function () {
        Route::get('/users/current','getCurrentUser');
        Route::patch('/users/current','update');
        Route::delete('/users/logout', 'logout');
    });
});

Route::controller(ContactController::class)->group(function () {

    Route::middleware('apiAuth')->group(function () {
        Route::post('/contacts', 'store');
        Route::get('/contacts', 'search');
        Route::get('/contacts/{id}', 'show')->where('id','[0-9]+');
        Route::put('/contacts/{id}', 'update')->where('id','[0-9]+');
        Route::delete('/contacts/{id}', 'destroy')->where('id','[0-9]+');
    });
});

Route::controller(AddressController::class)->group(function () {
    Route::middleware('apiAuth')->group(function () {
        Route::get('/contacts/{idContact}/addresses/{idAddress}', 'show')->where('idContact','[0-9]+')->where('idAddress','[0-9]+');
        Route::put('/contacts/{idContact}/addresses/{idAddress}', 'update')->where('idContact','[0-9]+')->where('idAddress','[0-9]+');
        Route::delete('/contacts/{idContact}/addresses/{idAddress}', 'destroy')->where('idContact','[0-9]+')->where('idAddress','[0-9]+');
        Route::post('/contacts/{idContact}/addresses', 'store')->where('idContact','[0-9]+');
        Route::get('/contacts/{idContact}/addresses', 'index')->where('idContact','[0-9]+');
    });
});