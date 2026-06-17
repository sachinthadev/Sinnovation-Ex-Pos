<?php

namespace App\Livewire\Order;

use Livewire\Component;
use App\Models\Cart as CartModel;
use App\Models\Order;
use App\Models\OrderItem;
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

    public $orderId;

    protected $rules = [
        'newService.name' => 'required|string|max:255',
        'newService.price' => 'required|numeric|min:0',
        'newService.quantity' => 'required|integer|min:1',
    ];

    public function mount($orderId)
    {
        $this->orderId = $orderId;  

        $this->cartItems = OrderItem::where('order_id', $orderId)            
                            ->orderBy('id', 'ASC')
                            ->get();    
        $this->currency_symbol = config('settings.currency_symbol');
    }

    
    public function render()
    {
        $this->currency_symbol = config('settings.currency_symbol');
        return view('livewire.order.cart', ['cartItems' => $this->cartItems, 'currency_symbol' => $this->currency_symbol]);
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

        OrderItem::create([
            'order_id' => $this->orderId,
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

    #[On('cartUpdated')]
    public function updateCart()
    {
        $this->cartItems = OrderItem::where('order_id', $this->orderId)            
                                        ->orderBy('id', 'ASC')
                                        ->get();

        $order = Order::find($this->orderId);
        
        $total_price = 0;
        foreach($this->cartItems as $item){ 
            $total_price += $item->quantity * $item->price;
        }
        $order->total_price = $total_price;
        $order->save();

    }


    #[On('cartUpdatedFromItem')] 
    public function cartUpdatedFromItem()
    {
        $this->cartItems = OrderItem::where('order_id', $this->orderId)            
                                        ->orderBy('id', 'ASC')
                                        ->get();

        $order = Order::find($this->orderId);

        $total_price = 0;
        foreach($this->cartItems as $item){ 
            $total_price += $item->quantity * $item->price;
        }
        $order->total_price = $total_price;
        $order->save();
                                
    }

    #[On('openCustomServiceRow')]
    public function openCustomServiceRow()
    {
        $this->addServiceRow();
    }

    public function checkout(){ 
        return $this->redirect( url('admin/orders') );
    }

}
