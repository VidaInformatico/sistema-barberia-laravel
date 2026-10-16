<?php

namespace App\Livewire\Front;

use App\Models\Product;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;

class Cart extends Component
{
    public $cart = [];

    public function mount()
    {
        $this->cart = session()->get('cart', []);
    }

    public function updateQuantity($id, $action)
    {
        if (isset($this->cart[$id])) {
            if ($action === 'inc') {
                $newQuantity = $this->cart[$id]['quantity'] + 1;
                $product = Product::with('comboItems')->find($id);

                if ($product) {
                    if ($product->is_inventoriable && $product->type === 'product') {
                        if ($product->stock < $newQuantity) {
                            LivewireAlert::title('Stock insuficiente para: ' . $product->name . ' (Disponible: ' . $product->stock . ')')
                                ->error()
                                ->asToast()
                                ->show();
                            return;
                        }
                    } elseif ($product->type === 'combo') {
                        foreach ($product->comboItems as $component) {
                            $requiredStock = $component->pivot->quantity * $newQuantity;
                            if ($component->is_inventoriable && $component->stock < $requiredStock) {
                                LivewireAlert::title("Stock insuficiente para el componente: {$component->name} (Disponible: {$component->stock})")
                                    ->error()
                                    ->asToast()
                                    ->show();
                                return;
                            }
                        }
                    }
                }

                $this->cart[$id]['quantity'] = $newQuantity;
            } elseif ($action === 'dec' && $this->cart[$id]['quantity'] > 1) {
                $this->cart[$id]['quantity']--;
            }

            session()->put('cart', $this->cart);
            $this->dispatch('cartUpdated')->to(Navigation::class);
        }
    }

    public function removeItem($id)
    {
        unset($this->cart[$id]);
        session()->put('cart', $this->cart);
        
        $this->dispatch('cartUpdated')->to(Navigation::class);
        
        LivewireAlert::title('Producto eliminado.')
            ->success()
            ->asToast()
            ->show();
    }

    public function clearCart()
    {
        $this->cart = [];

        session()->forget('cart');

        $this->dispatch('cartUpdated')->to(Navigation::class);

        LivewireAlert::title('Carrito vaciado.')
            ->success()
            ->asToast()
            ->show();
    }

    public function checkout() {
        if (!$this->cart) {
            LivewireAlert::title('El carrito está vacío.')
                ->warning()
                ->asToast()
                ->show();
            return;
        }

        foreach ($this->cart as $id => $item) {
            $product = Product::with('comboItems')->find($id);

            if (!$product) {
                LivewireAlert::title("El producto '{$item['name']}' ya no existe.")
                    ->error()
                    ->asToast()
                    ->show();
                return;
            }

            $requestedQuantity = $item['quantity'];

            if ($product->is_inventoriable && $product->type === 'product') {
                if ($product->stock < $requestedQuantity) {
                    LivewireAlert::title('Stock insuficiente para: ' . $product->name . ' (Disponible: ' . $product->stock . ')')
                        ->error()
                        ->asToast()
                        ->show();
                    return;
                }
            } elseif ($product->type === 'combo') {
                foreach ($product->comboItems as $component) {
                    $requiredStock = $component->pivot->quantity * $requestedQuantity;
                    if ($component->is_inventoriable && $component->stock < $requiredStock) {
                        LivewireAlert::title("Stock insuficiente para el componente: {$component->name} en el combo '{$product->name}' (Disponible: {$component->stock})")
                            ->error()
                            ->asToast()
                            ->show();
                        return;
                    }
                }
            }
        }

        return redirect()->route('checkout.index');
    }

    public function render()
    {
        return view('livewire.front.cart');
    }
}