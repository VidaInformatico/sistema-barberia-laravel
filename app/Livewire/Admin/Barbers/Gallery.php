<?php

namespace App\Livewire\Admin\Barbers;

use App\Models\Barber;
use App\Models\GalleryPhoto;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use Illuminate\Support\Str;

class Gallery extends Component
{
    use WithPagination, WithFileUploads;

    public $gallery_photo_id = null;
    public $barber_id;
    public $title;
    public $description;
    public $image;
    public $existing_image;
    public $is_featured = false;
    public $sort_order = 0;

    public $search = '';
    public $perPage = 10;
    public $isOpen = false;

    public $barbers = [];

    public function mount()
    {
        $this->barbers = Barber::with('user')->get();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $branchId = auth()->user()->branch_id;

        $photos = GalleryPhoto::with('barber.user')
            ->whereHas('barber.user', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->where(function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%')
                    ->orWhereHas('barber.user', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('last_name', 'like', '%' . $this->search . '%');
                    });
            })
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin.barbers.gallery', compact('photos'));
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
            'gallery_photo_id',
            'barber_id',
            'title',
            'description',
            'image',
            'existing_image',
            'is_featured',
            'sort_order'
        ]);
        $this->is_featured = false;
        $this->sort_order = 0;
        $this->resetValidation();
    }

    public function store()
    {
        $rules = [
            'barber_id'   => ['required', 'exists:barbers,id'],
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_featured' => ['boolean'],
            'sort_order'  => ['required', 'integer', 'min:0'],
            'image'       => $this->gallery_photo_id ? ['nullable', 'image', 'max:3072'] : ['required', 'image', 'max:3072'],
        ];

        $this->validate($rules);

        $data = [
            'barber_id'   => $this->barber_id,
            'title'       => $this->title,
            'description' => $this->description,
            'is_featured' => $this->is_featured,
            'sort_order'  => $this->sort_order,
        ];

        if ($this->image) {
            if ($this->gallery_photo_id) {
                $oldPhoto = GalleryPhoto::find($this->gallery_photo_id);
                if ($oldPhoto && $oldPhoto->image) {
                    Storage::disk('public')->delete($oldPhoto->image);
                }
            }

            $filename = 'gallery/' . Str::uuid() . '.webp';

            $manager = ImageManager::gd();
            $imageConverted = $manager->read($this->image->getRealPath())->toWebp(80);

            Storage::disk('public')->put($filename, (string) $imageConverted);

            $data['image'] = $filename;
        }

        GalleryPhoto::updateOrCreate(['id' => $this->gallery_photo_id], $data);

        $this->dispatch('swal', [
            'title' => $this->gallery_photo_id ? '¡Actualizada!' : '¡Subida!',
            'text'  => 'La fotografía ha sido guardada en la galería correctamente',
            'icon'  => 'success',
        ]);

        $this->closeModal();
    }

    public function edit($id)
    {
        $photo = GalleryPhoto::findOrFail($id);

        $this->gallery_photo_id = $photo->id;
        $this->barber_id        = $photo->barber_id;
        $this->title            = $photo->title;
        $this->description      = $photo->description;
        $this->existing_image   = $photo->image_url;
        $this->is_featured      = $photo->is_featured;
        $this->sort_order       = $photo->sort_order;
        $this->image            = null;

        $this->openModal();
    }

    public function deleteConfirm($id)
    {
        $this->dispatch('confirm-delete', id: $id);
    }

    #[On('delete-confirmed')]
    public function destroy($id)
    {
        $photo = GalleryPhoto::findOrFail($id);

        $photo->delete();

        $this->dispatch('swal', [
            'title' => 'Eliminada',
            'text'  => 'Fotografía eliminada de la galería correctamente',
            'icon'  => 'success',
        ]);
    }
}
