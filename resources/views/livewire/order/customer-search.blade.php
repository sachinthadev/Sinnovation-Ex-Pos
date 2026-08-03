<div class="relative w-full">
    <input 
        type="text" 
        wire:keydown.Backspace="clear"
        wire:keydown.Delete="clear"
        wire:model.live.debounce.250ms="query" 
        class="p-2 bg-white block w-full text-sm text-gray-900 border border-gray-300 rounded-md bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
        placeholder="Search customer name, vehicle number or chassis number..."
    />

    @if($selectedCustomer)
        <div class="absolute top-0 left-0 px-1 py-1 mx-1 my-1 rounded-md bg-gray-100 text-sm text-gray-900">
            @if(($selectedCustomer->first_name ?? '') || ($selectedCustomer->last_name ?? ''))
                <div class="font-semibold text-gray-900">{{ $selectedCustomer->vehicle_identifier }}</div>
                <div class="text-gray-600">{{ trim(($selectedCustomer->first_name ?? '') . ' ' . ($selectedCustomer->last_name ?? '')) }}</div>
            @else
                <div class="font-semibold text-gray-900">{{ $selectedCustomer->vehicle_identifier }}</div>
            @endif
        </div>
    @endif

    @if($showDropdown && $query)
        <ul class="absolute left-0 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-md z-10">
            @foreach($customers as $customer)
                <li wire:click="selectCustomer({{ $customer->id }})" 
                    class="px-4 py-2 cursor-pointer text-gray-900 hover:bg-blue-100">
                    <div class="font-semibold text-gray-900">{{ $customer->vehicle_identifier }}</div>
                    <div class="text-sm text-gray-600">{{ trim($customer->first_name . ' ' . $customer->last_name) ?: 'No customer name' }}</div>
                </li>
            @endforeach
        </ul>
    @endif
</div>
