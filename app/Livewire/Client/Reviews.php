<?php

namespace App\Livewire\Client;

use App\Models\Rating;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;

class Reviews extends Component
{
    use WithPagination;

    public $scoreFilter = '';
    public $editingId = null;
    public $editComment = '';
    public $editScore = 0;

    public function render()
    {
        $reviews = Rating::query()
            ->where('user_id', Auth::id())
            ->when($this->scoreFilter, function ($query) {
                $query->where('score', $this->scoreFilter);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.client.reviews', compact('reviews'));
    }

    public function edit($id)
    {
        $review = Rating::where('user_id', Auth::id())->findOrFail($id);
        $this->editingId = $id;
        $this->editComment = $review->comment;
        $this->editScore = $review->score;
    }

    public function update()
    {
        $review = Rating::where('user_id', Auth::id())->findOrFail($this->editingId);
        $review->update([
            'comment' => $this->editComment,
            'score' => $this->editScore,
        ]);

        $this->reset(['editingId', 'editComment', 'editScore']);
        LivewireAlert::title('Calificación Modificada')
            ->success()
            ->asToast()
            ->show();
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
