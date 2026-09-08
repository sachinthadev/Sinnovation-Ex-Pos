<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Maatwebsite\Excel\Excel;
use App\Filament\Resources\ProductResource\Pages\ListProducts;
use App\Filament\Resources\ProductResource\Pages\LowStockProducts;
use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextInputColumn;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Columns\Column;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('barcode')
                        ->required()
                        ->unique(Product::class, 'barcode', ignoreRecord: true),
                    TextInput::make('part_number')
                        ->label('Part Number')
                        ->maxLength(255)
                        ->helperText('Optional. Searchable by product part number.'),
                    TextInput::make('store_location')
                        ->label('Store Location')
                        ->maxLength(255)
                        ->placeholder('e.g. Shelf A1'),
                    TextInput::make('price')
                        ->numeric()
                        ->required(),
                    TextInput::make('quantity')
                        ->numeric()
                        ->minValue(0)
                        ->default(1)
                        ->required(),
                    FileUpload::make('image')
                        ->disk('public_uploads')
                        ->panelLayout('grid')
                        ->visibility('public'),
                    Toggle::make('status')
                        ->label('Active')
                        ->default(true)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                                ->width(250)
                                ->wrap()
                                ->sortable()
                                ->searchable(),
                ImageColumn::make('image')->disk('public_uploads')  
                                ->size(50)  
                                ->square(),
                TextColumn::make('barcode')->searchable(),
                TextColumn::make('part_number')->label('Part Number')->searchable()->sortable(),
                TextColumn::make('store_location')->label('Store Location')->searchable()->sortable(),
                TextInputColumn::make('quantity')->type('number')  
                                ->sortable() 
                                ->width(10)
                                ->rules(['required', 'integer', 'min:1']),
                TextColumn::make('price')->sortable(),              
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()->visible(fn () => auth()->user()->role === 'admin'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->visible(fn () => auth()->user()->role === 'admin'),
                ]),
                ExportBulkAction::make()->exports([
                    ExcelExport::make()
                        ->withFilename(fn ($resource) => $resource::getModelLabel() . '-' . date('Y-m-d'))
                        ->withWriterType(Excel::CSV)
                        ->withColumns([
                            Column::make('name')->heading('Name'),
                            Column::make('barcode')->heading('Barcode'),
                            Column::make('part_number')->heading('Part Number'),
                            Column::make('store_location')->heading('Store Location'),
                            Column::make('price')->heading('Price'),
                            Column::make('tax')->heading('Tax'),
                            Column::make('quantity')->heading('Quantity'),
                        ])
                ])
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            // 'create' => Pages\CreateProduct::route('/create'),
            // 'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
