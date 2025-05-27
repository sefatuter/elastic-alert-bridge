<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailIntegration extends Model
{
    protected $fillable = [
        'name',
        'smtp_host',
        'smtp_port', 
        'smtp_ssl',
        'smtp_username',
        'smtp_password',
        'from_address',
        'default_recipient'
    ];

    protected $casts = [
        'smtp_ssl' => 'boolean',
        'smtp_port' => 'integer'
    ];
}
