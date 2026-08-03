<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class CompletedOrders extends ListOrders
{
    protected static string $resource = OrderResource::class;

    protected static ?string $title = 'Completed Orders';

    public function table(Table $table): Table
    {
        return OrderResource::table($table)
            ->recordActions([
                Action::make('view')
                    ->label('View')
                    ->url(fn (Order $record) => OrderResource::getUrl('view', ['record' => $record]))
                    ->visible(fn () => Auth::check() && Auth::user()?->role === 'admin'),
                DeleteAction::make()->visible(fn () => Auth::check() && Auth::user()?->role === 'admin'),
            ]);
    }

    protected function getTableQuery(): Builder
    {
        return Order::query()->completedOrders();
    }
}
