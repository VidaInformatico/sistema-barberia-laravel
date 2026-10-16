<?php

namespace App\Livewire\Admin\Purchases;

use App\Models\CashRegister;
use App\Models\PaymentMethod;
use App\Models\Purchase;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;

class Credit extends Component
{
    use WithPagination;

    public $search = '';
    public $fromDate;
    public $toDate;

    public $showPaymentModal = false;
    public $payments = [];
    public $payment_method_id;
    public $amount;
    public $purchase;
    public $paymentMethods;
    public $reference;
    public $note;
    public $payment_date;

    public function mount()
    {
        $this->fromDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->toDate = Carbon::now()->format('Y-m-d');
        $this->paymentMethods = PaymentMethod::all();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFromDate()
    {
        $this->resetPage();
    }
    public function updatingToDate()
    {
        $this->resetPage();
    }

    public function printTicket($purchaseId)
    {
        $this->dispatch('print-ticket', purchaseId: $purchaseId);
    }

    public function openPaymentModal($purchaseId)
    {
        $this->purchase = Purchase::with(['user', 'payments.method'])->find($purchaseId);
        $this->amount = $this->pendingAmount;
        $this->showPaymentModal = true;
        $this->payment_date = Carbon::now()->format('Y-m-d');
    }

    #[Computed()]
    public function pendingAmount()
    {
        if (!$this->purchase) return 0;
        return $this->purchase->total - $this->purchase->payments->sum('amount');
    }

    public function savePayment()
    {
        $this->validate([
            'payment_method_id' => 'required',
            'amount' => ['required', 'numeric', 'min:0.01', 'max:' . $this->pendingAmount],
            'payment_date' => 'required|date',
        ]);

        $cash_register_id = null;
        $isEfectivo = PaymentMethod::find($this->payment_method_id)?->is_efectivo ?? false;

        if ($isEfectivo) {
            $cash_register = CashRegister::where('status', 'open')
                ->where('opened_by', Auth::user()->id)
                ->whereDate('opened_at', now()->toDateString())
                ->first();

            if (!$cash_register) {
                $this->dispatch('swal', [
                    'title' => 'Caja Cerrada',
                    'text'  => 'Debe abrir la caja para poder registrar abonos en efectivo.',
                    'icon'  => 'warning',
                ]);
                return;
            }

            if ($cash_register->current_amount < $this->amount) {
                $this->dispatch('swal', [
                    'title' => 'Saldo Insuficiente',
                    'text'  => 'La caja no cuenta con saldo suficiente para efectuar este pago.',
                    'icon'  => 'warning',
                ]);
                return;
            }

            $cash_register_id = $cash_register->id;
        }

        try {
            DB::transaction(function () use ($cash_register_id, $isEfectivo) {
                $this->purchase->payments()->create([
                    'payment_method_id' => $this->payment_method_id,
                    'amount' => $this->amount,
                    'received_amount' => $this->amount,
                    'returned_amount' => 0,
                    'reference' => $this->reference,
                    'note' => $this->note,
                    'created_at' => $this->payment_date . ' ' . now()->format('H:i:s'),
                ]);

                if ($isEfectivo && $this->amount > 0 && $cash_register_id) {
                    CashRegister::find($cash_register_id)
                        ->decrement('current_amount', $this->amount);
                }
            });

            $this->reset(['payment_method_id', 'amount', 'note', 'payment_date', 'reference']);
            $this->showPaymentModal = false;

            $this->dispatch('swal', [
                'title' => '¡Pago procesado!',
                'text'  => 'Pago procesado correctamente',
                'icon'  => 'success',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'title' => 'Error al pagar',
                'text'  => 'No se pudo registrar el abono: ' . $e->getMessage(),
                'icon'  => 'error',
            ]);
        }
    }

    public function render()
    {
        $baseQuery = Purchase::with(['user', 'branch', 'supplier', 'payments'])
            ->where('branch_id', Auth::user()->branch_id)
            ->where('credit', '>', 0)
            ->when($this->fromDate, fn($q) => $q->whereDate('created_at', '>=', $this->fromDate))
            ->when($this->toDate, fn($q) => $q->whereDate('created_at', '<=', $this->toDate))
            ->whereHas('supplier', function ($q) {
                $q->where('company_name', 'like', '%' . $this->search . '%');
            });

        $purchases = (clone $baseQuery)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('livewire.admin.purchases.credit', [
            'purchases' => $purchases
        ]);
    }
}
