<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogController;
use App\Http\Controllers\ElastAlertRulesController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/logs', [LogController::class, 'index']);

Route::get('/elastalert-rules', [ElastAlertRulesController::class, 'index'])->name('elastalert_rules.index');
Route::get('/elastalert-rules/create', [ElastAlertRulesController::class, 'create'])->name('elastalert_rules.create');
Route::post('/elastalert-rules', [ElastAlertRulesController::class, 'store'])->name('elastalert_rules.store');
Route::get('/elastalert-rules/{filename}/edit', [ElastAlertRulesController::class, 'edit'])->name('elastalert_rules.edit');
Route::put('/elastalert-rules/{filename}', [ElastAlertRulesController::class, 'update'])->name('elastalert_rules.update');
