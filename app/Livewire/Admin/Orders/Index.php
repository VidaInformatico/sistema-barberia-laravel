<?php

namespace App\Livewire\Admin\Orders;

use App\Models\Order;
use App\Models\OrderState;
use App\Models\OrderTracking;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    public $new_state_id = '';
    public $tracking_comment = '';

    public function showOrder($id)
    {
        $this->selectedOrder = Order::with([
            'state',
            'details.product',
            'trackings.state',
            'trackings.user'
        ])
        ->find($id);

        if ($this->selectedOrder) {
            $this->new_state_id = $this->selectedOrder->order_state_id;
            $this->tracking_comment = '';
            $this->resetValidation();
            $this->isDetailOpen = true;
        }
    }

    public function closeDetailModal()
    {
        $this->isDetailOpen = false;
        $this->selectedOrder = null;
        $this->new_state_id = '';
        $this->tracking_comment = '';
        $this->resetValidation();
    }

    public function addTracking()
    {
        if (!$this->selectedOrder) {
            return;
        }

        $this->validate([
            'new_state_id'     => ['required', 'exists:order_states,id'],
            'tracking_comment' => ['nullable', 'string', 'max:500'],
        ], [
            'new_state_id.required' => 'Debe seleccionar un estado.',
            'new_state_id.exists'   => 'El estado seleccionado no es válido.',
        ]);

        DB::transaction(function () {
            // 1. Crear el historial de seguimiento (OrderTracking)
            OrderTracking::create([
                'order_id'       => $this->selectedOrder->id,
                'order_state_id' => $this->new_state_id,
                'user_id'        => Auth::id(),
                'comment'        => $this->tracking_comment,
            ]);

            $this->selectedOrder->update([
                'order_state_id' => $this->new_state_id,
            ]);
        });

        $this->selectedOrder->load(['state', 'trackings.state', 'trackings.user']);
        $this->tracking_comment = '';

        $this->dispatch('swal', [
            'title' => '¡Seguimiento Registrado!',
            'text'  => 'El estado del pedido y su historial se actualizaron correctamente.',
            'icon'  => 'success',
        ]);
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
            ->where('branch_id', Auth::user()->branch_id)
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

        return view('livewire.admin.orders.index', [
            'orders'      => $orders,
            'orderStates' => OrderState::orderBy('name', 'asc')->get(),
        ]);
    }
}