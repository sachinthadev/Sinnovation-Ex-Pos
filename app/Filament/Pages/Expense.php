<?php

namespace App\Filament\Pages;

use App\Models\Expense as ExpenseRecord;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class Expense extends Page implements HasTable
{
    use InteractsWithTable;

    protected static bool $shouldRegisterNavigation = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-trending-down';

    protected string $view = 'filament.pages.expense';

    public string $month;

    public function mount(): void
    {
        $this->month = now()->format('Y-m');
    }

    public function table(Table $table): Table
    {
        $currencySymbol = config('settings.currency_symbol', '');
        $monthStart = Carbon::createFromFormat('Y-m', $this->month)->startOfMonth();

        return $table
            ->query(fn () => ExpenseRecord::query()->whereBetween('created_at', [
                $monthStart,
                $monthStart->copy()->endOfMonth(),
            ]))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('description')
                    ->wrap()
                    ->searchable(),
                TextColumn::make('total')
                    ->label('Total')
                    ->formatStateUsing(fn ($state): string => $currencySymbol.number_format((float) $state, 2))
                    ->sortable(),
                TextColumn::make('bill_path')
                    ->label('Bill')
                    ->formatStateUsing(fn (?string $state): string => $state ? 'View bill' : '-')
                    ->url(fn (ExpenseRecord $record): ?string => $record->bill_path
                        ? asset('uploads/'.ltrim($record->bill_path, '/'))
                        : null)
                    ->openUrlInNewTab(),
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make()
                    ->schema($this->expenseFormSchema())
                    ->successNotificationTitle('Expense updated'),
                DeleteAction::make()
                    ->successNotificationTitle('Expense deleted'),
            ]);
    }

    protected function getViewData(): array
    {
        return [
            'monthOptions' => collect(range(0, 11))->mapWithKeys(function (int $offset): array {
                $month = now()->startOfMonth()->subMonths($offset);

                return [$month->format('Y-m') => $month->format('F Y')];
            }),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('addExpense')
                ->label('Add expense')
                ->icon('heroicon-o-plus')
                ->form($this->expenseFormSchema())
                ->action(fn (array $data) => ExpenseRecord::create($data))
                ->successNotificationTitle('Expense saved'),
        ];
    }

    private function expenseFormSchema(): array
    {
        return [
            Textarea::make('description')
                ->required()
                ->maxLength(1000),
            TextInput::make('total')
                ->numeric()
                ->minValue(0.01)
                ->step(0.01)
                ->required(),
            FileUpload::make('bill_path')
                ->label('Bill attachment')
                ->disk('public_uploads')
                ->directory('expenses')
                ->visibility('public')
                ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/webp'])
                ->maxSize(10240),
        ];
    }
}
