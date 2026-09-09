<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Cart as CartModel;
use App\Models\Order;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\Employee;
use App\Models\Service;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On; 

class Cart extends Component
{
    public $cartItems = [];

    public $showMileagePopup = false;
    public $mileage = '';

    public $newServiceVisible = false;
    public $newService = [
        'name' => '',
        'price' => '0.00',
        'quantity' => 1,
        'commission_percentage' => 50,
    ];

    public $serviceId = '';
    public $employeeId = '';

    private $currency_symbol;

    protected $rules = [
        'newService.name' => 'required|string|max:255',
        'newService.price' => 'required|numeric|min:0',
        'newService.quantity' => 'required|integer|min:1',
        'serviceId' => 'nullable|exists:services,id',
        'employeeId' => 'required|exists:employees,id',
    ];

    public function mount()
    {
        $this->cartItems = CartModel::with('product')
                            ->where('user_id', Auth::id())
                            ->orderBy('id', 'ASC')
                            ->get();    

        $this->currency_symbol = config('settings.currency_symbol');
    }

    
    public function render()
    {
        return view('livewire.cart', [
            'cartItems' => $this->cartItems,
            'currency_symbol' => $this->currency_symbol,
            'services' => Service::query()
                ->where('type', 'service')
                ->where('status', true)
                ->orderBy('name')
                ->get(),
            'employees' => Employee::query()->orderBy('name')->get(),
        ]);
    }


    #[On('cartUpdated')] 
    public function updateCart()
    {
        $this->cartItems = CartModel::with('product')
                            ->where('user_id', Auth::id())
                            ->orderBy('id', 'ASC')
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
                            ->where('user_id', Auth::id())
                            ->orderBy('id', 'ASC')
                            ->get();

        $this->currency_symbol = config('settings.currency_symbol');

    }


    public function checkout(){
        
        $customerIdentifier =  session('customer_identifier');

        if( empty($customerIdentifier) ){
            return $this->dispatch('error', error: 'Please select customer!');
        }

        $items = $this->cartItems;

        if( ! is_countable( $items ) || count( $items ) < 1){
            return;
        }

        $this->mileage = '';
        $this->showMileagePopup = true;
    }

    public function confirmCheckoutWithMileage()
    {
        $this->validate(['mileage' => 'required|string|max:255']);
        $this->showMileagePopup = false;
        $this->processCheckout($this->mileage);
    }

    public function confirmCheckoutWithoutMileage()
    {
        $this->showMileagePopup = false;
        $this->processCheckout(null);
    }

    private function processCheckout($mileage)
    {
        $total_price = 0;
        $customerIdentifier =  session('customer_identifier');
        $items = $this->cartItems;

        $nowSriLanka = Carbon::now('Asia/Colombo');

        $order = Order::create([
            'customer_identifier' => $customerIdentifier,
            'total_price' => $total_price,
            'mileage' => $mileage,
            'user_id' => Auth::id(),
            'created_at' => $nowSriLanka,
            'updated_at' => $nowSriLanka,
        ]);

        foreach ($items as $item) {  
            $order->items()->create([
                'name' => $item->name,
                'price' => $item->price,
                'quantity' => $item->quantity,
                'product_id' => $item->product_id,
                'service_id' => $item->service_id,
                'employee_id' => $item->employee_id,
                'commission_percentage' => $item->commission_percentage,
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

        $this->cartItems = CartModel::where('user_id', Auth::id())->delete();

        // Clear the selected customer so the next invoice starts with a blank search
        session()->forget('customer_identifier');

        $this->dispatch('checkout-completed');

        redirect( url('/admin/orders/'. $order->id .'/edit') );
    }

    public function addServiceRow()
    {
        $this->newServiceVisible = true;
        $this->serviceId = '';
        $this->employeeId = '';
        $this->newService = [
            'name' => '',
            'price' => '0.00',
            'quantity' => 1,
            'commission_percentage' => 50,
        ];
    }

    public function updatedServiceId($serviceId)
    {
        $service = Service::query()
            ->where('type', 'service')
            ->where('status', true)
            ->find($serviceId);

        if (! $service) {
            $this->newService['name'] = '';
            $this->newService['price'] = '0.00';
            $this->newService['commission_percentage'] = 50;

            return;
        }

        $this->newService['name'] = $service->name;
        $this->newService['price'] = $service->price;
        $this->newService['commission_percentage'] = (float) ($service->commission_percentage ?? 0);
    }

    public function cancelCustomService()
    {
        $this->newServiceVisible = false;
        $this->serviceId = '';
        $this->employeeId = '';
        $this->newService = [
            'name' => '',
            'price' => '0.00',
            'quantity' => 1,
            'commission_percentage' => 50,
        ];
    }

    public function saveCustomService()
    {
        $this->validate();

        $service = $this->serviceId
            ? Service::query()
                ->where('type', 'service')
                ->where('status', true)
                ->find($this->serviceId)
            : null;

        if ($this->serviceId && ! $service) {
            return $this->addError('serviceId', 'Please select an active service.');
        }

        $commissionPercentage = $service
            ? (float) ($service->commission_percentage ?? 0)
            : 50;

        CartModel::create([
            'user_id' => Auth::id(),
            'product_id' => null,
            'service_id' => $this->serviceId ?: null,
            'employee_id' => $this->employeeId,
            'commission_percentage' => $commissionPercentage,
            'name' => $this->newService['name'],
            'price' => floatval($this->newService['price']),
            'quantity' => $this->newService['quantity'],
            'tax' => 0,
        ]);

        $this->newServiceVisible = false;
        $this->serviceId = '';
        $this->employeeId = '';
        $this->newService = [
            'name' => '',
            'price' => '0.00',
            'quantity' => 1,
            'commission_percentage' => 50,
        ];

        $this->updateCart();
    }

}
