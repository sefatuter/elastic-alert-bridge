<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/eab/alert', function (Request $request) {
    // TODO: validate & store
    return response()->json(['received' => $request->all()], 202);
});
