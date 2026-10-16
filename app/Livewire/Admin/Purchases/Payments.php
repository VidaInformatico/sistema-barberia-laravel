<?php

namespace App\Livewire\Admin\Purchases;

use App\Models\{CashRegister, Payment, PaymentMethod};
use Livewire\{Component, WithPagination};
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Payments extends Component
{
    use WithPagination;

    public $search = '', $status = '', $fromDate, $toDate;
    public $purchase;
    public $paymentMethods;

    public function mount($purchase = null)
    {
        $this->purchase = $purchase;
        $this->fromDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->toDate = Carbon::now()->format('Y-m-d');
        $this->paymentMethods = PaymentMethod::all();
    }

    public function deletePayment($paymentId)
    {
        try {
            $payment = Payment::with('paymentable')->findOrFail($paymentId);

            if ($payment->paymentable && isset($payment->paymentable->cash_register_id)) {
                $cashRegister = CashRegister::find($payment->paymentable->cash_register_id);
                if ($cashRegister && $cashRegister->status !== 'open') {
                    $this->dispatch('swal', [
                        'title' => 'Caja Cerrada',
                        'text'  => 'No se puede eliminar el pago porque la caja asociada ya se encuentra cerrada.',
                        'icon'  => 'warning',
                    ]);
                    return;
                }
            }

            DB::transaction(function () use ($payment) {
                $isEfectivo = PaymentMethod::find($payment->payment_method_id)?->is_efectivo ?? false;

                if ($isEfectivo && $payment->paymentable && isset($payment->paymentable->cash_register_id)) {
                    $cashRegister = CashRegister::find($payment->paymentable->cash_register_id);

                    if ($cashRegister && $payment->amount > 0) {
                        $cashRegister->increment('current_amount', $payment->amount);
                    }
                }

                $payment->delete();
            });

            $this->dispatch('swal', [
                'title' => 'Aviso',
                'text'  => 'Pago eliminado correctamente y saldo devuelto a la caja.',
                'icon'  => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'title' => 'Error',
                'text'  => 'No se pudo eliminar el pago: ' . $e->getMessage(),
                'icon'  => 'error',
            ]);
        }
    }

    public function render()
    {
        $branchId = Auth::user()->branch_id;

        $query = Payment::with(['method', 'paymentable.supplier'])
            ->where('paymentable_type', 'App\Models\Purchase')
            ->whereHasMorph('paymentable', ['App\Models\Purchase'], function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);

                if ($this->search) {
                    $search = $this->search;
                    $q->whereHas('supplier', fn($t) => $t->where('company_name', 'like', "%{$search}%"));
                }
            });

        $query->whereBetween('created_at', [
            $this->fromDate . ' 00:00:00',
            $this->toDate . ' 23:59:59'
        ]);

        if ($this->purchase) {
            $query->where('paymentable_id', $this->purchase->id);
        }

        $payments = $query->latest()->paginate($this->purchase ? 10 : 12);

        return view('livewire.admin.purchases.payments', compact('payments'));
    }
}
