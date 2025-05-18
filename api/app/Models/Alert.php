<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    protected $fillable = [
        'source', 'severity', 'title', 'description', 'raw_payload'
    ];

    protected $casts = [
        'raw_payload' => 'array',
    ];
}
