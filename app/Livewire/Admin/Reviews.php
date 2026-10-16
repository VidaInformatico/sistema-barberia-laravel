<?php

namespace App\Livewire\Admin;

use App\Models\Rating;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;

class Reviews extends Component
{
    use WithPagination;

    public $scoreFilter = '';
    public $typeFilter = '';

    public function updatedScoreFilter()
    {
        $this->resetPage();
    }

    public function updatedTypeFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Rating::query()
            ->when($this->scoreFilter, fn($q) => $q->where('score', $this->scoreFilter))
            ->when($this->typeFilter, fn($q) => $q->where('rateable_type', 'App\Models\\' . $this->typeFilter));

        $totalReviews = $query->count();
        $average = $query->avg('score') ?? 0;

        $stats = [
            5 => $query->clone()->where('score', 5)->count(),
            4 => $query->clone()->where('score', 4)->count(),
            3 => $query->clone()->where('score', 3)->count(),
            2 => $query->clone()->where('score', 2)->count(),
            1 => $query->clone()->where('score', 1)->count(),
        ];

        $reviews = $query->with(['user', 'rateable'])->latest()->paginate(10);

        return view('livewire.admin.reviews', compact('reviews', 'average', 'totalReviews', 'stats'));
    }

    public function delete($id)
    {
        Rating::where('user_id', Auth::id())->findOrFail($id)->delete();
        LivewireAlert::title('Calificación Eliminada')
            ->success()
            ->asToast()
            ->show();
    }
}
