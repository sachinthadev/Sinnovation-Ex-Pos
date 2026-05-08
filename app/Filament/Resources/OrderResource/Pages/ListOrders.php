<?php

namespace App\Filament\Resources\OrderResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Maatwebsite\Excel\Excel;
use App\Filament\Resources\OrderResource\Widgets\OrderStats;
use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Actions;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Columns\Column;

class ListOrders extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = OrderResource::class;

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
                ->url(fn () => url('/admin/products/inventory'))
                ->icon('heroicon-o-cube-transparent'),
            CreateAction::make()->visible(fn () => auth()->user()->role === 'admin'),
            ExportAction::make()
                ->exports([
                    ExcelExport::make()
                        ->fromTable()
                        ->withFilename(fn ($resource) => $resource::getModelLabel() . '-' . date('Y-m-d'))
                        ->withWriterType(Excel::CSV)
                        ->withColumns([
                            Column::make('customer.phone')->heading('Mobile'),
                            Column::make('customer.email')->heading('Email'),
                            Column::make('customer.address')->heading('Address'),
                            Column::make('updated_at'),
                        ])
                ]),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return Order::query()->openToday();
    }

    protected function getWidgets(): array
    {
        return [
            OrderStats::class,
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            OrderStats::class,
        ];
    }
}
