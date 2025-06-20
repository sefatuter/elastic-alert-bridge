<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SlackIntegration extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'webhook_url',
        'channel',
        'username',
        'icon_emoji'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}


