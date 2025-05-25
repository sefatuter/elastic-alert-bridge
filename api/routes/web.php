<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogController;
use App\Http\Controllers\ElastAlertRulesController;

Route::get('/', function () {
    return view('welcome');
});
