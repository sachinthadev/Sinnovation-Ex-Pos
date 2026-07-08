<?php

namespace Tests\Feature;

use App\Livewire\Order\CartItem;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class OrderCartItemTest extends TestCase
{
    public function test_remove_from_cart_handles_missing_item_gracefully(): void
    {
        Auth::shouldReceive('user')->andReturn((object) ['role' => 'admin']);

        $component = new CartItem();
        $component->cartItem = null;
        $component->quantity = 2;

        $component->removeFromCart();

        $this->assertNull($component->cartItem);
        $this->assertSame(0, $component->quantity);
    }
}
