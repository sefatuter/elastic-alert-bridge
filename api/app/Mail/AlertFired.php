<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AlertFired extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function build(): self
    {
        \Log::info('Attempting to build email with data', $this->data);

        $summary = $this->data['summary'] ?? 'No Summary';
        
        return $this->subject("🚨 Alert Fired: " . $summary)
                    ->view('emails.alert');
    }
}
