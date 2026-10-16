<?php

namespace App\Livewire\Admin\Barbers;

use App\Models\Barber;
use App\Models\BarberBlockedTime;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class BlockedTime extends Component
{
    use WithPagination;

    public $blocked_time_id = null;
    public $barber_id;
    public $date;
    public $start_time;
    public $end_time;
    public $reason;

    public $is_full_day = false;

    public $search = '';
    public $perPage = 10;
    public $isOpen = false;

    public $barbers = [];

    public function mount()
    {
        $this->barbers = Barber::with('user')->get();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedIsFullDay($value)
    {
        if ($value) {
            $this->start_time = null;
            $this->end_time = null;
        }
    }

    public function render()
    {
        $branchId = auth()->user()->branch_id;

        $blockedTimes = BarberBlockedTime::with('barber.user')
            ->whereHas('barber.user', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->where(function ($query) {
                $query->whereHas('barber.user', function ($subQuery) {
                    $subQuery->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('last_name', 'like', '%' . $this->search . '%');
                })
                    ->orWhere('reason', 'like', '%' . $this->search . '%');
            })
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'asc')
            ->paginate($this->perPage);

        return view('livewire.admin.barbers.blocked-time', compact('blockedTimes'));
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
            'blocked_time_id',
            'barber_id',
            'date',
            'start_time',
            'end_time',
            'reason',
            'is_full_day'
        ]);
        $this->resetValidation();
    }

    public function store()
    {
        $rules = [
            'barber_id'   => ['required', 'exists:barbers,id'],
            'date'        => ['required', 'date', 'after_or_equal:today'],
            'reason'      => ['nullable', 'string', 'max:255'],
            'is_full_day' => ['boolean'],
            'start_time'  => ['required_if:is_full_day,false', 'nullable', 'date_format:H:i'],
            'end_time'    => ['required_if:is_full_day,false', 'nullable', 'date_format:H:i', 'after:start_time'],
        ];

        $this->validate($rules);

        $data = [
            'barber_id'  => $this->barber_id,
            'date'       => $this->date,
            'start_time' => $this->is_full_day ? null : $this->start_time,
            'end_time'   => $this->is_full_day ? null : $this->end_time,
            'reason'     => $this->reason,
        ];

        BarberBlockedTime::updateOrCreate(['id' => $this->blocked_time_id], $data);

        $this->dispatch('swal', [
            'title' => $this->blocked_time_id ? '¡Actualizado!' : '¡Creado!',
            'text'  => 'El tiempo bloqueado ha sido guardado correctamente',
            'icon'  => 'success',
        ]);

        $this->closeModal();
    }

    public function edit($id)
    {
        $blocked = BarberBlockedTime::findOrFail($id);

        $this->blocked_time_id = $blocked->id;
        $this->barber_id       = $blocked->barber_id;
        $this->date            = $blocked->date ? $blocked->date->format('Y-m-d') : null;
        $this->start_time      = $blocked->start_time ? substr($blocked->start_time, 0, 5) : null;
        $this->end_time        = $blocked->end_time ? substr($blocked->end_time, 0, 5) : null;
        $this->reason          = $blocked->reason;
        $this->is_full_day     = $blocked->isFullDay();

        $this->openModal();
    }

    public function deleteConfirm($id)
    {
        $this->dispatch('confirm-delete', id: $id);
    }

    #[On('delete-confirmed')]
    public function destroy($id)
    {
        $blocked = BarberBlockedTime::findOrFail($id);
        $blocked->delete();

        $this->dispatch('swal', [
            'title' => 'Eliminado',
            'text'  => 'Bloqueo eliminado correctamente',
            'icon'  => 'success',
        ]);
    }
}
