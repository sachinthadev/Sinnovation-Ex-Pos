<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected string $view = 'filament.pages.view-order';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('edit')
                ->label('Edit')
                ->url(fn (Order $record) => OrderResource::getUrl('edit', ['record' => $record]))
                ->icon('heroicon-o-pencil')
                ->color('primary')
                ->visible(fn () => Auth::check() && Auth::user()?->role === 'admin'),
        ];
    }
}
