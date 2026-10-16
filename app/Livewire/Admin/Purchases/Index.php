<?php

namespace App\Livewire\Admin\Purchases;

use App\Models\CashRegister;
use App\Models\Expense;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $fromDate = '';
    public $toDate = '';

    public $viewingPurchase = null;

    public function showDetails($id)
    {
        $this->viewingPurchase = Purchase::with(['items.product', 'supplier', 'user'])->findOrFail($id);
    }

    public function closeDetails()
    {
        $this->viewingPurchase = null;
    }

    public function deleteConfirm($id)
    {
        $this->dispatch('confirm-void', id: $id);
    }

    #[On('void-confirmed')]
    public function destroy($id)
    {
        try {
            $purchase = Purchase::with(['items', 'payments'])->findOrFail($id);

            if ($purchase->cash_register_id) {
                $cash_register = CashRegister::find($purchase->cash_register_id);
                if ($cash_register && $cash_register->status !== 'open') {
                    $this->dispatch('swal', [
                        'title' => 'Caja Cerrada',
                        'text'  => 'No se puede anular la compra porque la caja con la que se procesó ya está cerrada.',
                        'icon'  => 'warning',
                    ]);
                    return;
                }
            }

            DB::transaction(function () use ($purchase) {
                foreach ($purchase->items as $item) {
                    Product::where('id', $item->product_id)
                        ->where('type', 'product')
                        ->increment('stock', $item->quantity);
                }

                if ($purchase->cash_register_id) {
                    foreach ($purchase->payments as $payment) {
                        $isEfectivo = PaymentMethod::find($payment->payment_method_id)?->is_efectivo ?? false;

                        if ($isEfectivo && $payment->amount > 0) {
                            CashRegister::where('id', $purchase->cash_register_id)
                                ->increment('current_amount', $payment->amount);
                        }
                    }

                    Expense::where('cash_register_id', $purchase->cash_register_id)
                        ->where('concept', 'Compra de mercancía / insumos')
                        ->where('description', 'like', '%' . $purchase->purchase_number . '%')
                        ->delete();
                }

                $purchase->delete();
            });

            $this->dispatch('swal', [
                'title' => 'Anulada',
                'text'  => 'Transacción revocada del sistema.',
                'icon'  => 'success',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'title' => 'Error al Anular',
                'text'  => 'No se pudo revertir la operación: ' . $e->getMessage(),
                'icon'  => 'error',
            ]);
        }
    }

    public function render()
    {
        $purchases = Purchase::with(['supplier', 'branch', 'user'])
            ->where('branch_id', Auth::user()->branch_id)
            ->where(function ($query) {
                $query->where('purchase_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('supplier', function ($q) {
                        $q->where('company_name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('user', function ($q) {
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

        return view('livewire.admin.purchases.index', compact('purchases'));
    }
}
