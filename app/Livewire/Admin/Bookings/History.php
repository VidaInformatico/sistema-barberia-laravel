<?php

namespace App\Livewire\Admin\Bookings;

use App\Models\BookingStatusHistory;
use Livewire\Component;
use Livewire\WithPagination;

class History extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $startDate = '';
    public $endDate = '';

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $branchId = auth()->user()->branch_id;

        $histories = BookingStatusHistory::query()
            ->with(['booking', 'changedBy'])
            ->whereHas('booking', fn($b) => $b->where('branch_id', $branchId))
            ->when($this->search, fn($q) => $q->whereHas('booking', fn($b) => $b->where('booking_number', 'like', "%{$this->search}%")))
            ->when($this->statusFilter, fn($q) => $q->where('new_status', $this->statusFilter))
            ->when($this->startDate, fn($q) => $q->whereDate('created_at', '>=', $this->startDate))
            ->when($this->endDate, fn($q) => $q->whereDate('created_at', '<=', $this->endDate))
            ->latest()
            ->paginate(10);

        return view('livewire.admin.bookings.history', compact('histories'));
    }
}
