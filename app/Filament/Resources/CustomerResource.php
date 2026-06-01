<?php 

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\CustomerResource\Pages\ListCustomers;
use App\Filament\Resources\CustomerResource\Pages\EditCustomer;
use App\Filament\Resources\CustomerResource\Pages;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\CustomerResource\RelationManagers\OrdersRelationManager;


class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('first_name')
                    ->required()
                    ->maxLength(20),
                TextInput::make('last_name')
                    ->maxLength(20),
                TextInput::make('email')
                    ->email()
                    ->nullable(),
                TextInput::make('phone')
                    ->tel()
                    ->nullable(),
                TextInput::make('vehicle_identifier')
                    ->label('Vehicle Identifier')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->helperText('Vehicle number or chassis number.'),
                TextInput::make('vehicle_model')
                    ->label('Vehicle Model')
                    ->nullable()
                    ->helperText('Optional vehicle model.'),
                Textarea::make('address')
                    ->nullable(),
                \Filament\Schemas\Components\Section::make('Statistics')
                    ->schema([
                        Forms\Components\Placeholder::make('completed_orders_count')
                            ->label('Completed Orders Count')
                            ->content(fn (?Customer $record): string => $record ? (string) $record->orders()->completedOrders()->count() : '0'),
                        Forms\Components\Placeholder::make('total_orders_count')
                            ->label('Total Orders Count')
                            ->content(fn (?Customer $record): string => $record ? (string) $record->orders()->count() : '0'),
                    ])->hidden(fn (?Customer $record) => $record === null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('first_name')->sortable()->searchable(),
                TextColumn::make('last_name')->searchable(),
                TextColumn::make('phone')->searchable(),
                TextColumn::make('vehicle_identifier')->label('Vehicle Identifier')->searchable(),
                TextColumn::make('vehicle_model')->label('Vehicle Model')->searchable(),
                TextColumn::make('orders_count')
                            ->label('Orders')
                            ->counts('orders')  
                            ->sortable(),                
                TextColumn::make('created_at')->sortable()->dateTime(),
            ])
            ->filters([])
            ->recordActions([
                EditAction::make()->visible(fn () => auth()->user()->role === 'admin'),
                DeleteAction::make()->visible(fn () => auth()->user()->role === 'admin'),
            ])
            ->toolbarActions([
                DeleteBulkAction::make()->visible(fn () => auth()->user()->role === 'admin'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            OrdersRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCustomers::route('/'),
            // 'create' => Pages\CreateCustomer::route('/create'),
            'edit' => EditCustomer::route('/{record}/edit'),
        ];
    }
}
