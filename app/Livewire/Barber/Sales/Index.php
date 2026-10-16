<?php

namespace App\Livewire\Barber\Sales;

use App\Models\Sale;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $fromDate = '';
    public $toDate = '';

    public $viewingSale = null;

    public function showDetails($id)
    {
        $this->viewingSale = Sale::with(['items.product', 'items.comboItems', 'client', 'barber.user', 'creator'])->findOrFail($id);
    }

    public function closeDetails()
    {
        $this->viewingSale = null;
    }

    public function render()
    {
        $sales = Sale::with(['client', 'branch', 'barber.user', 'creator'])
            ->where('barber_id', Auth::user()->barber->id)
            ->where(function ($query) {
                $query->where('sale_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('client', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('barber.user', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->fromDate, function ($q) {
                $q->whereDate('created_at', '>=', $this->fromDate);
            })
            ->when($this->toDate, function ($q) {
                $q->whereDate('created_at', '<=', $this->toDate);
            })
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        return view('livewire.barber.sales.index', compact('sales'));
    }
}
