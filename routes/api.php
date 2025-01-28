<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BeamController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [UserController::class, 'login']);

Route::post('/register', [UserController::class, 'store']);

Route::post('/logout', [UserController::class, 'logout']);

Route::get('/w-beam/{id}', [BeamController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/user/{user}', [UserController::class, 'show']);
    Route::apiResource('/pages', PageController::class);
    Route::post('/w-beams/bulk-update', [BeamController::class, 'bulkUpdate']);
    Route::post('/w-beams/bulk-upload', [BeamController::class, 'bulkUpload']);
});
