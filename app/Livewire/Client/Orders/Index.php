<?php

namespace App\Livewire\Client\Orders;

use App\Models\Order;
use App\Models\OrderState;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithPagination;

    #[Url(keep: true)]
    public $search = '';

    #[Url(keep: true)]
    public $selectedStatus = '';

    #[Url(keep: true)]
    public $selectedDate = '';

    public $isDetailOpen = false;
    public ?Order $selectedOrder = null;

    public function showOrder($id)
    {
        $this->selectedOrder = Order::with([
            'state',
            'details.product',
            'trackings.state',
            'trackings.user'
        ])
        ->where('client_id', Auth::id())
        ->find($id);

        if ($this->selectedOrder) {
            $this->isDetailOpen = true;
        }
    }

    public function closeDetailModal()
    {
        $this->isDetailOpen = false;
        $this->selectedOrder = null;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedStatus()
    {
        $this->resetPage();
    }

    public function updatingSelectedDate()
    {
        $this->resetPage();
    }

    public function render()
    {
        $orders = Order::query()
            ->with(['state', 'details'])
            ->where('client_id', Auth::id())
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('order_number', 'like', '%' . $this->search . '%')
                        ->orWhere('payment_id', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->selectedStatus, fn($q) => $q->where('order_state_id', $this->selectedStatus))
            ->when($this->selectedDate, fn($q) => $q->whereDate('created_at', $this->selectedDate))
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.client.orders.index', [
            'orders' => $orders,
            'orderStates' => OrderState::all(),
        ]);
    }
}