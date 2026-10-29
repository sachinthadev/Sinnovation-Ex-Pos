<x-filament-panels::page>
	<div class="space-y-5">
		<div>
			<div>
				<h2 class="text-lg font-semibold text-gray-950 dark:text-white">Employee Salary</h2>
				<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Commission summary for {{ $selectedMonth }}.</p>
			</div>
			<div class="mt-4 grid gap-4 sm:grid-cols-2">
				<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900">
					<div class="flex items-center gap-3">
						<div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-success-50 text-success-600 dark:bg-success-400/10 dark:text-success-400">
							<x-filament::icon icon="heroicon-o-arrow-trending-up" class="size-5" />
						</div>
						<div>
							<div class="text-sm font-medium text-gray-500 dark:text-gray-400">Monthly total service income</div>
							<div class="mt-1 text-2xl font-bold tabular-nums text-gray-950 dark:text-white">
								{{ $currency_symbol }}{{ number_format($monthlyIncome, 2) }}
							</div>
						</div>
					</div>
					<div class="mt-3 text-xs text-gray-500 dark:text-gray-400">Total price × quantity for {{ $selectedMonth }}</div>
				</div>

				<div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900">
					<div class="flex items-center gap-3">
						<div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-warning-50 text-warning-600 dark:bg-warning-400/10 dark:text-warning-400">
							<x-filament::icon icon="heroicon-o-banknotes" class="size-5" />
						</div>
						<div>
							<div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total salary</div>
							<div class="mt-1 text-2xl font-bold tabular-nums text-gray-950 dark:text-white">
								{{ $currency_symbol }}{{ number_format($totalSalary, 2) }}
							</div>
						</div>
					</div>
					<div class="mt-3 text-xs text-gray-500 dark:text-gray-400">Employee commissions for {{ $selectedMonth }}</div>
				</div>
			</div>
		</div>

		<div class="flex flex-col gap-4 rounded-xl border border-gray-200 bg-white p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between dark:border-gray-700 dark:bg-gray-900">
			<div class="flex items-center gap-3">
				<div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-primary-50 text-primary-600 dark:bg-primary-400/10 dark:text-primary-400">
					<x-filament::icon icon="heroicon-o-calendar-days" class="size-5" />
				</div>
				<div>
					<h3 class="text-sm font-semibold text-gray-950 dark:text-white">Salary by month</h3>
					<p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">Choose a month to view employee commissions</p>
				</div>
			</div>
			<div class="w-full sm:w-60">
				<label for="ledger-month" class="mb-1.5 block text-xs font-medium text-gray-500 dark:text-gray-400">Reporting month</label>
				<div class="relative">
					<select id="ledger-month" wire:model.live="month" class="w-full appearance-none rounded-lg border-gray-300 bg-gray-50 py-2.5 pl-3 pr-10 text-sm font-medium text-gray-950 shadow-sm transition focus:border-primary-500 focus:bg-white focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:focus:bg-gray-800">
						@foreach ($monthOptions as $value => $label)
							<option value="{{ $value }}">{{ $label }}</option>
						@endforeach
					</select>
					<x-filament::icon icon="heroicon-m-chevron-down" class="pointer-events-none absolute right-3 top-1/2 size-4 -translate-y-1/2 text-gray-400" />
				</div>
			</div>
		</div>

		<div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
			<div class="border-b border-gray-200 px-4 py-3 dark:border-gray-700">
				<h3 class="text-sm font-semibold text-gray-950 dark:text-white">Salary by employee</h3>
			</div>
			<div class="overflow-x-auto">
			<table class="w-full min-w-[500px] text-sm">
				<thead class="bg-gray-50 text-gray-600 dark:bg-gray-800 dark:text-gray-300">
					<tr>
						<th class="px-4 py-2.5 text-left text-xs font-semibold uppercase tracking-wide">Employee</th>
						<th class="px-4 py-2.5 text-center text-xs font-semibold uppercase tracking-wide">Services</th>
						<th class="px-4 py-2.5 text-right text-xs font-semibold uppercase tracking-wide">Salary</th>
					</tr>
				</thead>
				<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
					@forelse ($employeeSalaries as $salary)
							<tr class="hover:bg-gray-50 dark:hover:bg-gray-800/60">
								<td class="px-4 py-3 font-medium text-gray-950 dark:text-white">{{ $salary['employee_name'] ?? '-' }}</td>
								<td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">{{ $salary['service_count'] }}</td>
								<td class="px-4 py-3 text-right font-semibold text-warning-600 dark:text-warning-400">{{ $currency_symbol }}{{ number_format($salary['total_salary'], 2) }}</td>
						</tr>
					@empty
						<tr><td colspan="3" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">No salary records found.</td></tr>
					@endforelse
				</tbody>
			</table>
			</div>
		</div>

		<div>
			<h3 class="mb-3 text-sm font-semibold text-gray-950 dark:text-white">Salary details</h3>
			{{ $this->table }}
		</div>
	</div>
</x-filament-panels::page>
