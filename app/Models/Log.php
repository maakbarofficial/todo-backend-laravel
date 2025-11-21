<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $fillable = [
        'user_id',
        'user_name',
        'method',
        'url',
        'ip_address',
        'device',
        'browser',
        'platform',
        'payload',
        'status_code',
        // 'response_body',
        'execution_time'
    ];

    protected $casts = [
        'extra' => 'array',
    ];
}
