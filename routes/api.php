<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BeamController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DealerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\HighMastController;


Route::post('/login', [UserController::class, 'login'])->name('login');
Route::post('/dealer/register', [DealerController::class, 'store']); 
Route::post('/dealer/login', [DealerController::class, 'login']);


Route::get('w-beam/{id}', [BeamController::class, 'show'])
    ->where('id', '^(?!status$).+')
    ->name('beam.show');
Route::get('/pole/{id}', [PoleController::class, 'show']);
Route::get('/high-mast/{id}', [HighMastController::class, 'show']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/w-beam', [BeamController::class, 'index']);
    Route::get('/w-beam/status', [BeamController::class, 'mappingStatus']);

    Route::get('/high_mast', [HighMastController::class, 'index']);

    Route::get('/pole', [PoleController::class, 'index']);
    Route::apiResource('/user', UserController::class);
    Route::get('/logout', [UserController::class, 'logout']);
    Route::get('/dealer/logout', [DealerController::class, 'logout']);
    Route::apiResource('/dealer', DealerController::class)->except(['store']);

    
    Route::apiResource('/project', ProjectController::class);
    Route::post('/project/{project_id}/assign-components', [ProjectController::class, 'assignComponents']); 

    
    Route::apiResource('/pages', PageController::class);

    
    Route::post('/w-beams/bulk-update', [BeamController::class, 'bulkUpdate']);
    Route::post('/w-beams/bulk-upload', [BeamController::class, 'bulkUpload']);
    // Route::post('/w-beams/bulk-mapped', [BeamController::class, 'bulkUpdateFromExcel']);
    
    Route::post('/poles/bulk-update', [PoleController::class, 'bulkUpdate']);
    Route::post('/poles/bulk-upload', [PoleController::class, 'bulkUpload']); 
    
    Route::post('/high-masts/bulk-update', [HighMastController::class, 'bulkUpdate']);
    Route::post('/high-masts/bulk-upload', [HighMastController::class, 'bulkUpload']); 

    Route::post('/user/{id}/change-password', [UserController::class, 'changePassword']);
    Route::post('/dealer/{id}/change-password', [DealerController::class, 'changeDealerPassword']);

    Route::apiResource('/product', ProductController::class);
    Route::post('/product/filter', [ProductController::class, 'filter']);
    Route::post('/product/report', [ProductController::class, 'report']);
    Route::post('/product/total', [ProductController::class, 'total']);
    Route::post('/product/graph', [ProductController::class, 'graph']);
    Route::post('/product/quarter', [ProductController::class, 'quarter']);
    Route::post('/product/mapped', [ProductController::class, 'mapped']);
    Route::post('/product/bulk-mapped', [ProductController::class, 'bulkMapped']);

    Route::get('/header-options', [PageController::class, 'getHeaderOptions']);
    Route::get('/header-data/{id}', [PageController::class, 'getHeaderData']);

});
