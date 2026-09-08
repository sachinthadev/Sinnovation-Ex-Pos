<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'cart';

    public $timestamps = false;

    protected $fillable = [
        'user_id', 'product_id', 'service_id', 'employee_id', 'commission_percentage', 'quantity', 'price', 'tax','name'
    ];

   
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

}
