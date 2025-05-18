<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('api_page');
});

Route::get('/test', function () {
    return view('emails.alert');
});