<?php

namespace App\Filament\Pages;

use App\Models\Expense as ExpenseRecord;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Filament\Pages\Page;

class Account extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'account';

    protected string $view = 'filament.pages.account';

    public string $month;

    public function mount(): void
    {
        $this->month = now()->format('Y-m');
    }

    protected function getViewData(): array
    {
        $monthStart = Carbon::createFromFormat('Y-m', $this->month)->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        $income = Order::completedOrders()
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->sum('total_price');

        $otherExpenses = ExpenseRecord::query()
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->sum('total');

        $salaryExpenses = OrderItem::query()
            ->with('service')
            ->whereNotNull('employee_id')
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->get()
            ->sum(fn (OrderItem $item): float => $this->calculateSalary($item));

        $totalExpenses = (float) $otherExpenses + $salaryExpenses;

        return [
            'currencySymbol' => config('settings.currency_symbol', ''),
            'selectedMonth' => $monthStart->format('F Y'),
            'monthOptions' => collect(range(0, 11))->mapWithKeys(function (int $offset): array {
                $month = now()->startOfMonth()->subMonths($offset);

                return [$month->format('Y-m') => $month->format('F Y')];
            }),
            'income' => (float) $income,
            'otherExpenses' => (float) $otherExpenses,
            'salaryExpenses' => $salaryExpenses,
            'totalExpenses' => $totalExpenses,
            'profit' => (float) $income - $totalExpenses,
        ];
    }

    private function calculateSalary(OrderItem $item): float
    {
        $commissionPercentage = (float) ($item->commission_percentage ?? $item->service?->commission_percentage ?? 0);

        return (float) $item->price * (int) $item->quantity * ($commissionPercentage / 100);
    }
}
