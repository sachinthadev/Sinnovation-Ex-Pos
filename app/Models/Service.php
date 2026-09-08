<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'commission_percentage',
        'type',
        'labour_type',
        'allowed_service_type',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'commission_percentage' => 'decimal:2',
    ];
}
