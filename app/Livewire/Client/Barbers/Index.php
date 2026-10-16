<?php

namespace App\Livewire\Client\Barbers;

use App\Models\Barber;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;


    public function render()
    {
        $barbers = Barber::with('user.branch')
            ->whereHas('user', function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('last_name', 'like', '%' . $this->search . '%');
            })
            ->orWhere('specialty', 'like', '%' . $this->search . '%')
            ->paginate($this->perPage);

        return view('livewire.client.barbers.index', compact('barbers'));
    }
}
