<?php

use App\Http\Controllers\ReportController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

// Custom login route
Route::get('/sangram/roygupta/{id}/sneider/{name}', [AuthController::class, 'showLoginForm'])->name('custom.login');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware('auth.redirect')->group(function () {
    Route::get('/generate', function () {
        return view('welcome');
    })->name('generate');

    Route::post('/pages', [PageController::class, 'store'])->name('pages.store');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/bulk-update', [ReportController::class, 'bulkUpdate'])->name('reports.bulk-update');
});

// Public download routes
Route::get('/download/excel/{file}', function ($file) {
    $path = storage_path("app/public/excel_files/$file");
    if (file_exists($path)) {
        return response()->download($path)->deleteFileAfterSend(true);
    }
    return response()->json(['error' => 'File not found'], 404);
});

Route::match(['get', 'post'], '/download/pdf/{file}', function ($file) {
    $path = storage_path("app/public/pdf_files/$file");
    if (file_exists($path)) {
        return response()->download($path)->deleteFileAfterSend(true);
    }
    return response()->json(['error' => 'File not found'], 404);
});

Route::get('/download/{filename}', function ($filename) {
    $path = public_path("download/$filename");

    if (file_exists($path)) {
        return response()->download($path);
    }
    \Log::error("Download failed: File not found at $path");
    return response()->view('errors.file_not_found', ['filename' => $filename], 404);
})->name('download.pdf');