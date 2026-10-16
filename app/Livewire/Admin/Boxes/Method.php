<?php

namespace App\Livewire\Admin\Boxes;

use App\Models\PaymentMethod;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class Method extends Component
{
    use WithPagination;

    public $payment_method_id = null;
    public $name;
    public $is_efectivo = 0;

    public $search = '';
    public $perPage = 10;
    public $isOpen = false;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $paymentMethods = PaymentMethod::where('name', 'like', '%' . $this->search . '%')
            ->orderBy('name', 'asc')
            ->paginate($this->perPage);

        return view('livewire.admin.boxes.method', compact('paymentMethods'));
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
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->reset([
            'payment_method_id',
            'name',
            'is_efectivo'
        ]);
        $this->is_efectivo = 0;
        $this->resetValidation();
    }

    public function store()
    {
        $rules = [
            'name'        => ['required', 'string', 'max:255'],
            'is_efectivo' => ['boolean'],
        ];

        $this->validate($rules);

        $data = [
            'name'        => $this->name,
            'is_efectivo' => $this->is_efectivo ? 1 : 0,
        ];

        PaymentMethod::updateOrCreate(['id' => $this->payment_method_id], $data);

        $this->dispatch('swal', [
            'title' => $this->payment_method_id ? '¡Actualizado!' : '¡Creado!',
            'text'  => 'El método de pago ha sido guardado correctamente',
            'icon'  => 'success',
        ]);

        $this->closeModal();
    }

    public function edit($id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);

        $this->payment_method_id = $paymentMethod->id;
        $this->name              = $paymentMethod->name;
        $this->is_efectivo       = $paymentMethod->is_efectivo;

        $this->openModal();
    }

    public function deleteConfirm($id)
    {
        $this->dispatch('confirm-delete', id: $id);
    }

    #[On('delete-confirmed')]
    public function destroy($id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);
        $paymentMethod->delete();

        $this->dispatch('swal', [
            'title' => 'Eliminado',
            'text'  => 'Método de pago eliminado correctamente',
            'icon'  => 'success',
        ]);
    }
}