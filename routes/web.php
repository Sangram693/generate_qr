<?php

use Illuminate\Support\Facades\Route;

Route::get('/sangram/roygupta/{id}/sneider/{name}', function ($id, $name) {
    if ($id == 143 && $name == 'qr') {
        return view('welcome');
    } else {
        abort(403, 'Unauthorized Access');
    }
});


Route::get('/', function(){
    return "<h1>Sangram Roygupta</h1>";
});