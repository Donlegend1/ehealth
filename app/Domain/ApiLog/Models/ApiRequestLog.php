<?php

namespace App\Domain\ApiLog\Models;

use Illuminate\Database\Eloquent\Model;

class ApiRequestLog extends Model
{
    protected $fillable = [
        'endpoint',
        'method',
        'status_code',
        'ip_address',
        'payload',
        'response',
        'response_time_ms',
    ];
}
