<?php

use Illuminate\Support\Facades\Mail;
use App\Mail\AlertFired;
use Illuminate\Http\Request;
use App\Http\Controllers\AlertController;

Route::post('/eab/alert', function (Request $request) {
    $data = $request->all();

    try {
        // Send alert via email
        Mail::to('sefatutercom@gmail.com')->send(new AlertFired($data));
        return response()->json(['ok' => true, 'message' => 'Email sent successfully']);
    } catch (\Exception $e) {
        \Log::error('Error sending email: ' . $e->getMessage());
        return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
    }
});