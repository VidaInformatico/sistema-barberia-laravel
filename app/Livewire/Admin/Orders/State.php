<?php

namespace App\Livewire\Admin\Orders;

use App\Models\OrderState;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Illuminate\Support\Str;

class State extends Component
{
    use WithPagination;

    public $state_id = null;
    public $name;
    public $slug;
    public $color = '#6c757d';
    public $is_initial = false;
    public $is_canceled = false;
    public $is_final = false;

    public $search = '';
    public $perPage = 10;
    public $isOpen = false;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedName($value)
    {
        if (!$this->state_id) {
            $this->slug = Str::slug($value);
        }
    }

    public function render()
    {
        $states = OrderState::where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('slug', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name', 'asc')
            ->paginate($this->perPage);

        return view('livewire.admin.orders.state', compact('states'));
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
            'state_id',
            'name',
            'slug',
            'color',
            'is_initial',
            'is_canceled',
            'is_final'
        ]);
        $this->color = '#6c757d';
        $this->is_initial = false;
        $this->is_canceled = false;
        $this->is_final = false;
        $this->resetValidation();
    }

    public function store()
    {
        $rules = [
            'name'        => ['required', 'string', 'max:255'],
            'slug'        => ['required', 'string', 'max:255', 'unique:order_states,slug,' . $this->state_id],
            'color'       => ['required', 'string', 'max:50'],
            'is_initial'  => ['boolean'],
            'is_canceled' => ['boolean'],
            'is_final'    => ['boolean'],
        ];

        $this->validate($rules);

        $data = [
            'name'        => $this->name,
            'slug'        => Str::slug($this->slug),
            'color'       => $this->color,
            'is_initial'  => $this->is_initial,
            'is_canceled' => $this->is_canceled,
            'is_final'    => $this->is_final,
        ];

        OrderState::updateOrCreate(['id' => $this->state_id], $data);

        $this->dispatch('swal', [
            'title' => $this->state_id ? '¡Actualizado!' : '¡Creado!',
            'text'  => 'El estado de orden ha sido guardado correctamente',
            'icon'  => 'success',
        ]);

        $this->closeModal();
    }

    public function edit($id)
    {
        $state = OrderState::findOrFail($id);

        $this->state_id    = $state->id;
        $this->name        = $state->name;
        $this->slug        = $state->slug;
        $this->color       = $state->color;
        $this->is_initial  = $state->is_initial;
        $this->is_canceled = $state->is_canceled;
        $this->is_final    = $state->is_final;

        $this->openModal();
    }

    public function deleteConfirm($id)
    {
        $this->dispatch('confirm-delete', id: $id);
    }

    #[On('delete-confirmed')]
    public function destroy($id)
    {
        $state = OrderState::findOrFail($id);
        $state->delete();

        $this->dispatch('swal', [
            'title' => 'Eliminado',
            'text'  => 'Estado de orden eliminado correctamente',
            'icon'  => 'success',
        ]);
    }
}