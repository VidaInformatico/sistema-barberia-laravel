<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Booking;

class NotificationBooking extends Component
{
    public int $count = 0;

    public function mount()
    {
        $this->getPendingCount();
    }

    public function getPendingCount()
    {
        $user = auth()->user();

        if (!$user) {
            $this->count = 0;
            return;
        }

        $query = Booking::where('status', Booking::STATUS_PENDING);

        if ($user->role === 'barber') {
            $barber = \App\Models\Barber::where('user_id', $user->id)->first();

            if ($barber) {
                $query->forBarber($barber->id);
            } else {
                $this->count = 0;
                return;
            }
        }

        $this->count = $query->count();
    }

    public function render()
    {
        return view('livewire.admin.notification-booking');
    }
}
