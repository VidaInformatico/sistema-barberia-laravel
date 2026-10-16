<?php

namespace App\Livewire\Front;

use App\Models\Product;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;

class ServiceRatings extends Component
{
    public $service;
    public $score = 5;
    public $comment = '';

    public function mount($serviceId)
    {
        $this->service = Product::findOrFail($serviceId);
    }

    public function deleteRating($ratingId)
    {
        $rating = \App\Models\Rating::find($ratingId);

        if ($rating && $rating->user_id === Auth::id()) {
            $rating->delete();
            LivewireAlert::title('Reseña eliminada correctamente.')
                ->success()
                ->asToast()
                ->show();
        }
    }

    public function submit()
    {
        $this->validate([
            'score' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:255',
        ]);

        $existingRating = $this->service->ratings()->where('user_id', Auth::id())->first();

        if ($existingRating) {
            LivewireAlert::title('Ya has calificado a este servicio anteriormente.')
                ->warning()
                ->asToast()
                ->show();
            return;
        }

        $this->service->ratings()->create([
            'user_id' => Auth::id(),
            'score' => $this->score,
            'comment' => $this->comment,
        ]);

        $this->reset(['score', 'comment']);
        LivewireAlert::title('¡Gracias por compartir tu opinión!')
            ->success()
            ->asToast()
            ->show();
    }

    public function render()
    {
        $ratings = $this->service->ratings()->with('user')->latest()->get();
        $stats = [
            5 => $this->service->ratings()->where('score', 5)->count(),
            4 => $this->service->ratings()->where('score', 4)->count(),
            3 => $this->service->ratings()->where('score', 3)->count(),
            2 => $this->service->ratings()->where('score', 2)->count(),
            1 => $this->service->ratings()->where('score', 1)->count(),
        ];
        $total = $ratings->count() ?: 1;

        return view('livewire.front.service-ratings', compact('ratings', 'stats', 'total'));
    }
}
