
<div class="mx-auto">   
    <div class="relative">
        <input wire:model.live.debounce.250ms="query" type="search" id="default-search" class="p-2 bg-white block w-full text-sm text-gray-900 border border-gray-300 rounded-md bg-gray-50 focus:ring-blue-500 focus:border-blue-500" placeholder="Search product or part number..." />
    </div>
    <div class="mt-4">
        @if(!empty($query))
            @if($products->isNotEmpty())
                <div class="flex flex-col gap-2">
                    @foreach ($products as $product)
                        <button type="button" wire:click="addToCart({{$product->id}})" class="w-full text-left bg-white border border-gray-300 rounded px-3 py-2 hover:bg-gray-50">
                            <div class="flex justify-between items-center gap-2">
                                <span class="text-sm text-gray-700">{{$product->name}}</span>
                                <span class="text-sm font-medium text-gray-900">{{$currency_symbol . $product->price}}</span>
                            </div>
                            <div class="text-xs text-gray-500">Qty: {{$product->quantity}}</div>
                        </button>
                    @endforeach
                </div>
            @else
                <div class="text-sm text-gray-500 mt-2">No products found</div>
            @endif
        @endif
    </div>
</div>
