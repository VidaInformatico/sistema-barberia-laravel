<?php

namespace App\Livewire\Admin\Services;

use App\Models\ProductCategory;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class Category extends Component
{
    use WithPagination;

    public $category_id = null;
    public $name;
    public $description;
    public $icon;
    public $sort_order = 0;
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
        $categories = ProductCategory::where('type', 'service')
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->orderBy('sort_order', 'asc')
            ->paginate($this->perPage);

        return view('livewire.admin.services.category', compact('categories'));
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
            'category_id',
            'name',
            'description',
            'icon',
            'sort_order',
            'is_active'
        ]);
        $this->is_active = true;
        $this->sort_order = 0;
        $this->resetValidation();
    }

    public function store()
    {
        $rules = [
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'icon'        => ['nullable', 'string', 'max:255'],
            'sort_order'  => ['required', 'integer'],
            'is_active'   => ['boolean'],
        ];

        $this->validate($rules);

        $data = [
            'name'        => $this->name,
            'description' => $this->description,
            'icon'        => $this->icon,
            'sort_order'  => $this->sort_order,
            'is_active'   => $this->is_active,
        ];

        ProductCategory::updateOrCreate(['id' => $this->category_id], $data);

        $this->dispatch('swal', [
            'title' => $this->category_id ? '¡Actualizado!' : '¡Creado!',
            'text'  => 'La categoría de servicio ha sido guardada correctamente',
            'icon'  => 'success',
        ]);

        $this->closeModal();
    }

    public function edit($id)
    {
        $category = ProductCategory::findOrFail($id);

        $this->category_id = $category->id;
        $this->name        = $category->name;
        $this->description = $category->description;
        $this->icon        = $category->icon;
        $this->sort_order  = $category->sort_order;
        $this->is_active   = $category->is_active;

        $this->openModal();
    }

    public function deleteConfirm($id)
    {
        $this->dispatch('confirm-delete', id: $id);
    }

    #[On('delete-confirmed')]
    public function destroy($id)
    {
        $category = ProductCategory::findOrFail($id);
        $category->delete();

        $this->dispatch('swal', [
            'title' => 'Eliminado',
            'text'  => 'Categoría de servicio eliminada correctamente',
            'icon'  => 'success',
        ]);
    }
}
