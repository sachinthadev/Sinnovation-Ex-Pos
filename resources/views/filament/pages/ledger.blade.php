<x-filament-panels::page>
	<div class="space-y-5">
		<div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
			<div>
				<h2 class="text-lg font-semibold text-gray-950 dark:text-white">Employee Salary</h2>
				<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Commission summary for {{ $selectedMonth }}.</p>
			</div>
			<div class="rounded-lg border border-gray-200 bg-white px-4 py-2 text-left shadow-sm sm:text-right dark:border-gray-700 dark:bg-gray-900">
				<div class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Total salary</div>
				<div class="text-xl font-bold text-warning-600 dark:text-warning-400">
					{{ $currency_symbol }}{{ number_format($totalSalary, 2) }}
				</div>
			</div>
		</div>

		<div class="flex items-end justify-end">
				<div class="w-full md:w-56">
					<label for="ledger-month" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">Month</label>
					<select id="ledger-month" wire:model.live="month" class="w-full rounded-lg border-gray-300 bg-white text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
						@foreach ($monthOptions as $value => $label)
							<option value="{{ $value }}">{{ $label }}</option>
						@endforeach
					</select>
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
