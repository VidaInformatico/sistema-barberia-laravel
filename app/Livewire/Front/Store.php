<?php

namespace App\Livewire\Front;

use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductCategory;
use Livewire\Component;
use Livewire\WithPagination;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

class Store extends Component
{
    use WithPagination;

    public $selectedCategory = null;
    public $selectedBrands = [];
    public $sort = 'created_at_desc';
    public $search = '';

    public function updating($name)
    {
        if (in_array($name, ['selectedCategory', 'selectedBrands', 'sort', 'search'])) {
            $this->resetPage();
        }
    }

    public function addToCart($productId)
    {
        $product = Product::with('comboItems')->findOrFail($productId);
        $cart = session()->get('cart', []);

        $currentQuantityInCart = isset($cart[$productId]) ? $cart[$productId]['quantity'] : 0;
        $requestedQuantity = $currentQuantityInCart + 1;

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
                    LivewireAlert::title("Stock insuficiente para el componente: {$component->name} (Disponible: {$component->stock})")
                        ->error()
                        ->asToast()
                        ->show();
                    return;
                }
            }
        }

        $comboComponentsFormatted = [];
        if ($product->type === 'combo' && $product->comboItems) {
            foreach ($product->comboItems as $comp) {
                $comboComponentsFormatted[] = [
                    'product_id' => $comp->id,
                    'name' => $comp->name,
                    'quantity' => $comp->pivot->quantity
                ];
            }
        }

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity']++;
        } else {
            $cart[$productId] = [
                'name' => $product->name,
                'category' => $product->category->name ?? '',
                'price' => $product->price,
                'quantity' => 1,
                'image' => $product->image,
                'type' => $product->type,
                'combo_components' => $comboComponentsFormatted
            ];
        }

        session()->put('cart', $cart);

        $this->dispatch('cartUpdated')->to(Navigation::class);

        LivewireAlert::title($product->name . ' agregado al carrito.')
            ->success()
            ->asToast()
            ->show();
    }

    public function render()
    {
        $categories = ProductCategory::where('type', 'product')
            ->where('is_active', true)
            ->withCount('products')->get();

        $brands = Brand::where('is_active', true)
            ->withCount('products')->get();

        $branchId = session('current_branch') ?? \App\Models\Branch::where('active', true)->first()?->id;

        $sortParts = explode('_', trim($this->sort), 2);
        $sortField = $sortParts[0] ?? 'created_at';
        $sortDirection = $sortParts[1] ?? 'desc';

        $sortDirection = in_array(strtolower($sortDirection), ['asc', 'desc'])
            ? strtolower($sortDirection)
            : 'desc';

        $allowedFields = ['created_at', 'price', 'name'];
        if (!in_array($sortField, $allowedFields)) {
            $sortField = 'created_at';
        }

        $products = Product::query()
            ->whereIn('type', ['product', 'combo'])
            ->where('branch_id', $branchId)
            ->where('is_active', true)
            ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
            ->when($this->selectedCategory, fn($q) => $q->where('category_id', $this->selectedCategory))
            ->when(!empty($this->selectedBrands), fn($q) => $q->whereIn('brand_id', $this->selectedBrands))
            ->orderBy($sortField, $sortDirection)
            ->paginate(9);

        return view('livewire.front.store', compact('categories', 'brands', 'products'));
    }
}
