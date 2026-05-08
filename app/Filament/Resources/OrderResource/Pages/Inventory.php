<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Illuminate\Database\Eloquent\Builder;

class Inventory extends Page
{
    use InteractsWithTable;

    protected static string $resource = OrderResource::class;

    protected string $view = 'filament.pages.inventory';

    protected static ?string $title = 'Inventory';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('orders')
                ->label('Orders')
                ->url(fn () => url('/admin/orders'))
                ->icon('heroicon-o-shopping-bag'),
            Action::make('completed')
                ->label('Completed Orders')
                ->url(fn () => url('/admin/orders/completed'))
                ->icon('heroicon-o-check-badge'),
            Action::make('inventory')
                ->label('Inventory')
                ->url(fn () => url('/admin/orders/inventory'))
                ->icon('heroicon-o-cube-transparent'),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return Product::query()
            ->where('quantity', '<=', 5)
            ->orderBy('quantity', 'asc');
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->label('Product Name')
                ->sortable()
                ->searchable(),
            TextColumn::make('barcode')
                ->sortable()
                ->searchable(),
            TextColumn::make('part_number')
                ->label('Part Number')
                ->sortable()
                ->searchable(),
            TextColumn::make('quantity')
                ->label('Quantity')
                ->sortable(),
            TextColumn::make('price')
                ->label('Price')
                ->sortable(),
        ];
    }
}
