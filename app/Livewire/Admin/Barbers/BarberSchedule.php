<?php

namespace App\Livewire\Admin\Barbers;

use App\Models\BarberSchedule as ScheduleModel;
use App\Models\Barber;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class BarberSchedule extends Component
{
    use WithPagination;

    public $schedule_id = null;
    public $barber_id;
    public $day_of_week = '';
    public $start_time;
    public $end_time;
    public $is_active = true;

    public $search = '';
    public $perPage = 10;
    public $isOpen = false;

    public $barbers = [];
    public $days_list = [
        1 => 'Lunes',
        2 => 'Martes',
        3 => 'Miércoles',
        4 => 'Jueves',
        5 => 'Viernes',
        6 => 'Sábado',
        0 => 'Domingo'
    ];

    public function mount()
    {
        $this->barbers = Barber::with('user')->get();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $branchId = auth()->user()->branch_id;

        $schedules = ScheduleModel::with('barber.user')
            ->whereHas('barber.user', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->when($this->search, function ($query) {
                $query->whereHas('barber.user', function ($subQuery) {
                    $subQuery->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('last_name', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('barber_id', 'asc')
            ->orderBy('day_of_week', 'asc')
            ->paginate($this->perPage);

        return view('livewire.admin.barbers.barber-schedule', compact('schedules'));
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
            'schedule_id',
            'barber_id',
            'day_of_week',
            'start_time',
            'end_time',
            'is_active'
        ]);
        $this->day_of_week = '';
        $this->is_active = true;
        $this->resetValidation();
    }

    public function store()
    {
        $rules = [
            'barber_id'   => ['required', 'exists:barbers,id'],
            'day_of_week' => [
                'required',
                'integer',
                'between:0,6',
                'unique:barber_schedules,day_of_week,' . $this->schedule_id . ',id,barber_id,' . $this->barber_id
            ],
            'start_time'  => ['required', 'date_format:H:i'],
            'end_time'    => ['required', 'date_format:H:i', 'after:start_time'],
            'is_active'   => ['boolean'],
        ];

        $messages = [
            'day_of_week.unique' => 'Este barbero ya tiene configurado un horario para este día.',
        ];

        $this->validate($rules, $messages);

        $data = [
            'barber_id'   => $this->barber_id,
            'day_of_week' => $this->day_of_week,
            'start_time'  => $this->start_time,
            'end_time'    => $this->end_time,
            'is_active'   => $this->is_active,
        ];

        ScheduleModel::updateOrCreate(['id' => $this->schedule_id], $data);

        $this->dispatch('swal', [
            'title' => $this->schedule_id ? '¡Actualizado!' : '¡Creado!',
            'text'  => 'El horario laboral ha sido configurado correctamente',
            'icon'  => 'success',
        ]);

        $this->closeModal();
    }

    public function edit($id)
    {
        $schedule = ScheduleModel::findOrFail($id);

        $this->schedule_id = $schedule->id;
        $this->barber_id   = $schedule->barber_id;
        $this->day_of_week = $schedule->day_of_week;
        $this->start_time  = $schedule->start_time ? substr($schedule->start_time, 0, 5) : null;
        $this->end_time    = $schedule->end_time ? substr($schedule->end_time, 0, 5) : null;
        $this->is_active   = $schedule->is_active;

        $this->openModal();
    }

    public function deleteConfirm($id)
    {
        $this->dispatch('confirm-delete', id: $id);
    }

    #[On('delete-confirmed')]
    public function destroy($id)
    {
        $schedule = ScheduleModel::findOrFail($id);
        $schedule->delete();

        $this->dispatch('swal', [
            'title' => 'Eliminado',
            'text'  => 'Horario eliminado correctamente',
            'icon'  => 'success',
        ]);
    }
}
