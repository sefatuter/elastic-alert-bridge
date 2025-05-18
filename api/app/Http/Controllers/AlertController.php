<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AlertController extends Controller
{
    public function receive(Request $request)
    {
        $data = $request->all();

        $alert = Alert::create([
            'source' => $data['source'] ?? 'unknown',
            'severity' => $data['severity'] ?? 'INFO',
            'title' => $data['title'] ?? 'No title',
            'description' => $data['description'] ?? '',
            'raw_payload' => $data,
        ]);

        Mail::raw("🚨 {$alert->severity}: {$alert->title}\n\n{$alert->description}", function ($message) {
            $message->to('ops@example.com')
                    ->subject('New ElastAlert Notification');
        });

        return response()->json(['status' => 'ok'], 202);
    }
}
