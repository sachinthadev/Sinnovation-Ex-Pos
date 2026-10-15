<x-filament-panels::page>
	<div class="space-y-6">
		<div class="flex flex-col gap-4 rounded-xl border border-gray-200 bg-white p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between dark:border-gray-700 dark:bg-gray-900">
			<div class="flex items-center gap-3">
				<div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-primary-50 text-primary-600 dark:bg-primary-400/10 dark:text-primary-400">
					<x-filament::icon icon="heroicon-o-chart-bar" class="size-5" />
				</div>
				<div>
					<h2 class="text-sm font-semibold text-gray-950 dark:text-white">Monthly account summary</h2>
					<p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">Income, expenses, and profit</p>
				</div>
			</div>

			<div class="w-full sm:w-60">
				<label for="account-month" class="mb-1.5 block text-xs font-medium text-gray-500 dark:text-gray-400">Reporting month</label>
				<div class="relative">
					<select id="account-month" wire:model.live="month" class="w-full appearance-none rounded-lg border-gray-300 bg-gray-50 py-2.5 pl-3 pr-10 text-sm font-medium text-gray-950 shadow-sm transition focus:border-primary-500 focus:bg-white focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:focus:bg-gray-800">
						@foreach ($monthOptions as $value => $label)
							<option value="{{ $value }}">{{ $label }}</option>
						@endforeach
					</select>
					<x-filament::icon icon="heroicon-m-chevron-down" class="pointer-events-none absolute right-3 top-1/2 size-4 -translate-y-1/2 text-gray-400" />
				</div>
			</div>
		</div>

		<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
			<div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900">
				<div class="text-sm font-medium text-gray-500 dark:text-gray-400">Income</div>
				<div class="mt-2 text-2xl font-bold text-success-600 dark:text-success-400">{{ $currencySymbol }}{{ number_format($income, 2) }}</div>
			</div>

			<div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900">
				<div class="text-sm font-medium text-gray-500 dark:text-gray-400">Recorded expenses</div>
				<div class="mt-2 text-2xl font-bold text-danger-600 dark:text-danger-400">{{ $currencySymbol }}{{ number_format($otherExpenses, 2) }}</div>
			</div>

			<div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900">
				<div class="text-sm font-medium text-gray-500 dark:text-gray-400">Salary expense</div>
				<div class="mt-2 text-2xl font-bold text-warning-600 dark:text-warning-400">{{ $currencySymbol }}{{ number_format($salaryExpenses, 2) }}</div>
			</div>

			<div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900">
				<div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total expenses</div>
				<div class="mt-2 text-2xl font-bold text-danger-600 dark:text-danger-400">{{ $currencySymbol }}{{ number_format($totalExpenses, 2) }}</div>
				<div class="mt-1 text-xs text-gray-500 dark:text-gray-400">Recorded expenses and employee salary</div>
			</div>

			<div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900">
				<div class="text-sm font-medium text-gray-500 dark:text-gray-400">Profit</div>
				<div class="mt-2 text-2xl font-bold {{ $profit >= 0 ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400' }}">{{ $currencySymbol }}{{ number_format($profit, 2) }}</div>
				<div class="mt-1 text-xs text-gray-500 dark:text-gray-400">Income minus total expenses for {{ $selectedMonth }}</div>
			</div>
		</div>
	</div>
</x-filament-panels::page>
