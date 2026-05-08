<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Customer extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'vehicle_identifier',
        'vehicle_model',
        'user_id',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_identifier', 'vehicle_identifier');
    }
}
