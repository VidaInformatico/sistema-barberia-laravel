<?php

namespace App\Livewire\Admin\Products;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Movement extends Component
{
    use WithPagination;

    public $movement_id = null;
    public $product_id;
    public $user_id;
    public $branch_id;
    public $type = 'in'; // 'in' = Entrada, 'out' = Salida
    public $quantity;
    public $stock_before;
    public $stock_after;
    public $reason;

    public $reference_id = null;
    public $reference_type = null;

    public $current_stock = 0;
    public $projected_stock = 0;
    public $responsible_user_name = '';

    public $search = '';
    public $filterType = '';
    public $perPage = 10;
    public $isOpen = false;

    public $products = [];

    public function mount()
    {
        $this->products = Product::where('type', 'product')->where('branch_id', Auth::user()->branch_id)->where('is_active', true)->orderBy('name', 'asc')->get();
    }

    public function updatedProductId($value)
    {
        if ($value) {
            $product = Product::find($value);
            $this->current_stock = $product ? $product->stock : 0;
        } else {
            $this->current_stock = 0;
        }
        $this->calculateProjectedStock();
    }

    public function updatedType()
    {
        $this->calculateProjectedStock();
    }

    public function updatedQuantity()
    {
        $this->calculateProjectedStock();
    }

    private function calculateProjectedStock()
    {
        $qty = is_numeric($this->quantity) ? (int)$this->quantity : 0;

        if ($this->type === 'in') {
            $this->projected_stock = $this->current_stock + $qty;
        } else {
            $this->projected_stock = $this->current_stock - $qty;
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterType()
    {
        $this->resetPage();
    }

    public function render()
    {
        $branchId = auth()->user()->branch_id;
        $movements = StockMovement::with(['product', 'user', 'reference'])
            ->where('branch_id', $branchId)
            ->where(function ($query) {
                $query->where('reason', 'like', '%' . $this->search . '%')
                    ->orWhere('type', 'like', '%' . $this->search . '%')
                    ->orWhereHas('product', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('sku', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('user', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('last_name', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->filterType, function ($query) {
                $query->where('type', $this->filterType);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin.products.movement', compact('movements'));
    }

    public function create()
    {
        $this->resetInputFields();
        $this->responsible_user_name = Auth::user()->name;
        $this->openModal();
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->reset([
            'movement_id',
            'product_id',
            'user_id',
            'quantity',
            'stock_before',
            'stock_after',
            'reason',
            'reference_id',
            'reference_type',
            'current_stock',
            'projected_stock',
            'responsible_user_name'
        ]);
        $this->type = 'in';
        $this->resetValidation();
    }

    public function store()
    {
        $rules = [
            'product_id' => ['required', 'exists:products,id'],
            'type'       => ['required', 'in:in,out'],
            'quantity'   => ['required', 'integer', 'min:1'],
            'reason'     => ['required', 'string', 'max:255'],
        ];

        $this->validate($rules);

        $product = Product::findOrFail($this->product_id);

        if ($this->type === 'out' && $product->stock < $this->quantity) {
            $this->addError('quantity', 'El stock actual (' . $product->stock . ') es insuficiente para realizar esta salida.');
            return;
        }

        $this->user_id = Auth::id();
        $this->branch_id = Auth::user()->branch_id;

        if ($this->type === 'in') {
            $movement = $product->incrementStock($this->quantity, '[Ajuste Manual] ' . $this->reason);
        } else {
            $movement = $product->decrementStock($this->quantity, '[Ajuste Manual] ' . $this->reason);
        }

        $this->stock_before = $movement->stock_before;
        $this->stock_after  = $movement->stock_after;

        $this->dispatch('swal', [
            'title' => '¡Inventario Ajustado!',
            'text'  => 'El stock se actualizó y el movimiento quedó registrado bajo auditoría.',
            'icon'  => 'success',
        ]);

        $this->closeModal();
    }
}
