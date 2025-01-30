<?php

use Illuminate\Support\Facades\Route;

Route::get('/sangram/roygupta/{id}/sneider/{name}', function () {
    return view('welcome');
});

Route::get('/', function(){
    return "<h1>Sangram Roygupta</h1>";
});