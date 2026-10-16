<?php

namespace App\Livewire\Admin;

use App\Models\Branch;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Illuminate\Validation\Rule;

class BranchComponent extends Component
{
    use WithPagination;

    public $name = '';
    public $phone = '';
    public $email = '';
    public $address = '';
    public $active = true;
    public $direct_printing = false;
    public $printer_name = '';
    public $print_size = '';

    public $branch_id = null;
    public $search = '';
    public $isOpen = false;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $branches = Branch::where(function ($q) {
            $q->where('name', 'like', '%' . $this->search . '%')
              ->orWhere('email', 'like', '%' . $this->search . '%')
              ->orWhere('phone', 'like', '%' . $this->search . '%');
        })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.branch-component', compact('branches'));
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
            'name',
            'phone',
            'email',
            'address',
            'active',
            'direct_printing',
            'printer_name',
            'print_size',
            'branch_id'
        ]);
        $this->resetValidation();
    }

    public function store()
    {
        $this->validate([
            'name' => [
                'required',
                'min:3',
                Rule::unique('branches', 'name')->ignore($this->branch_id),
            ],
            'phone' => ['required', 'string'],
            'email' => ['required', 'email'],
            'address' => ['required', 'string'],
            'active' => ['boolean'],
            'direct_printing' => ['boolean'],
            'printer_name' => ['nullable', 'string'],
            'print_size' => ['required', 'numeric'],
        ]);

        Branch::updateOrCreate(
            ['id' => $this->branch_id],
            [
                'name' => $this->name,
                'phone' => $this->phone,
                'email' => $this->email,
                'address' => $this->address,
                'active' => $this->active,
                'direct_printing' => $this->direct_printing,
                'printer_name' => $this->printer_name,
                'print_size' => $this->print_size,
            ]
        );

        $this->dispatch('swal', [
            'title' => $this->branch_id ? '¡Actualizado!' : '¡Creado!',
            'text'  => 'Sucursal procesada correctamente',
            'icon'  => 'success',
        ]);

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $branch = Branch::findOrFail($id);

        $this->branch_id = $branch->id;
        $this->name = $branch->name;
        $this->phone = $branch->phone;
        $this->email = $branch->email;
        $this->address = $branch->address;
        $this->active = $branch->active;
        $this->direct_printing = $branch->direct_printing;
        $this->printer_name = $branch->printer_name;
        $this->print_size = $branch->print_size;

        $this->openModal();
    }

    public function deleteConfirm($id)
    {
        $this->dispatch('confirm-delete', id: $id);
    }

    #[On('delete-confirmed')]
    public function destroy($id)
    {
        Branch::findOrFail($id)->delete();

        $this->dispatch('swal', [
            'title' => 'Eliminado',
            'text'  => 'Sucursal eliminada correctamente',
            'icon'  => 'success',
        ]);
    }
}