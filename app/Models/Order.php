<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'customer_identifier',
        'total_price',
        'mileage',
        'user_id'
    ];

    public function scopeOpenToday(Builder $query): Builder
    {
        $todayNoon = Carbon::today()->addHours(12);

        if (Carbon::now()->greaterThan($todayNoon)) {
            return $query->whereRaw('0 = 1');
        }

        return $query
            ->whereDate('created_at', Carbon::today())
            ->where('created_at', '<=', $todayNoon);
    }

    public function scopeCompletedOrders(Builder $query): Builder
    {
        if (Carbon::now()->greaterThan(Carbon::today()->addHours(12))) {
            return $query->whereDate('created_at', '<=', Carbon::today());
        }

        return $query->whereDate('created_at', '<', Carbon::today());
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_identifier', 'vehicle_identifier');
    }

    public function getCustomerName()
    {
        if($this->customer) {
            return $this->customer->first_name . ' ' . $this->customer->last_name;
        }
        return __('customer.working');
    }

    public function total()
    {
        return $this->items->map(function ($i){
            return $i->price;
        })->sum();
    }

    public function formattedTotal()
    {
        return number_format($this->total(), 2);
    }

    public function receivedAmount()
    {
        return $this->payments->map(function ($i){
            return $i->amount;
        })->sum();
    }

    public function formattedReceivedAmount()
    {
        return number_format($this->receivedAmount(), 2);
    }
}
