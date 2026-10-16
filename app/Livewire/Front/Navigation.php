<?php

namespace App\Livewire\Front;

use App\Models\Branch;
use App\Models\Product;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;

class Navigation extends Component
{
    public $cart = [];
    public $searchQuery = '';
    public $searchResults = [];
    public $currentBranchId;

    protected $listeners = ['cartUpdated' => 'refreshCart'];

    public function mount()
    {
        $this->refreshCart();
        $this->currentBranchId = session('current_branch') ?? Branch::where('active', true)->first()?->id;
    }

    public function updatedSearchQuery($value)
    {
        if (strlen(trim($value)) < 2) {
            $this->searchResults = [];
            return;
        }

        $this->searchResults = Product::with('branch')
            ->where('is_active', true)
            ->where(function ($query) use ($value) {
                $query->where('name', 'like', '%' . $value . '%')
                    ->orWhere('sku', 'like', '%' . $value . '%');
            })
            ->take(5)
            ->get();
    }

    public function setBranch($branchId)
    {
        $cart = session()->get('cart', []);

        if (!empty($cart)) {
            LivewireAlert::title('No puedes cambiar de sucursal')
                ->text('Tienes productos en el carrito de otra sucursal. Vacía tu carrito antes de cambiar.')
                ->warning()
                ->asToast()
                ->show();

            return;
        }

        $branch = Branch::find($branchId);

        if ($branch) {
            session(['current_branch' => $branch->id]);
            $this->currentBranchId = $branch->id;

            LivewireAlert::title('Sucursal Cambiada')
                ->success()
                ->asToast()
                ->show();

            return redirect(request()->header('Referer'));
        }
    }

    public function refreshCart()
    {
        $this->cart = session()->get('cart', []);
    }

    public function updateQuantity($productId, $type)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            if ($type === 'inc') {
                $cart[$productId]['quantity']++;
            } elseif ($type === 'dec') {
                $cart[$productId]['quantity']--;
                if ($cart[$productId]['quantity'] <= 0) {
                    unset($cart[$productId]);
                }
            }
        }

        session()->put('cart', $cart);
        $this->cart = $cart;
    }

    public function removeItem($productId)
    {
        $cart = session()->get('cart', []);
        unset($cart[$productId]);
        session()->put('cart', $cart);
        $this->cart = $cart;
        LivewireAlert::title('Producto eliminado del carrito.')
            ->success()
            ->asToast()
            ->show();
    }

    public function render()
    {
        return view('livewire.front.navigation', [
            'branches' => Branch::where('active', true)->get(),
            'currentBranch' => Branch::find($this->currentBranchId)
        ]);
    }
}
