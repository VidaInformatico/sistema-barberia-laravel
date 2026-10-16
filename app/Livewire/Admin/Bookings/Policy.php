<?php

namespace App\Livewire\Admin\Bookings;

use App\Models\BookingPolicy;
use Livewire\Component;
use Livewire\Attributes\On;

class Policy extends Component
{
    public $policy_id = null;
    public $max_days_in_advance;
    public $min_hours_before_booking;
    public $cancel_before_hours;
    public $allow_client_cancellation = true;
    public $grace_period_minutes;
    public $mark_no_show_after_grace = false;
    public $is_active = true;

    public $isOpen = false;

    public function render()
    {
        $policy = BookingPolicy::orderBy('id', 'desc')->first();

        return view('livewire.admin.bookings.policy', compact('policy'));
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
            'policy_id',
            'max_days_in_advance',
            'min_hours_before_booking',
            'cancel_before_hours',
            'allow_client_cancellation',
            'grace_period_minutes',
            'mark_no_show_after_grace',
            'is_active'
        ]);
        
        $this->allow_client_cancellation = true;
        $this->mark_no_show_after_grace = false;
        $this->is_active = true;
        
        $this->resetValidation();
    }

    public function store()
    {
        $rules = [
            'max_days_in_advance'       => ['required', 'integer', 'min:0'],
            'min_hours_before_booking'  => ['required', 'integer', 'min:0'],
            'cancel_before_hours'       => ['required', 'integer', 'min:0'],
            'allow_client_cancellation' => ['boolean'],
            'grace_period_minutes'      => ['required', 'integer', 'min:0'],
            'mark_no_show_after_grace'  => ['boolean'],
            'is_active'                 => ['boolean'],
        ];

        $this->validate($rules);

        $data = [
            'max_days_in_advance'       => $this->max_days_in_advance,
            'min_hours_before_booking'  => $this->min_hours_before_booking,
            'cancel_before_hours'       => $this->cancel_before_hours,
            'allow_client_cancellation' => $this->allow_client_cancellation,
            'grace_period_minutes'      => $this->grace_period_minutes,
            'mark_no_show_after_grace'  => $this->mark_no_show_after_grace,
            'is_active'                 => $this->is_active,
        ];

        BookingPolicy::updateOrCreate(['id' => $this->policy_id], $data);

        $this->dispatch('swal', [
            'title' => $this->policy_id ? '¡Actualizado!' : '¡Creado!',
            'text'  => 'La política de reserva ha sido guardada correctamente',
            'icon'  => 'success',
        ]);

        $this->closeModal();
    }

    public function edit($id)
    {
        $policy = BookingPolicy::findOrFail($id);

        $this->policy_id                 = $policy->id;
        $this->max_days_in_advance       = $policy->max_days_in_advance;
        $this->min_hours_before_booking  = $policy->min_hours_before_booking;
        $this->cancel_before_hours       = $policy->cancel_before_hours;
        $this->allow_client_cancellation = (bool)$policy->allow_client_cancellation;
        $this->grace_period_minutes      = $policy->grace_period_minutes;
        $this->mark_no_show_after_grace  = (bool)$policy->mark_no_show_after_grace;
        $this->is_active                 = (bool)$policy->is_active;

        $this->openModal();
    }

    public function deleteConfirm($id)
    {
        $this->dispatch('confirm-delete', id: $id);
    }

    #[On('delete-confirmed')]
    public function destroy($id)
    {
        $policy = BookingPolicy::findOrFail($id);
        $policy->delete();

        $this->dispatch('swal', [
            'title' => 'Eliminado',
            'text'  => 'Política de reserva eliminada correctamente',
            'icon'  => 'success',
        ]);
    }
}