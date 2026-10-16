<?php

namespace App\Livewire\Barber\Boxes;

use App\Models\Expense;
use App\Models\CashRegister;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class Bill extends Component
{
    use WithPagination;

    public $expense_id = null;
    public $cash_register_id = '';
    public $payment_method_id = '';
    public $concept = '';
    public $description = '';
    public $amount = '';
    public $itbis = '';
    public $other_taxes = 0;
    public $total_amount = '';
    public $expense_date = '';

    public $search = '';
    public $start_date = '';
    public $end_date = '';

    public $isOpen = false;

    public $paymentMethods = [];

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingStartDate()
    {
        $this->resetPage();
    }
    public function updatingEndDate()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->cash_register_id = CashRegister::where('status', 'open')
            ->where('opened_by', Auth::user()->id)
            ->whereDate('opened_at', now()->toDateString())
            ->first()?->id;

        if (!$this->cash_register_id) {
            session()->flash('message', 'Debes abrir una caja primero para continuar.');
            return redirect()->to(route('admin.boxes.index'));
        }

        $this->paymentMethods = PaymentMethod::all();
        $this->expense_date = now()->format('Y-m-d\TH:i');

        $this->start_date = now()->startOfMonth()->format('Y-m-d');
        $this->end_date = now()->endOfMonth()->format('Y-m-d');
    }

    public function render()
    {
        $bills = Expense::with(['cashRegister', 'branch', 'paymentMethod', 'user'])
            ->where('user_id', Auth::user()->id)
            ->where(function ($query) {
                $query->where('concept', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->when($this->start_date, function ($query) {
                $query->whereDate('expense_date', '>=', $this->start_date);
            })
            ->when($this->end_date, function ($query) {
                $query->whereDate('expense_date', '<=', $this->end_date);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.barber.boxes.Bill', compact('bills'));
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    private function resetInputFields()
    {
        $this->reset([
            'expense_id',
            'payment_method_id',
            'concept',
            'description',
            'amount',
            'itbis',
            'other_taxes',
            'total_amount',
        ]);

        $this->expense_date = now()->format('Y-m-d\TH:i');
        $this->resetValidation();
    }

    public function edit($id)
    {
        $expense = Expense::findOrFail($id);

        $this->expense_id        = $expense->id;
        $this->cash_register_id  = $expense->cash_register_id;
        $this->payment_method_id = $expense->payment_method_id;
        $this->concept           = $expense->concept;
        $this->description       = $expense->description;
        $this->amount           =  $expense->amount;
        $this->itbis             = $expense->itbis;
        $this->other_taxes       = $expense->other_taxes;
        $this->total_amount      = $expense->total_amount;

        $this->expense_date      = $expense->expense_date->format('Y-m-d');

        $this->openModal();
    }

    public function store()
    {
        $rules = [
            'payment_method_id' => 'required|exists:payment_methods,id',
            'concept'           => 'required|string|min:3|max:255',
            'total_amount'      => 'required|numeric|min:0.01',
            'expense_date'      => 'required|date'
        ];

        $this->validate($rules);

        $isEfectivo = PaymentMethod::find($this->payment_method_id)?->is_efectivo ?? false;
        $amountDifference = 0;

        if ($isEfectivo) {
            $register = CashRegister::find($this->cash_register_id);
            $availableAmount = $register ? $register->current_amount : 0;

            if ($this->expense_id) {
                $oldExpense = Expense::find($this->expense_id);
                $amountDifference = $this->total_amount - $oldExpense->total_amount;
            } else {
                $amountDifference = $this->total_amount;
            }

            if ($amountDifference > $availableAmount) {
                $this->dispatch('swal', [
                    'title' => 'Saldo Insuficiente',
                    'text'  => 'El monto supera el dinero disponible en caja.',
                    'icon'  => 'error',
                ]);
                return;
            }
        }

        $data = [
            'cash_register_id'  => $this->cash_register_id,
            'payment_method_id' => $this->payment_method_id,
            'user_id'           => Auth::id(),
            'branch_id'         => Auth::user()->branch_id,
            'concept'           => $this->concept,
            'description'       => $this->description,
            'amount'    => $this->amount ?? 0,
            'itbis'             => empty($this->itbis) ? 0 : $this->itbis,
            'other_taxes'       => empty($this->other_taxes) ? 0 : $this->other_taxes,
            'total_amount'      => $this->total_amount,
            'expense_date'      => $this->expense_date,
        ];

        Expense::updateOrCreate(['id' => $this->expense_id], $data);

        if ($amountDifference != 0) {
            $this->updateCashRegisterAmount($this->cash_register_id, $amountDifference);
        }

        $this->dispatch('swal', [
            'title' => $this->expense_id ? '¡Actualizado!' : '¡Creado!',
            'text'  => 'El gasto se registró correctamente',
            'icon'  => 'success',
        ]);

        $this->closeModal();
        $this->resetInputFields();
    }

    public function deleteConfirm($id)
    {
        $this->dispatch('confirm-delete', id: $id);
    }

    #[On('delete-confirmed')]
    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);

        $isEfectivo = $expense->paymentMethod?->is_efectivo ?? false;
        if ($isEfectivo) {
            $this->updateCashRegisterAmount($expense->cash_register_id, -$expense->total_amount);
        }

        $expense->delete();

        $this->dispatch('swal', [
            'title' => 'Eliminado',
            'text'  => 'El gasto ha sido eliminado',
            'icon'  => 'success',
        ]);
    }

    private function updateCashRegisterAmount($cashRegisterId, $amountDifference)
    {
        $register = CashRegister::find($cashRegisterId);
        if ($register) {
            $register->current_amount -= $amountDifference;
            $register->save();
        }
    }
}
