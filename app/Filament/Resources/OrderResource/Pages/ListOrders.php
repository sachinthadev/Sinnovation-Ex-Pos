<?php

namespace App\Filament\Resources\OrderResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Maatwebsite\Excel\Excel;
use App\Filament\Resources\OrderResource\Widgets\OrderStats;
use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Actions;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Columns\Column;

class ListOrders extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = OrderResource::class;
    protected static ?string $title = 'Active Orders';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('orders')
                ->label('Active Orders')
                ->url(fn () => url('/admin/orders'))
                ->icon('heroicon-o-shopping-bag'),
            Action::make('completed')
                ->label('Completed Orders')
                ->url(fn () => url('/admin/orders/completed'))
                ->icon('heroicon-o-check-badge'),
            CreateAction::make()->visible(fn () => auth()->user()->role === 'admin'),
            ExportAction::make()
                ->exports([
                    ExcelExport::make()
                        ->withFilename(fn ($resource) => $resource::getModelLabel() . '-' . date('Y-m-d'))
                        ->withWriterType(Excel::CSV)
                        ->withColumns([
                            Column::make('id')->heading('ID'),
                            Column::make('customer.vehicle_identifier')->heading('Vehicle Number'),
                            Column::make('customer.phone')->heading('Mobile'),
                            Column::make('customer.email')->heading('Email'),
                            Column::make('customer.address')->heading('Address'),
                            Column::make('total_price')->heading('Total Price'),
                            Column::make('mileage')->heading('Mileage'),
                            Column::make('settlement_status')->heading('Status'),
                            Column::make('created_at')->heading('Created At'),
                        ])
                ]),
        ];
    }

    public function table(Table $table): Table
    {
        return OrderResource::table($table)
            ->recordActions([
                Action::make('edit')
                    ->label('Edit')
                    ->url(fn (Order $record) => OrderResource::getUrl('edit', ['record' => $record]))
                    ->visible(fn () => Auth::check() && Auth::user()?->role === 'admin'),
                DeleteAction::make()->visible(fn () => Auth::check() && Auth::user()?->role === 'admin'),
            ]);
    }

    protected function getTableQuery(): Builder
    {
        return Order::query()->whereDate('created_at', now()->toDateString());
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
