<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Cart as CartModel;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use Livewire\Attributes\On; 

class Cart extends Component
{
    public $cartItems = [];

    public $newServiceVisible = false;
    public $newService = [
        'name' => '',
        'price' => '0.00',
        'quantity' => 1,
    ];

    private $currency_symbol;

    protected $rules = [
        'newService.name' => 'required|string|max:255',
        'newService.price' => 'required|numeric|min:0',
        'newService.quantity' => 'required|integer|min:1',
    ];

    public function mount()
    {
        $this->cartItems = CartModel::with('product')
                            ->where('user_id', auth()->user()->id)
                            ->orderBy('id', 'DESC')
                            ->get();    

        $this->currency_symbol = config('settings.currency_symbol');
    }

    
    public function render()
    {
        return view('livewire.cart', ['cartItems' => $this->cartItems, 'currency_symbol' => $this->currency_symbol]);
    }


    #[On('cartUpdated')] 
    public function updateCart()
    {
        $this->cartItems = CartModel::with('product')
                            ->where('user_id', auth()->user()->id)
                            ->orderBy('id', 'DESC')
                            ->get();

        $this->currency_symbol = config('settings.currency_symbol');

    }

    #[On('openCustomServiceRow')]
    public function openCustomServiceRow()
    {
        $this->addServiceRow();
    }


    #[On('cartUpdatedFromItem')] 
    public function cartUpdatedFromItem()
    {
        $this->cartItems = CartModel::with('product')
                            ->where('user_id', auth()->user()->id)
                            ->orderBy('id', 'DESC')
                            ->get();

        $this->currency_symbol = config('settings.currency_symbol');

    }


    public function checkout(){
        
        $total_price = 0;
        $customerIdentifier =  session('customer_identifier');

        if( empty($customerIdentifier) ){
            return $this->dispatch('error', error: 'Please select customer!');
        }

        $items = $this->cartItems;

        if( ! is_countable( $items ) || count( $items ) < 1){
            return;
        }

        $order = Order::create([
            'customer_identifier' => $customerIdentifier,
            'total_price' => $total_price
        ]);

        

        foreach ($items as $item) {  
            $order->items()->create([
                'name' => $item->name,
                'price' => $item->price,
                'quantity' => $item->quantity,
                'product_id' => $item->product_id,
            ]);

            $total_price += $item->quantity * $item->price;

            if ($item->product_id) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->quantity = $product->quantity - $item->quantity;
                    $product->save();
                }
            }
        }
        
        $order->total_price = $total_price;
        $order->save();

        $this->cartItems = CartModel::where('user_id', auth()->user()->id)
                            ->delete();  

        $this->dispatch('checkout-completed');

        redirect( url('/admin/orders/'. $order->id .'/edit') );

    }

    public function addServiceRow()
    {
        $this->newServiceVisible = true;
        $this->newService = [
            'name' => '',
            'price' => '0.00',
            'quantity' => 1,
        ];
    }

    public function cancelCustomService()
    {
        $this->newServiceVisible = false;
        $this->newService = [
            'name' => '',
            'price' => '0.00',
            'quantity' => 1,
        ];
    }

    public function saveCustomService()
    {
        $this->validate();

        CartModel::create([
            'user_id' => auth()->user()->id,
            'product_id' => null,
            'name' => $this->newService['name'],
            'price' => floatval($this->newService['price']),
            'quantity' => $this->newService['quantity'],
            'tax' => 0,
        ]);

        $this->newServiceVisible = false;
        $this->newService = [
            'name' => '',
            'price' => '0.00',
            'quantity' => 1,
        ];

        $this->updateCart();
    }

}
