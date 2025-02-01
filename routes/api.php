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

// Public Routes
Route::post('/login', [UserController::class, 'login'])->name('login');
Route::post('/dealer/register', [DealerController::class, 'store']);  // Fixed spelling of "register"
Route::post('/dealer/login', [DealerController::class, 'login']);

// Fetch Individual Components without Authentication
Route::get('/w-beam/{id}', [BeamController::class, 'show']);
Route::get('/pole/{id}', [PoleController::class, 'show']);
Route::get('/high-mast/{id}', [HighMastController::class, 'show']);

// Protected Routes - Require Authentication
Route::middleware('auth:sanctum')->group(function () {
    
    // User Management
    Route::apiResource('/user', UserController::class);
    Route::get('/logout', [UserController::class, 'logout']);
    
    // Dealer Management
    Route::apiResource('/dealer', DealerController::class)->except(['store']);
    Route::get('/dealer/logout', [DealerController::class, 'logout']);

    // Project Management
    Route::apiResource('/project', ProjectController::class);
    Route::post('/project/{project_id}/assign-components', [ProjectController::class, 'assignComponents']); // Assign beams, poles, high masts to a project

    // Page Management
    Route::apiResource('/pages', PageController::class);

    // Bulk Operations for Beams, Poles, and High Masts
    Route::post('/w-beams/bulk-update', [BeamController::class, 'bulkUpdate']);
    Route::post('/w-beams/bulk-upload', [BeamController::class, 'bulkUpload']);
    
    Route::post('/poles/bulk-update', [PoleController::class, 'bulkUpdate']);
    Route::post('/poles/bulk-upload', [PoleController::class, 'bulkUpload']); // Added missing bulk upload for poles
    
    Route::post('/high-masts/bulk-update', [HighMastController::class, 'bulkUpdate']);
    Route::post('/high-masts/bulk-upload', [HighMastController::class, 'bulkUpload']); // Added missing bulk upload for high-masts
});
