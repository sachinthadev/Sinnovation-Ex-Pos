<x-filament-panels::page>
    <div class="space-y-6">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Order Details</h2>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">View the order summary and continue to edit when needed.</p>
                </div>
            </div>

            <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/60">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Order ID</p>
                    <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">#{{ $this->record->id }}</p>
                </div>
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/60">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Customer</p>
                    <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ $this->record->getCustomerName() }}</p>
                </div>
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/60">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Vehicle Number</p>
                    <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ $this->record->customer?->vehicle_identifier ?? '—' }}</p>
                </div>
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/60">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</p>
                    <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ $this->record->settlement_status_label }}</p>
                </div>
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/60">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Mileage</p>
                    <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ $this->record->mileage ?? '—' }}</p>
                </div>
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800/60">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total</p>
                    <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ config('settings.currency_symbol') }}{{ number_format($this->record->total_price, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Items</h3>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead>
                        <tr>
                            <th class="px-3 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Item</th>
                            <th class="px-3 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Price</th>
                            <th class="px-3 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Qty</th>
                            <th class="px-3 py-2 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($this->record->items as $item)
                            <tr>
                                <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">{{ $item->name }}</td>
                                <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">{{ config('settings.currency_symbol') }}{{ number_format($item->price, 2) }}</td>
                                <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">{{ $item->quantity }}</td>
                                <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300">{{ config('settings.currency_symbol') }}{{ number_format($item->price * $item->quantity, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-3 py-4 text-sm text-gray-500 dark:text-gray-400">No items found for this order.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
