<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;

class CompletedOrders extends ListOrders
{
    protected static string $resource = OrderResource::class;

    protected static ?string $title = 'Completed Orders';

    protected function getTableQuery(): Builder
    {
        return Order::query()->completedOrders();
    }
}
