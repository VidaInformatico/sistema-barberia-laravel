<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use App\Models\Branch;
use Intervention\Image\ImageManager;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;

class Index extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $user_id = null;
    public $name;
    public $last_name;
    public $email;
    public $password;
    public $phone;
    public $role = 'client';
    public $is_active = true;
    public $avatar;
    public $branch_id;
    public $bluetooth_printing = false;
    public $bluetooth_print_size;
    public $direct_pc_printing = false;
    public $pc_print_size;
    public $pc_printer_name;
    public $branches = [];

    public $activeTab = 'users';
    public $search = '';
    public $perPage = 10;
    public $isOpen = false;

    public function mount()
    {
        $this->branches = Branch::all();
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $users = User::with('branch')
            ->where(function ($query) {
                if ($this->activeTab === 'users') {
                    $query->whereIn('role', ['admin', 'barber']);
                } else {
                    $query->where('role', 'client');
                }
            })
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('last_name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('phone', 'like', '%' . $this->search . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin.users.index', compact('users'));
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->reset([
            'user_id', 'name', 'last_name', 'email', 'password', 'phone', 
            'role', 'is_active', 'avatar', 'branch_id', 
            'bluetooth_printing', 'bluetooth_print_size', 
            'direct_pc_printing', 'pc_print_size', 'pc_printer_name'
        ]);
        $this->is_active = true;
        $this->role = 'client';
        $this->bluetooth_printing = false;
        $this->direct_pc_printing = false;
        $this->resetValidation();
    }

    public function store()
    {
        $slug = \Illuminate\Support\Str::slug($this->name . ' ' . $this->last_name);

        $originalSlug = $slug;
        $count = 1;
        while (User::where('slug', $slug)->where('id', '!=', $this->user_id)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        $rules = [
            'name'                 => ['required', 'string', 'max:255'],
            'last_name'            => ['required', 'string', 'max:255'],
            'email'                => ['required', 'email', 'max:255', 'unique:users,email,' . $this->user_id],
            'password'             => $this->user_id ? ['nullable', 'string', 'min:8'] : ['required', 'string', 'min:8'],
            'phone'                => ['nullable', 'string', 'max:20'],
            'role'                 => ['required', 'in:admin,barber,client'],
            'is_active'            => ['boolean'],
            'avatar'               => $this->avatar instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile
                ? ['nullable', 'image', 'max:2048']
                : ['nullable', 'string'],
            'branch_id'            => $this->role !== 'client' ? ['required', 'exists:branches,id'] : ['nullable'],
            'bluetooth_printing'   => ['boolean'],
            'bluetooth_print_size' => ['nullable', 'in:58,80'],
            'direct_pc_printing'   => ['boolean'],
            'pc_print_size'        => ['nullable', 'in:58,80'],
            'pc_printer_name'      => $this->role !== 'client' && $this->direct_pc_printing ? ['required', 'string', 'max:255'] : ['nullable'],
        ];

        $this->validate($rules);

        $data = [
            'name'                 => $this->name,
            'last_name'            => $this->last_name,
            'slug'                 => $slug,
            'email'                => $this->email,
            'phone'                => $this->phone,
            'role'                 => $this->role,
            'is_active'            => $this->is_active,
            'branch_id'            => $this->role !== 'client' ? $this->branch_id : null,
            'bluetooth_printing'   => $this->role !== 'client' ? $this->bluetooth_printing : false,
            'bluetooth_print_size' => $this->role !== 'client' ? $this->bluetooth_print_size : null,
            'direct_pc_printing'   => $this->role !== 'client' ? $this->direct_pc_printing : false,
            'pc_print_size'        => $this->role !== 'client' ? $this->pc_print_size : null,
            'pc_printer_name'      => $this->role !== 'client' && $this->direct_pc_printing ? $this->pc_printer_name : null,
        ];

        if ($this->avatar instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
            if ($this->user_id) {
                $oldUser = User::find($this->user_id);
                if ($oldUser && $oldUser->avatar) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($oldUser->avatar);
                }
            }

            $filename = 'avatars/' . \Illuminate\Support\Str::uuid() . '.webp';

            $manager = ImageManager::gd();
            $imageConverted = $manager->read($this->avatar->getRealPath())->toWebp(80);

            \Illuminate\Support\Facades\Storage::disk('public')->put($filename, (string) $imageConverted);

            $data['avatar'] = $filename;
        }

        if ($this->password) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($this->password);
        }

        User::updateOrCreate(['id' => $this->user_id], $data);

        $this->dispatch('swal', [
            'title' => $this->user_id ? '¡Actualizado!' : '¡Creado!',
            'text'  => 'El usuario ha sido guardado correctamente',
            'icon'  => 'success',
        ]);

        $this->closeModal();
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);

        $this->user_id              = $user->id;
        $this->name                 = $user->name;
        $this->last_name            = $user->last_name;
        $this->email                = $user->email;
        $this->phone                = $user->phone;
        $this->role                 = $user->role;
        $this->is_active            = $user->is_active;
        $this->avatar               = $user->avatar;
        $this->branch_id            = $user->branch_id;
        $this->bluetooth_printing   = $user->bluetooth_printing ?? false;
        $this->bluetooth_print_size = $user->bluetooth_print_size;
        $this->direct_pc_printing   = $user->direct_pc_printing ?? false;
        $this->pc_print_size        = $user->pc_print_size;
        $this->pc_printer_name      = $user->pc_printer_name;
        $this->password             = '';

        $this->openModal();
    }

    public function deleteConfirm($id)
    {
        $this->dispatch('confirm-delete', id: $id);
    }

    #[On('delete-confirmed')]
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        $this->dispatch('swal', [
            'title' => 'Eliminado',
            'text'  => 'Usuario eliminado correctamente',
            'icon'  => 'success',
        ]);
    }
}