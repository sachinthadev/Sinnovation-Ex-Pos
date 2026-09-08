<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Maatwebsite\Excel\Excel;
use App\Filament\Resources\ServiceResource\Pages\ListServices;
use App\Filament\Resources\ServiceResource\Pages;
use App\Filament\Resources\ServiceResource\RelationManagers;
use App\Models\Service;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextInputColumn;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Columns\Column;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('price')
                        ->numeric()
                        ->default(0)
                        ->required(fn (Get $get): bool => $get('type') === 'service')
                        ->visible(fn (Get $get): bool => $get('type') === 'service'),
                    Forms\Components\Select::make('type')
                        ->options([
                            'labour' => 'Labour',
                            'service' => 'Service',
                        ])
                        ->default('service')
                        ->required(),
                    Forms\Components\Select::make('labour_type')
                        ->label('Labour Type')
                        ->options([
                            'mechanic' => 'Mechanic',
                            'supporter' => 'Supporter',
                        ])
                        ->required(fn (Get $get): bool => $get('type') === 'labour')
                        ->visible(fn (Get $get): bool => $get('type') === 'labour'),
                    Forms\Components\Select::make('allowed_service_type')
                        ->label('Allowed Service Type')
                        ->options([
                            'repair' => 'Repair',
                            'service' => 'Service',
                        ])
                        ->required(fn (Get $get): bool => $get('type') === 'labour')
                        ->visible(fn (Get $get): bool => $get('type') === 'labour'),
                    TextInput::make('commission_percentage')
                        ->label('Commission (%)')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->step(0.01)
                        ->suffix('%')
                        ->required(fn (Get $get): bool => $get('type') === 'service')
                        ->visible(fn (Get $get): bool => $get('type') === 'service'),
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
                TextColumn::make('type')
                                ->sortable()
                                ->searchable(),
                TextColumn::make('commission_percentage')
                                ->label('Commission')
                                ->suffix('%')
                                ->placeholder('-'),
                TextColumn::make('price')->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()->visible(fn () => auth()->user()->role === 'admin'),
                DeleteAction::make()->visible(fn () => auth()->user()->role === 'admin'),
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
                            Column::make('type')->heading('Type'),
                            Column::make('price')->heading('Price'),
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
            'index' => ListServices::route('/'),
            // 'create' => Pages\CreateService::route('/create'),
            // 'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}
