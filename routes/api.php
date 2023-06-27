<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\TerminalController;

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
    Route::post('login', 'login');
    Route::post('logout', 'logout');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(TerminalController::class)->group(function(){
        Route::get('terminals', 'index');
        Route::post('terminals', 'store');
        Route::put('terminals/{id}', 'update')->where('id', '[0-9]+');
        Route::delete('terminals/{id}', 'destroy')->where('id', '[0-9]+');

        Route::get('terminals/{id}/ping', 'pingTerminal')->where('id', '[0-9]+');
        Route::get('terminals/{id}/logs', 'pingTerminalLogs')->where('id', '[0-9]+');
        Route::get('terminals/ping', 'pingAllTerminals');
    });
});
