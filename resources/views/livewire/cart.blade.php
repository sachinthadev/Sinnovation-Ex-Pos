<div class="">

    @if (session()->has('error'))
        <p class="text-red-500">{{ session('error') }}</p>
    @endif
    <div class="overflow-x-auto md:overflow-x-none">
        <table class="min-w-[600px] min-w-full border border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="px-2 py-2 border border-gray-400 text-left w-3/5 dark:text-gray-800">Item</th>
                    <th class="px-2 py-2 border border-gray-400 text-center w-1/6 dark:text-gray-800">Price</th>
                    <th class="px-2 py-2 border border-gray-400 text-center w-1/6 dark:text-gray-800">Quantity</th>
                    <th class="px-2 py-2 border border-gray-400 text-center w-1/6 dark:text-gray-800">Total</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $total_price = 0;
                    $grand_total = 0;
                @endphp

                @if ( !is_countable($cartItems) || count($cartItems) < 1)
                    <tr class="min-h-32"><td class="p-4" colspan="4">Add Items.</td></tr>
                @else
                    @foreach($cartItems as $item)
                    @php 
                        $item_total = $item->price * $item->quantity;
                        $total_price += $item_total;
                        $grand_total += $item_total;
                    @endphp
                    <livewire:cart-item :cartItem="$item" :currency_symbol="$currency_symbol" :key="$item->id" />
                    @endforeach
                @endif

                @if($newServiceVisible)
                    @php
                        $service_total = floatval($newService['price']) * intval($newService['quantity']);
                    @endphp
                    <tr>
                        <td colspan="4" class="px-2 py-2 border border-gray-400">
                            <div class="flex flex-col gap-2 md:flex-row">
                                <div class="w-full md:w-1/2">
                                    <select wire:model.live="serviceId" class="w-full p-2 border border-gray-300 rounded">
                                        <option value="">Select existing service (optional)</option>
                                        @foreach ($services as $service)
                                            <option value="{{ $service->id }}">{{ $service->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('serviceId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div class="w-full md:w-1/2">
                                    <select wire:model.defer="employeeId" class="w-full p-2 border border-gray-300 rounded">
                                        <option value="">Assign employee</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('employeeId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr class="bg-yellow-50">
                        <td class="px-2 py-1 border border-gray-400">
                            <input wire:model.defer="newService.name" type="text" placeholder="Custom service name" class="w-full p-2 border border-gray-300 rounded" />
                            @error('newService.name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </td>
                        <td class="px-2 py-1 border border-gray-400 text-center">
                            <input wire:model.defer="newService.price" type="number" min="0" step="0.01" class="w-full p-2 border border-gray-300 rounded text-center" />
                            @error('newService.price') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
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
                
                <tr class="border-gray-400 border">
                    <td colspan="3" class="px-4 py-2 border-r text-right font-semibold">Subtotal</td>
                    <td class="px-4 py-2 text-center font-semibold">{{ $currency_symbol }}{{ number_format($total_price, 2) }}</td>
                </tr>

                <tr class="bg-gray-100 border-gray-400 border">
                    <td colspan="3" class="px-4 py-2 border-r text-right font-bold">Grand Total</td>
                    <td class="px-4 py-2 text-center font-bold">{{ $currency_symbol }}{{ number_format($grand_total, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="flex flex-wrap gap-2 mt-3">
        <button wire:click="addServiceRow" type="button" class="bg-blue-500 hover:bg-blue-600 rounded text-white px-4 py-2">
            Add Service
        </button>
        <button wire:click="checkout" wire:loading.attr="disabled" class="bg-green-500 hover:bg-green-600 rounded text-white px-4 py-2">
            <span wire:loading.remove wire:target='checkout'>Save</span>
            <span wire:loading wire:target='checkout' class="w-4 h-4 border-2 border-t-red-100 border-transparent rounded-full animate-spin"></span>
        </button>
    </div>

    @if($showMileagePopup)
    <div class="fixed inset-0 z-50 flex items-center justify-center overflow-auto bg-black bg-opacity-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md p-6 m-4">
            <h3 class="text-lg font-bold mb-4 dark:text-white">Add Vehicle Mileage</h3>
            <p class="mb-4 text-gray-600 dark:text-gray-300">Do you want to add the current vehicle mileage to this invoice?</p>
            
            <input wire:model.defer="mileage" type="text" placeholder="Enter mileage (e.g. 50000 km)" class="w-full p-2 border border-gray-300 rounded mb-4" />
            @error('mileage') <span class="text-red-500 text-sm mb-4 block">{{ $message }}</span> @enderror

            <div class="flex justify-end gap-3 mt-6">
                <button wire:click="confirmCheckoutWithoutMileage" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-black rounded">
                    No, skip mileage
                </button>
                <button wire:click="confirmCheckoutWithMileage" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded">
                    Add & Save
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
