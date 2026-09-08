<?php

namespace App\Filament\Pages;

use App\Models\OrderItem;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class Ledger extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 9;

    protected static ?string $navigationLabel = 'Ledger';

    protected string $view = 'filament.pages.ledger';

    public string $month;

    public function mount(): void
    {
        $this->month = now()->format('Y-m');
    }

    public function table(Table $table): Table
    {
        $currencySymbol = config('settings.currency_symbol');

        return $table
            ->query(fn () => $this->salaryQuery())
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('order_id')
                    ->label('Order ID')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Service')
                    ->getStateUsing(fn (OrderItem $record): string => $record->name ?: ($record->service?->name ?? '-'))
                    ->searchable(),
                TextColumn::make('price')
                    ->label('Price')
                    ->formatStateUsing(fn ($state): string => $currencySymbol . number_format((float) $state, 2))
                    ->sortable(),
                TextColumn::make('quantity')
                    ->label('Qty')
                    ->sortable(),
                TextColumn::make('employee.name')
                    ->label('Employee')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('commission_percentage')
                    ->label('Commission')
                    ->getStateUsing(fn (OrderItem $record): float => (float) ($record->commission_percentage ?? $record->service?->commission_percentage ?? 0))
                    ->formatStateUsing(fn ($state): string => number_format((float) $state, 2) . '%'),
                TextColumn::make('salary')
                    ->label('Salary')
                    ->getStateUsing(fn (OrderItem $record): float => $this->calculateSalary($record))
                    ->formatStateUsing(fn ($state): string => $currencySymbol . number_format((float) $state, 2)),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
            ]);
    }

    protected function getViewData(): array
    {
        $monthStart = $this->monthStart();
        $serviceItems = $this->salaryQuery()->get();

        $employeeSalaries = $serviceItems
            ->map(function (OrderItem $item): array {
                return [
                    'employee_name' => $item->employee?->name,
                    'salary' => $this->calculateSalary($item),
                ];
            })
            ->groupBy('employee_name')
            ->map(function (Collection $items, string $employeeName): array {
                return [
                    'employee_name' => $employeeName,
                    'service_count' => $items->count(),
                    'total_salary' => $items->sum('salary'),
                ];
            })
            ->sortByDesc('total_salary')
            ->values();

        return [
            'employeeSalaries' => $employeeSalaries,
            'totalSalary' => $employeeSalaries->sum('total_salary'),
            'currency_symbol' => config('settings.currency_symbol'),
            'selectedMonth' => $monthStart->format('F Y'),
            'monthOptions' => collect(range(0, 11))->mapWithKeys(function (int $offset): array {
                $month = now()->startOfMonth()->subMonths($offset);

                return [$month->format('Y-m') => $month->format('F Y')];
            }),
        ];
    }

    protected function salaryQuery()
    {
        $monthStart = $this->monthStart();
        $monthEnd = $monthStart->copy()->endOfMonth();

        /** @var Builder $query */
        $query = OrderItem::query();

        return $query
            ->with(['service', 'employee'])
            ->whereNotNull('employee_id')
            ->whereBetween('created_at', [$monthStart, $monthEnd]);
    }

    protected function monthStart(): Carbon
    {
        return Carbon::createFromFormat('Y-m', $this->month)->startOfMonth();
    }

    protected function calculateSalary(OrderItem $item): float
    {
        $commissionPercentage = (float) ($item->commission_percentage ?? $item->service?->commission_percentage ?? 0);

        return (float) $item->price * (int) $item->quantity * ($commissionPercentage / 100);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('income')
                ->label('Income')
                ->icon('heroicon-o-arrow-trending-up')
                ->color('success')
                ->url(url('/admin/income')),
            Action::make('expense')
                ->label('Expense')
                ->icon('heroicon-o-arrow-trending-down')
                ->color('danger'),
            Action::make('salary')
                ->label('Salary')
                ->icon('heroicon-o-banknotes')
                ->color('warning'),
        ];
    }
}
