<?php

namespace App\Livewire\Admin\Products;

use App\Models\Brand;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class BrandComponent extends Component
{
    use WithPagination;

    public $brand_id = null;
    public $name;
    public $description;
    public $is_active = true;

    public $search = '';
    public $perPage = 10;
    public $isOpen = false;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $brands = Brand::where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name', 'asc')
            ->paginate($this->perPage);

        return view('livewire.admin.products.brand-component', compact('brands'));
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
            'brand_id',
            'name',
            'description',
            'is_active'
        ]);
        $this->is_active = true;
        $this->resetValidation();
    }

    public function store()
    {
        $rules = [
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active'   => ['boolean'],
        ];

        $this->validate($rules);

        $data = [
            'name'        => $this->name,
            'description' => $this->description,
            'is_active'   => $this->is_active,
        ];

        Brand::updateOrCreate(['id' => $this->brand_id], $data);

        $this->dispatch('swal', [
            'title' => $this->brand_id ? '¡Actualizado!' : '¡Creado!',
            'text'  => 'La marca ha sido guardada correctamente',
            'icon'  => 'success',
        ]);

        $this->closeModal();
    }

    public function edit($id)
    {
        $brand = Brand::findOrFail($id);

        $this->brand_id = $brand->id;
        $this->name     = $brand->name;
        $this->description = $brand->description;
        $this->is_active   = $brand->is_active;

        $this->openModal();
    }

    public function deleteConfirm($id)
    {
        $this->dispatch('confirm-delete', id: $id);
    }

    #[On('delete-confirmed')]
    public function destroy($id)
    {
        $brand = Brand::findOrFail($id);
        $brand->delete();

        $this->dispatch('swal', [
            'title' => 'Eliminado',
            'text'  => 'Marca eliminada correctamente',
            'icon'  => 'success',
        ]);
    }
}