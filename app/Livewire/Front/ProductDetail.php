<?php

namespace App\Livewire\Front;

use Livewire\Component;
use App\Models\Product;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

class ProductDetail extends Component
{
    public $product;
    public $quantity = 1;

    public function mount($slug)
    {
        $this->product = Product::with(['category', 'comboItems'])->where('slug', $slug)->firstOrFail();
    }

    public function increment()
    {
        if ($this->product->is_inventoriable && $this->quantity >= $this->product->stock) {
            return;
        }
        $this->quantity++;
    }

    public function decrement()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function addToCart()
    {
        $branchId = session('current_branch') ?? \App\Models\Branch::where('active', true)->first()?->id;
        if ($branchId != $this->product->branch_id) {
            LivewireAlert::title('EL PRODUCTO NO PERTENECE AL SUCURSAL SELECCIONADO')
                ->error()
                ->asToast()
                ->show();
            return;
        }
        if ($this->product->is_inventoriable && $this->product->type === 'product') {
            if ($this->product->stock < $this->quantity) {
                LivewireAlert::title('Stock insuficiente para: ' . $this->product->name . ' (Disponible: ' . $this->product->stock . ')')
                    ->error()
                    ->asToast()
                    ->show();
                return;
            }
        } elseif ($this->product->type === 'combo') {
            foreach ($this->product->comboItems as $component) {
                $requiredStock = $component->pivot->quantity * $this->quantity;
                if ($component->is_inventoriable && $component->stock < $requiredStock) {
                    LivewireAlert::title("Stock insuficiente para el componente: {$component->name} (Disponible: {$component->stock})")
                        ->error()
                        ->asToast()
                        ->show();
                    return;
                }
            }
        }

        $productId = $this->product->id;
        $cart = session()->get('cart', []);

        $comboComponentsFormatted = [];
        if ($this->product->type === 'combo' && $this->product->comboItems) {
            foreach ($this->product->comboItems as $comp) {
                $comboComponentsFormatted[] = [
                    'product_id' => $comp->id,
                    'name' => $comp->name,
                    'quantity' => $comp->pivot->quantity
                ];
            }
        }

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $this->quantity;
        } else {
            $cart[$productId] = [
                'name' => $this->product->name,
                'category' => $this->product->category->name ?? '',
                'price' => $this->product->price,
                'quantity' => $this->quantity,
                'image' => $this->product->image,
                'type' => $this->product->type,
                'combo_components' => $comboComponentsFormatted
            ];
        }

        session()->put('cart', $cart);

        $this->quantity = 1;

        $this->dispatch('cartUpdated')->to(Navigation::class);

        LivewireAlert::title($this->product->name . ' agregado al carrito.')
            ->success()
            ->asToast()
            ->show();
    }

    public function render()
    {
        return view('livewire.front.product-detail');
    }
}
