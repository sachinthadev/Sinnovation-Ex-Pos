<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\Product;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class LowStockProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected static ?string $title = 'Low Stock Inventory';

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
                ->label('Name')
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
