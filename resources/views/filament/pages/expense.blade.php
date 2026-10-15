<x-filament-panels::page>
	<div class="space-y-5">
		<div class="flex flex-col gap-4 rounded-xl border border-gray-200 bg-white p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between dark:border-gray-700 dark:bg-gray-900">
			<div class="flex items-center gap-3">
				<div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-danger-50 text-danger-600 dark:bg-danger-400/10 dark:text-danger-400">
					<x-filament::icon icon="heroicon-o-calendar-days" class="size-5" />
				</div>
				<div>
					<h2 class="text-sm font-semibold text-gray-950 dark:text-white">Expenses by month</h2>
					<p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">Choose a month to view recorded expenses</p>
				</div>
			</div>
			<div class="w-full sm:w-60">
				<label for="expense-month" class="mb-1.5 block text-xs font-medium text-gray-500 dark:text-gray-400">Reporting month</label>
				<div class="relative">
					<select id="expense-month" wire:model.live="month" class="w-full appearance-none rounded-lg border-gray-300 bg-gray-50 py-2.5 pl-3 pr-10 text-sm font-medium text-gray-950 shadow-sm transition focus:border-primary-500 focus:bg-white focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:focus:bg-gray-800">
						@foreach ($monthOptions as $value => $label)
							<option value="{{ $value }}">{{ $label }}</option>
						@endforeach
					</select>
					<x-filament::icon icon="heroicon-m-chevron-down" class="pointer-events-none absolute right-3 top-1/2 size-4 -translate-y-1/2 text-gray-400" />
				</div>
			</div>
		</div>

		{{ $this->table }}
	</div>
</x-filament-panels::page>