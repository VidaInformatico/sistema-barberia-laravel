<?php

namespace App\Livewire\Front;

use App\Models\Barber;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;

class BarberRatings extends Component
{
    public $barber;
    public $score = 5;
    public $comment = '';

    public function mount($barberId)
    {
        $this->barber = Barber::findOrFail($barberId);
    }

    public function deleteRating($ratingId)
    {
        $rating = \App\Models\Rating::find($ratingId);

        if ($rating && $rating->user_id === Auth::id()) {
            $rating->delete();
            LivewireAlert::title('Reseña eliminada.')
                ->success()
                ->asToast()
                ->show();
        }
    }

    public function submit()
    {
        $this->validate([
            'score' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:5|max:500',
        ]);

        $existingRating = $this->barber->ratings()->where('user_id', Auth::id())->first();

        if ($existingRating) {
            LivewireAlert::title('Ya has calificado a este barbero anteriormente.')
                ->warning()
                ->asToast()
                ->show();
            return;
        }

        $this->barber->ratings()->create([
            'user_id' => Auth::id(),
            'score' => $this->score,
            'comment' => $this->comment,
        ]);

        $this->reset(['score', 'comment']);

        LivewireAlert::title('¡Gracias por tu reseña!')
            ->success()
            ->asToast()
            ->show();
    }

    public function render()
    {
        $ratings = $this->barber->ratings()->with('user')->latest()->get();

        $stats = [
            5 => $this->barber->ratings()->where('score', 5)->count(),
            4 => $this->barber->ratings()->where('score', 4)->count(),
            3 => $this->barber->ratings()->where('score', 3)->count(),
            2 => $this->barber->ratings()->where('score', 2)->count(),
            1 => $this->barber->ratings()->where('score', 1)->count(),
        ];

        $total = $ratings->count() ?: 1;

        return view('livewire.front.barber-ratings', compact('ratings', 'stats', 'total'));
    }
}
