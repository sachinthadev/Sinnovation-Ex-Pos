<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use App\Models\Order;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Widgets\IncomeStats;

class Income extends Page implements HasTable
{
    use InteractsWithTable;

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.pages.income';

    public function table(Table $table): Table
    {
        return $table
            ->query(Order::completedOrders())
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer_identifier')
                    ->label('Vehicle Number')
                    ->searchable(),
                TextColumn::make('customer_name')
                    ->label('Customer Name')
                    ->getStateUsing(fn (Order $record) => $record->getCustomerName())
                    ->searchable(),
                TextColumn::make('total_price')
                    ->label('Total Price')
                    ->formatStateUsing(fn ($state) => 'Rs. ' . number_format((float) $state, 2))
                    ->sortable(),
                TextColumn::make('settlement_status')
                    ->label('Status')
                    ->getStateUsing(fn (Order $record) => $record->settlement_status_label),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('month')
                    ->label('Month')
                    ->options([
                        '01' => 'January',
                        '02' => 'February',
                        '03' => 'March',
                        '04' => 'April',
                        '05' => 'May',
                        '06' => 'June',
                        '07' => 'July',
                        '08' => 'August',
                        '09' => 'September',
                        '10' => 'October',
                        '11' => 'November',
                        '12' => 'December',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (isset($data['value']) && $data['value']) {
                            return $query->whereMonth('created_at', $data['value']);
                        }
                        return $query;
                    }),
            ]);
    }

    protected function getHeaderWidgets(): array
    {
        return [
            IncomeStats::class,
        ];
    }
}
