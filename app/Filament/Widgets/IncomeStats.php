<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class IncomeStats extends BaseWidget
{
    protected function getStats(): array
    {
        $totalIncome = Order::completedOrders()->sum('total_price');
        $monthlyIncome = Order::completedOrders()->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year)->sum('total_price');
        $dailyIncome = Order::completedOrders()->whereDate('created_at', Carbon::today())->sum('total_price');

        return [
            Stat::make('Total Income', 'Rs. ' . number_format($totalIncome, 2))
                ->description('Total income from all completed orders')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
            Stat::make('Monthly Income', 'Rs. ' . number_format($monthlyIncome, 2))
                ->description('Income for this month')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),
            Stat::make('Daily Income', 'Rs. ' . number_format($dailyIncome, 2))
                ->description('Income for today')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('primary'),
        ];
    }
}
