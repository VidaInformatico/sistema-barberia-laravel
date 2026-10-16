<?php

namespace App\Livewire\Barber\Sales;

use App\Models\CashRegister;
use App\Models\PaymentMethod;
use Livewire\Component;
use App\Models\Sale;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
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
    public $sale;
    public $paymentMethods;
    public $reference;
    public $note;
    public $payment_date;
    public $boxId;

    public function mount()
    {
        $this->boxId = CashRegister::where('status', 'open')
            ->where('opened_by', Auth::user()->id)
            ->whereDate('opened_at', now()->toDateString())
            ->first()?->id;
        if (!$this->boxId) {
            return redirect()->to(route('barber.cash.register'));
        }

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

    public function printTicket($saleId)
    {
        $this->dispatch('print-ticket', saleId: $saleId);
    }

    public function openPaymentModal($saleId)
    {
        $this->sale = Sale::with(['creator', 'payments.method'])->find($saleId);
        $this->amount = $this->pendingAmount;
        $this->showPaymentModal = true;
    }

    #[Computed()]
    public function pendingAmount()
    {
        if (!$this->sale) return 0;
        return $this->sale->total - $this->sale->payments->sum('amount');
    }

    public function savePayment()
    {
        $this->validate([
            'payment_method_id' => 'required',
            'amount' => ['required', 'numeric', 'min:0.01', 'max:' . $this->pendingAmount],
            'payment_date' => 'required|date',
        ]);

        $this->sale->payments()->create([
            'payment_method_id' => $this->payment_method_id,
            'amount' => $this->amount,
            'received_amount' => $this->amount,
            'returned_amount' => 0,
            'reference' => $this->reference,
            'note' => $this->note,
            'created_at' => $this->payment_date . ' ' . now()->format('H:i:s'),
        ]);

        $isEfectivo = PaymentMethod::find($this->payment_method_id)?->is_efectivo ?? false;

        if ($isEfectivo && $this->amount > 0) {
            CashRegister::find($this->boxId)
                ->increment('current_amount', $this->amount);
        }

        $this->reset(['payment_method_id', 'amount', 'note', 'payment_date']);
        $this->showPaymentModal = false;

        $this->dispatch('swal', [
            'title' => '¡Pago procesado!',
            'text'  => 'Pago procesado correctamente',
            'icon'  => 'success',
        ]);
    }

    public function render()
    {
        $baseQuery = Sale::with(['creator', 'branch', 'client', 'payments'])
            ->where('barber_id', Auth::user()->barber->id)
            ->where('credit', '>', 0)
            ->when($this->fromDate, fn($q) => $q->whereDate('created_at', '>=', $this->fromDate))
            ->when($this->toDate, fn($q) => $q->whereDate('created_at', '<=', $this->toDate))
            ->whereHas('client', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            });

        $sales = (clone $baseQuery)
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('livewire.barber.sales.credit', [
            'sales' => $sales
        ]);
    }
}
