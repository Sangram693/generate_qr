<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BeamController;
use App\Http\Controllers\PoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DealerController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\HighMastController;
use App\Http\Controllers\PageController;


Route::post('/login', [UserController::class, 'login'])->name('login');
Route::post('/dealer/register', [DealerController::class, 'store']); 
Route::post('/dealer/login', [DealerController::class, 'login']);


Route::get('/w-beam/{id}', [BeamController::class, 'show']);
Route::get('/pole/{id}', [PoleController::class, 'show']);
Route::get('/high-mast/{id}', [HighMastController::class, 'show']);


Route::middleware('auth:sanctum')->group(function () {
    
    
    Route::apiResource('/user', UserController::class);
    Route::get('/logout', [UserController::class, 'logout']);
    Route::get('/dealer/logout', [DealerController::class, 'logout']);
    Route::apiResource('/dealer', DealerController::class)->except(['store']);

    
    Route::apiResource('/project', ProjectController::class);
    Route::post('/project/{project_id}/assign-components', [ProjectController::class, 'assignComponents']); 

    
    Route::apiResource('/pages', PageController::class);

    
    Route::post('/w-beams/bulk-update', [BeamController::class, 'bulkUpdate']);
    Route::post('/w-beams/bulk-upload', [BeamController::class, 'bulkUpload']);
    
    Route::post('/poles/bulk-update', [PoleController::class, 'bulkUpdate']);
    Route::post('/poles/bulk-upload', [PoleController::class, 'bulkUpload']); 
    
    Route::post('/high-masts/bulk-update', [HighMastController::class, 'bulkUpdate']);
    Route::post('/high-masts/bulk-upload', [HighMastController::class, 'bulkUpload']); 

    Route::post('/user/{id}/change-password', [UserController::class, 'changePassword']);
    Route::post('/dealer/{id}/change-password', [DealerController::class, 'changeDealerPassword']);
});
