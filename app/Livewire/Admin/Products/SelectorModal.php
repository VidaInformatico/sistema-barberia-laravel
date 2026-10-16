<?php

namespace App\Livewire\Admin\Products;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class SelectorModal extends Component
{
    use WithPagination;

    public $open = false;
    public $search = '';

    protected $listeners = ['openComboModal' => 'openModal'];

    public function updatingSearch()
    {
        $this->resetPage(pageName: 'productsPage');
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->reset(['search']);
        $this->open = true;
    }

    public function closeModal()
    {
        $this->open = false;
    }

    public function selectProduct($productId)
    {
        $this->dispatch('productSelected', productId: $productId);
        $this->closeModal();
    }

    public function render()
    {
        $products = Product::query()
            ->where('type', 'product')
            ->where('branch_id', Auth::user()->branch_id)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('sku', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(5, pageName: 'productsPage');

        return view('livewire.admin.products.selector-modal', compact('products'));
    }
}