<?php

namespace App\Livewire\Barber;

use App\Models\Rating;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;

class Reviews extends Component
{
    use WithPagination;

    public $scoreFilter = '';

    public function render()
    {
        $reviews = Rating::query()
            ->where('user_id', Auth::id())
            ->when($this->scoreFilter, function ($query) {
                $query->where('score', $this->scoreFilter);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.barber.reviews', compact('reviews'));
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
