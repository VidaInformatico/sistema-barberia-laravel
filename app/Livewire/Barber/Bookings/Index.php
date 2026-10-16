<?php

namespace App\Livewire\Barber\Bookings;

use App\Models\Booking;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Index extends Component
{
    use WithPagination;

    #[Url(keep: true)]
    public $search = '';

    public $selectedBarber = '';

    #[Url(keep: true)]
    public $selectedStatus = '';

    #[Url(keep: true)]
    public $selectedDate = '';

    public $isDetailOpen = false;
    public $selectedBookingData = null;

    public ?Booking $selectedBooking = null;
    public string $newStatus = '';
    public string $adminComment = '';
    public string $cancellationReason = '';

    public function mount()
    {
        $this->selectedDate = today()->format('Y-m-d');
        $this->selectedBarber = Auth::user()->barber->id;
    }

    public function showBooking($id)
    {
        $this->selectedBookingData = \App\Models\Booking::with(['client', 'barber'])->find($id);

        if ($this->selectedBookingData) {
            $this->isDetailOpen = true;
        }
    }

    public function closeDetailModal()
    {
        $this->isDetailOpen = false;
        $this->selectedBookingData = null;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openStatusModal(Booking $booking, string $status)
    {
        $this->selectedBooking = $booking;
        $this->newStatus = $status;
        $this->adminComment = '';
        $this->cancellationReason = '';

        $this->dispatch('open-modal', name: 'status-confirmation-modal');
    }

    public function processStatusChange()
    {
        if (!$this->selectedBooking) return;

        $this->validate([
            'adminComment' => 'nullable|string|max:500',
            'cancellationReason' => 'required_if:newStatus,' . Booking::STATUS_CANCELLED . '|nullable|string|max:255',
        ]);

        DB::transaction(function () {
            if ($this->newStatus === Booking::STATUS_CANCELLED) {
                $this->selectedBooking->cancellation_reason = $this->cancellationReason;
            }

            if ($this->newStatus === Booking::STATUS_IN_PROGRESS) {
                $this->selectedBooking->arrived_at = now();
            }

            $this->selectedBooking->changeStatus(
                $this->newStatus,
                Auth::user(),
                $this->adminComment ?: 'Estado actualizado desde el panel de control.'
            );
        });

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => "Reserva {$this->selectedBooking->booking_number} actualizada a " . $this->selectedBooking->status_label
        ]);

        $this->dispatch('close-modal', name: 'status-confirmation-modal');
        $this->reset(['selectedBooking', 'newStatus', 'adminComment', 'cancellationReason']);
    }

    public function render()
    {
        $bookings = Booking::query()
            ->with(['client', 'barber', 'services.service'])
            ->where('barber_id', Auth::user()->barber->id)
            ->when($this->selectedBarber, fn($q) => $q->forBarber($this->selectedBarber))
            ->when($this->selectedDate, fn($q) => $q->forDate($this->selectedDate))
            ->when($this->selectedStatus, fn($q) => $q->where('status', $this->selectedStatus))
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('booking_number', 'like', "%{$this->search}%")
                        ->orWhereHas('client', fn($c) => $c->where('name', 'like', "%{$this->search}%"))
                        ->orWhereHas('client', fn($c) => $c->where('phone', 'like', "%{$this->search}%"));
                });
            })
            ->orderByRaw("CASE WHEN status IN ('" . Booking::STATUS_CANCELLED . "', '" . Booking::STATUS_NO_SHOW . "') THEN 1 ELSE 0 END ASC")
            ->orderBy('booking_date', 'desc')
            ->orderBy('start_time', 'asc')
            ->paginate(10);

        return view('livewire.barber.bookings.index', [
            'bookings' => $bookings,
            'statuses' => [
                Booking::STATUS_PENDING => 'Pendiente',
                Booking::STATUS_CONFIRMED => 'Confirmada',
                Booking::STATUS_IN_PROGRESS => 'En curso',
                Booking::STATUS_COMPLETED => 'Finalizada',
                Booking::STATUS_CANCELLED => 'Cancelada',
                Booking::STATUS_NO_SHOW => 'No se presentó',
            ]
        ]);
    }
}
