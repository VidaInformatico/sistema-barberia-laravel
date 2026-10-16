<?php

namespace App\Livewire\Admin\Sales;

use App\Models\{CashRegister, Payment, PaymentMethod};
use Livewire\{Component, WithPagination};
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Payments extends Component
{
    use WithPagination;

    public $search = '', $status = '', $fromDate, $toDate;
    public $sale;
    public $paymentMethods;

    public function mount($sale = null)
    {
        $this->sale = $sale;
        $this->fromDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->toDate = Carbon::now()->format('Y-m-d');
        $this->paymentMethods = PaymentMethod::all();
    }

    public function deletePayment($paymentId)
    {
        DB::transaction(function () use ($paymentId) {
            $payment = Payment::with('paymentable')->findOrFail($paymentId);

            $isEfectivo = PaymentMethod::find($payment->payment_method_id)?->is_efectivo ?? false;

            if ($isEfectivo && $payment->paymentable && isset($payment->paymentable->cash_register_id)) {
                $cashRegister = CashRegister::find($payment->paymentable->cash_register_id);

                if ($cashRegister) {
                    $cashRegister->decrement('current_amount', $payment->amount);
                }
            }

            $payment->delete();
        });

        $this->dispatch('swal', [
            'title' => 'Aviso',
            'text'  => 'Pago eliminado correctamente',
            'icon'  => 'success'
        ]);
    }

    public function render()
{
    $branchId = Auth::user()->branch_id;

    $query = Payment::with(['method', 'paymentable.client'])
        ->where('paymentable_type', 'App\Models\Sale')
        ->whereHasMorph('paymentable', ['App\Models\Sale'], function ($q) use ($branchId) {
            $q->where('branch_id', $branchId);

            if ($this->search) {
                $search = $this->search;
                $q->whereHas('client', fn($t) => $t->where('name', 'like', "%{$search}%"));
            }
        });

    $query->whereBetween('created_at', [
        $this->fromDate . ' 00:00:00',
        $this->toDate . ' 23:59:59'
    ]);

    if ($this->sale) {
        $query->where('paymentable_id', $this->sale->id);
    }

    $payments = $query->latest()->paginate($this->sale ? 10 : 12);

    return view('livewire.admin.sales.payments', compact('payments'));
}
}
