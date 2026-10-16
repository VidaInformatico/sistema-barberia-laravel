<?php

namespace App\Livewire\Admin\Services;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Illuminate\Support\Str;

class Index extends Component
{
    use WithPagination;

    public $service_id = null;
    public $category_id;
    public $name;
    public $description;
    public $duration_minutes;
    public $price;
    public $image;
    public $sort_order = 0;
    public $is_active = true;

    public $search = '';
    public $perPage = 10;
    public $isOpen = false;

    public $categories = [];

    public function mount()
    {
        $this->categories = ProductCategory::where('type', 'service')->orderBy('name', 'asc')->get();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $services = Product::where('type', 'service')
            ->where('branch_id', Auth::user()->branch_id)
            ->with('category')
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->orderBy('sort_order', 'asc')
            ->paginate($this->perPage);

        return view('livewire.admin.services.index', compact('services'));
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
            'service_id',
            'category_id',
            'name',
            'description',
            'duration_minutes',
            'price',
            'image',
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
            'category_id' => ['required', 'exists:categories,id'],
            'name'                => ['required', 'string', 'max:255'],
            'description'         => ['nullable', 'string'],
            'duration_minutes'    => ['required', 'integer', 'min:1'],
            'price'               => ['required', 'numeric', 'min:0'],
            'image'               => ['nullable', 'string'],
            'sort_order'          => ['required', 'integer'],
            'is_active'           => ['boolean'],
        ];

        $this->validate($rules);

        $data = [
            'category_id' => $this->category_id,
            'name'                => $this->name,
            'type'                => 'service',
            'slug'                => Str::slug($this->name),
            'description'         => $this->description,
            'duration_minutes'    => $this->duration_minutes,
            'price'               => $this->price,
            'image'               => $this->image,
            'sort_order'          => $this->sort_order,
            'is_active'           => $this->is_active,
            'branch_id'         => Auth::user()->branch_id
        ];

        Product::updateOrCreate(['id' => $this->service_id], $data);

        $this->dispatch('swal', [
            'title' => $this->service_id ? '¡Actualizado!' : '¡Creado!',
            'text'  => 'El servicio ha sido guardado correctamente',
            'icon'  => 'success',
        ]);

        $this->closeModal();
    }

    public function edit($id)
    {
        $service = Product::findOrFail($id);

        $this->service_id          = $service->id;
        $this->category_id = $service->category_id;
        $this->name                = $service->name;
        $this->description         = $service->description;
        $this->duration_minutes    = $service->duration_minutes;
        $this->price               = $service->price;
        $this->image               = $service->image;
        $this->sort_order          = $service->sort_order;
        $this->is_active           = $service->is_active;

        $this->openModal();
    }

    public function deleteConfirm($id)
    {
        $this->dispatch('confirm-delete', id: $id);
    }

    #[On('delete-confirmed')]
    public function destroy($id)
    {
        $service = Product::findOrFail($id);
        $service->delete();

        $this->dispatch('swal', [
            'title' => 'Eliminado',
            'text'  => 'Servicio eliminado correctamente',
            'icon'  => 'success',
        ]);
    }
}
