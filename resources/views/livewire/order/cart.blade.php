<div class="" x-data="{ showConfirm: false, pendingChecked: false }">

    @if (session()->has('error'))
        <p class="text-red-500">{{ session('error') }}</p>
    @endif

    <div class="flex flex-wrap gap-3 mb-3 items-center">
        <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-sm font-medium text-slate-800">
            Products added: {{ $productCount }}
        </span>
        <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-sm font-medium text-slate-800">
            Services added: {{ $serviceCount }}
        </span>
        <label class="inline-flex items-center gap-2 rounded-full border border-green-200 bg-green-50 px-3 py-1 text-sm font-medium text-green-700">
            <input type="checkbox" class="rounded border-gray-300 text-green-600 focus:ring-green-500" @checked($isSettled) x-on:change="if (event.target.checked) { pendingChecked = true; showConfirm = true; event.target.checked = false; } else { $wire.setSettlementStatus(false); }">
            <span>Verify</span>
        </label>

        <div x-show="showConfirm" x-transition class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-900/60 px-4 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-2xl border border-gray-200 bg-white p-6 shadow-2xl dark:border-gray-700 dark:bg-gray-900">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-green-100 text-green-600 dark:bg-green-500/10 dark:text-green-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-7.5 7.5a1 1 0 01-1.414 0l-3.5-3.5a1 1 0 111.414-1.414L8.5 12.086l6.793-6.793a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Settle this order?</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">This will mark the order as cash received.</p>
                    </div>
                </div>

                <div class="mt-6 flex flex-wrap justify-end gap-2">
                    <button type="button" @click="showConfirm = false; pendingChecked = false" class="inline-flex min-w-[90px] items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-100 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                        Cancel
                    </button>
                    <button type="button" @click="showConfirm = false; $wire.setSettlementStatus(true); window.location.reload();" class="inline-flex min-w-[90px] items-center justify-center rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-green-700">
                        Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto md:overflow-x-none">
        <table class="min-w-[600px] min-w-full border border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="px-2 py-1 border border-gray-400 text-left w-3/5 dark:text-gray-800">Item</th>
                    <th class="px-2 py-1 border border-gray-400 text-center w-1/6 dark:text-gray-800">Price</th>
                    <th class="px-2 py-1 border border-gray-400 text-center w-1/6 dark:text-gray-800">Quantity</th>
                    <th class="px-2 py-1 border border-gray-400 text-center w-1/6 dark:text-gray-800">Total</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $total_price = 0;
                    $grand_total = 0;
                @endphp

                @if ( !is_countable($cartItems) || count($cartItems) < 1)
                    <tr class="min-h-32"><td class="p-4" colspan="4">Add Items.</td></tr>
                @endif

                @foreach ($cartItems as $item) 
                @php 
                    $item_total = $item->price * $item->quantity;
                    $total_price += $item_total;
                    $grand_total += $item_total;
                @endphp
                    <livewire:order.cart-item :cartItem="$item" :currency_symbol="$currency_symbol" :order-id="$orderId" :key="$item->id" />
                @endforeach

                @if($newServiceVisible)
                    @php
                        $service_total = floatval($newService['price']) * intval($newService['quantity']);
                    @endphp
                    <tr class="bg-yellow-50">
                        <td class="px-2 py-1 border border-gray-400">
                            <input wire:model.defer="newService.name" type="text" placeholder="Custom service name" class="w-full p-2 border border-gray-300 rounded" />
                        </td>
                        <td class="px-2 py-1 border border-gray-400 text-center">
                            <input wire:model.defer="newService.price" type="number" min="0" step="0.01" class="w-full p-2 border border-gray-300 rounded text-center" />
                        </td>
                        <td class="px-2 py-1 border border-gray-400 text-center">
                            <input wire:model.defer="newService.quantity" type="number" min="1" class="w-full p-2 border border-gray-300 rounded text-center" />
                        </td>
                        <td class="px-2 py-1 border border-gray-400 text-center font-semibold">{{ $currency_symbol }}{{ number_format($service_total, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="4" class="px-2 py-2">
                            <div class="flex flex-wrap gap-2 justify-end">
                                <button wire:click="saveCustomService" wire:loading.attr="disabled" class="bg-green-500 hover:bg-green-600 rounded text-white px-4 py-2">
                                    Add Service
                                </button>
                                <button wire:click="cancelCustomService" type="button" class="bg-gray-300 hover:bg-gray-400 rounded text-black px-4 py-2">
                                    Cancel
                                </button>
                            </div>
                        </td>
                    </tr>
                @endif

                @if(count($cartItems) > 0 || $newServiceVisible)
                    <tr class="border-gray-400 border">
                        <td colspan="3" class="px-4 py-2 border-r text-right font-semibold">Subtotal</td>
                        <td class="px-4 py-2 text-center font-semibold">{{ $currency_symbol }}{{ number_format($total_price, 2) }}</td>
                    </tr>

                    <tr class="bg-gray-100 border-gray-400 border">
                        <td colspan="3" class="px-4 py-2 border-r text-right font-bold">Grand Total</td>
                        <td class="px-4 py-2 text-center font-bold">{{ $currency_symbol }}{{ number_format($grand_total, 2) }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="flex flex-wrap gap-2 mt-3">
        <button wire:click="addServiceRow" type="button" class="bg-blue-500 hover:bg-blue-600 rounded text-white px-4 py-2">
            Add Service
        </button>
        <button wire:click="checkout" class="bg-green-500 rounded text-white px-4 py-2">
            <svg class="size-6 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
        </button>
    </div>

</div>
