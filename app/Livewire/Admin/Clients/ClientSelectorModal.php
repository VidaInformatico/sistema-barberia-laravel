<?php

namespace App\Livewire\Admin\Clients;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class ClientSelectorModal extends Component
{
    use WithPagination;

    public $open = false;
    public $tab = 'search';

    public $search = '';

    public $name = '';
    public $last_name = '';
    public $email = '';
    public $password = '';
    public $phone = '';
    public $avatar = '';

    protected $listeners = ['openClientModal' => 'openModal'];

    public function updatingSearch()
    {
        $this->resetPage(pageName: 'clientsPage');
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->reset(['name', 'last_name', 'email', 'password', 'phone', 'avatar']);
        $this->open = true;
    }

    public function closeModal()
    {
        $this->open = false;
    }

    public function selectClient($clientId)
    {
        $this->dispatch('clientSelected', clientId: $clientId);
        $this->closeModal();
    }

    public function saveClient()
    {
        $validated = $this->validate([
            'name'      => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email'     => 'required|email|max:150|unique:users,email',
            'password'  => 'nullable|string|min:6',
            'phone'     => 'required|string|max:20',
            'avatar'    => 'nullable|string|max:255',
        ]);

        $validated['role'] = 'client';
        $validated['is_active'] = true;
        $validated['slug'] = Str::slug($validated['name'] . ' ' . $validated['last_name']) . '-' . Str::random(5);
        $validated['password'] = empty($this->password) ? Hash::make($this->phone) : Hash::make($this->password);

        $client = User::create($validated);

        $this->dispatch('clientSelected', clientId: $client->id);
        $this->reset(['name', 'last_name', 'email', 'password', 'phone', 'avatar']);
        $this->closeModal();
    }

    public function render()
    {
        $clients = User::query()
            ->where('role', 'client')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('last_name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('phone', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(5, pageName: 'clientsPage');

        return view('livewire.admin.clients.client-selector-modal', compact('clients'));
    }
}