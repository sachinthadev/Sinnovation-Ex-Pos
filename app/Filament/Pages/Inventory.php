<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Product;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Maatwebsite\Excel\Excel;
use pxlrbt\FilamentExcel\Columns\Column;

class Inventory extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?int $navigationSort = 2;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected string $view = 'filament.pages.inventory';

    public function table(Table $table): Table
    {
        $currency_symbol = config('settings.currency_symbol', '$');

        return $table
            ->query(
                Product::query()
                    ->where('quantity', '<=', 5)
                    ->orderBy('quantity', 'asc')
            )
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('barcode')->sortable()->searchable(),
                TextColumn::make('part_number')->label('Part Number')->sortable()->searchable(),
                TextColumn::make('quantity')->label('Quantity')->sortable()
                    ->color(fn ($state) => $state === 0 ? 'danger' : 'warning'),
                TextColumn::make('price')->label('Price')->sortable()
                    ->formatStateUsing(fn ($record) => $currency_symbol.$record->price),
            ])
            ->headerActions([
                ExportAction::make('export')
                    ->label('Export CSV')
                    ->exports([
                        ExcelExport::make()
                            ->withFilename('Low_Stock_Inventory-' . date('Y-m-d'))
                            ->withWriterType(Excel::CSV)
                            ->withColumns([
                                Column::make('name')->heading('Name'),
                                Column::make('barcode')->heading('Barcode'),
                                Column::make('part_number')->heading('Part Number'),
                                Column::make('quantity')->heading('Quantity'),
                                Column::make('price')->heading('Price'),
                            ])
                    ]),
            ]);
    }
}
