<?php

namespace App\Livewire\Admin\Barbers;

use App\Models\Barber;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class Index extends Component
{
    use WithPagination;

    public $barber_id = null;
    public $user_id;
    public $bio;
    public $specialty;
    public $experience_years;
    public $is_available = true;
    public $instagram;

    public $search = '';
    public $perPage = 10;
    public $isOpen = false;

    public $users = [];

    public function mount()
    {
        $this->users = User::where('role', 'barber')->orderBy('name', 'asc')->get();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $branchId = auth()->user()->branch_id;

        $barbers = Barber::with('user')
            ->whereHas('user', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->where(function ($query) {
                $query->whereHas('user', function ($subQuery) {
                    $subQuery->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('last_name', 'like', '%' . $this->search . '%');
                })
                    ->orWhere('specialty', 'like', '%' . $this->search . '%');
            })
            ->paginate($this->perPage);

        return view('livewire.admin.barbers.index', compact('barbers'));
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
            'barber_id',
            'user_id',
            'bio',
            'specialty',
            'experience_years',
            'is_available',
            'instagram'
        ]);
        $this->is_available = true;
        $this->resetValidation();
    }

    public function store()
    {
        $rules = [
            'user_id'          => ['required', 'exists:users,id', 'unique:barbers,user_id,' . $this->barber_id],
            'bio'              => ['nullable', 'string'],
            'specialty'        => ['nullable', 'string', 'max:255'],
            'experience_years' => ['required', 'integer', 'min:0'],
            'is_available'     => ['boolean'],
            'instagram'        => ['nullable', 'string', 'max:255'],
        ];

        $this->validate($rules);

        $data = [
            'user_id'          => $this->user_id,
            'bio'              => $this->bio,
            'specialty'        => $this->specialty,
            'experience_years' => $this->experience_years,
            'is_available'     => $this->is_available,
            'instagram'        => $this->instagram,
        ];

        Barber::updateOrCreate(['id' => $this->barber_id], $data);

        $this->dispatch('swal', [
            'title' => $this->barber_id ? '¡Actualizado!' : '¡Creado!',
            'text'  => 'El perfil del barbero ha sido guardado correctamente',
            'icon'  => 'success',
        ]);

        $this->closeModal();
    }

    public function edit($id)
    {
        $barber = Barber::findOrFail($id);

        $this->barber_id        = $barber->id;
        $this->user_id          = $barber->user_id;
        $this->bio              = $barber->bio;
        $this->specialty        = $barber->specialty;
        $this->experience_years = $barber->experience_years;
        $this->is_available     = $barber->is_available;
        $this->instagram        = $barber->instagram;

        $this->openModal();
    }

    public function deleteConfirm($id)
    {
        $this->dispatch('confirm-delete', id: $id);
    }

    #[On('delete-confirmed')]
    public function destroy($id)
    {
        $barber = Barber::findOrFail($id);
        $barber->delete();

        $this->dispatch('swal', [
            'title' => 'Eliminado',
            'text'  => 'Perfil de barbero eliminado correctamente',
            'icon'  => 'success',
        ]);
    }
}
