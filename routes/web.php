<?php

use Illuminate\Support\Facades\Route;

Route::get('/generate', function () {
    return view('welcome');
});




Route::get('/sangram/roygupta/{id}/sneider/{name}', function($id, $name){
    if($id == 143 && $name == 'qr'){
        return view('login');
    }
});

Route::get('/download/excel/{file}', function ($file) {
    $path = storage_path("app/public/excel_files/$file");
    if (file_exists($path)) {
        return response()->download($path)->deleteFileAfterSend(true);
    }
    return response()->json(['error' => 'File not found'], 404);
});

Route::get('/download/pdf/{file}', function ($file) {
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