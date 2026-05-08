<div class="">

    @if (session()->has('error'))
        <p class="text-red-500">{{ session('error') }}</p>
    @endif
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
