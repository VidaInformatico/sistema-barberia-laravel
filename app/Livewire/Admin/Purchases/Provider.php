<?php

namespace App\Livewire\Admin\Purchases;

use App\Models\Supplier;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Illuminate\Validation\Rule;

class Provider extends Component
{
    use WithPagination;

    public $supplier_id = null;
    public $company_name = '';
    public $contact_name = '';
    public $email = '';
    public $phone = '';
    public $tax_id = '';
    public $address = '';
    public $city = '';
    public $website = '';
    public $is_active = true;
    public $notes = '';
    public $search = '';
    public $isOpen = false;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $suppliers = Supplier::where('company_name', 'like', '%' . $this->search . '%')
            ->orWhere('tax_id', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.admin.purchases.provider', compact('suppliers'));
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
            'supplier_id', 'company_name', 'contact_name', 'email', 'phone', 
            'tax_id', 'address', 'city', 'website', 'is_active', 'notes'
        ]);
        $this->is_active = true;
        $this->resetValidation();
    }

    public function store()
    {
        $rules = [
            'company_name' => 'required|min:3',
            'contact_name' => 'nullable|string',
            'email' => ['required', 'email', Rule::unique('suppliers', 'email')->ignore($this->supplier_id)],
            'phone' => 'required|string',
            'tax_id' => ['required', Rule::unique('suppliers', 'tax_id')->ignore($this->supplier_id)],
            'address' => 'required|string',
            'city' => 'nullable|string',
            'website' => 'nullable|url',
            'is_active' => 'required|boolean',
            'notes' => 'nullable|string',
        ];

        $this->validate($rules);

        Supplier::updateOrCreate(['id' => $this->supplier_id], [
            'company_name' => $this->company_name,
            'contact_name' => $this->contact_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'tax_id' => $this->tax_id,
            'address' => $this->address,
            'city' => $this->city,
            'website' => $this->website,
            'is_active' => $this->is_active,
            'notes' => $this->notes,
        ]);

        $this->dispatch('swal', [
            'title' => $this->supplier_id ? '¡Actualizado!' : '¡Creado!',
            'text'  => 'El proveedor se guardó correctamente',
            'icon'  => 'success',
        ]);

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        $this->supplier_id = $supplier->id;
        $this->company_name = $supplier->company_name;
        $this->contact_name = $supplier->contact_name;
        $this->email = $supplier->email;
        $this->phone = $supplier->phone;
        $this->tax_id = $supplier->tax_id;
        $this->address = $supplier->address;
        $this->city = $supplier->city;
        $this->website = $supplier->website;
        $this->is_active = $supplier->is_active;
        $this->notes = $supplier->notes;

        $this->openModal();
    }

    public function deleteConfirm($id)
    {
        $this->dispatch('confirm-delete', id: $id);
    }

    #[On('delete-confirmed')]
    public function destroy($id)
    {
        Supplier::findOrFail($id)->delete();

        $this->dispatch('swal', [
            'title' => 'Eliminado',
            'text'  => 'Proveedor eliminado correctamente',
            'icon'  => 'success',
        ]);
    }
}