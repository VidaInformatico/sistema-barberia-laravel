<?php

namespace App\Livewire\Front;

use App\Models\Booking;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;

class Confirmation extends Component
{
    public $booking;
    public $notes = null;
    public $payment_method = 'local';

    public function mount($booking)
    {
        $this->booking = $booking;
    }

    public function processBooking()
    {
        $this->validate([
            'payment_method' => 'required|in:local,online',
            'notes' => 'nullable|string|max:500',
        ]);

        $this->booking->status = Booking::STATUS_CONFIRMED;
        $this->booking->confirmed_at = now();
        $this->booking->payment_method = $this->payment_method;
        $this->booking->notes = $this->notes;
        $this->booking->save();

        LivewireAlert::title('Reserva confirmada.')
            ->success()
            ->asToast()
            ->show();

        return redirect()->route('client.dashboard');
    }

    public function render()
    {
        return view('livewire.front.confirmation');
    }
}
