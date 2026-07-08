<?php

namespace App\Livewire\Order;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\On; 

class CartItem extends Component
{
    public $cartItem;

    public $currency_symbol;

    public $quantity;

    public $orderId;


    public function mount($cartItem, $orderId)
    {
        $this->orderId = $orderId;
        $this->cartItem = $cartItem;
        $this->quantity = $cartItem?->quantity ?? 0;
    }

    #[On('cartUpdated')]
    public function cartUpdated()
    {
        if (! $this->cartItem) {
            $this->quantity = 0;
            return;
        }

        $this->quantity = $this->cartItem->quantity;
    }


    public function removeFromCart()
    {
        if (Auth::user()?->role !== 'admin') {
            return;
        }

        if (! $this->cartItem) {
            $this->quantity = 0;
            $this->dispatch('cartUpdatedFromItem');
            return;
        }

        $product = Product::find($this->cartItem->product_id);
        if ($product) {
            $product->quantity = $product->quantity + $this->quantity;
            $product->save();
        }

        $this->quantity = 0;
        $this->cartItem->delete();
        $this->cartItem = null;
        $this->dispatch('cartUpdatedFromItem');
    }


    public function updated()
    {
        if (! $this->cartItem) {
            $this->quantity = 0;
            $this->dispatch('cartUpdatedFromItem');
            return;
        }

        if ($this->quantity > 0) {
            $product = Product::find($this->cartItem->product_id);
            if (! $product) {
                $this->quantity = 0;
                $this->dispatch('cartUpdatedFromItem');
                return;
            }

            $product->quantity = $product->quantity + $this->cartItem->quantity;

            if ($product->quantity < $this->quantity) {
                $this->quantity = $product->quantity;
            }

            $product->save();

            $this->cartItem->quantity = $this->quantity;
            $this->cartItem->save();

            $product->quantity = $product->quantity - $this->quantity;
            $product->save();
        }

        if (is_numeric($this->quantity) && $this->quantity <= 0) {
            $this->quantity = 1;
        }

        $this->dispatch('cartUpdatedFromItem');
    }

    public function render()
    {      
        return view('livewire.order.cart-item');
    }
}
