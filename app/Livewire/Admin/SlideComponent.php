<?php

namespace App\Livewire\Admin;

use App\Models\HeroSlide;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;

class SlideComponent extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $slide_id = null;
    public $image;
    public $current_image;
    public $subtitle;
    public $title;
    public $text;
    public $btn1_text;
    public $btn1_url;
    public $btn1_icon;
    public $btn2_text;
    public $btn2_url;
    public $btn2_icon;
    public $order = 0;
    public $is_active = true;

    public $search = '';
    public $perPage = 10;
    public $isOpen = false;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $slides = HeroSlide::where('title', 'like', '%' . $this->search . '%')
            ->orWhere('subtitle', 'like', '%' . $this->search . '%')
            ->orderBy('order', 'asc')
            ->paginate($this->perPage);

        return view('livewire.admin.slide-component', compact('slides'));
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
            'slide_id',
            'image',
            'current_image',
            'subtitle',
            'title',
            'text',
            'btn1_text',
            'btn1_url',
            'btn1_icon',
            'btn2_text',
            'btn2_url',
            'btn2_icon',
            'is_active',
            'order'
        ]);
        $this->is_active = true;
        $this->order = 0;
        $this->resetValidation();
    }

    public function store()
    {
        $rules = [
            'image'     => $this->slide_id ? ['nullable', 'image', 'max:3072'] : ['required', 'image', 'max:3072'],
            'title'     => ['required', 'string', 'max:255'],
            'subtitle'  => ['nullable', 'string', 'max:255'],
            'text'      => ['nullable', 'string'],
            'btn1_text' => ['required', 'string', 'max:255'],
            'btn1_url'  => ['required', 'url'],
            'btn1_icon' => ['nullable', 'string', 'max:255'],
            'btn2_text' => ['nullable', 'string', 'max:255'],
            'btn2_url'  => ['nullable', 'url'],
            'btn2_icon' => ['nullable', 'string', 'max:255'],
            'order'     => ['required', 'integer'],
            'is_active' => ['boolean'],
        ];

        $this->validate($rules);

        $data = [
            'title'     => $this->title,
            'subtitle'  => $this->subtitle,
            'text'      => $this->text,
            'btn1_text' => $this->btn1_text,
            'btn1_url'  => $this->btn1_url,
            'btn1_icon' => $this->btn1_icon,
            'btn2_text' => $this->btn2_text,
            'btn2_url'  => $this->btn2_url,
            'btn2_icon' => $this->btn2_icon,
            'order'     => $this->order,
            'is_active' => $this->is_active,
        ];

        if ($this->image) {
            if ($this->slide_id) {
                $oldSlide = HeroSlide::find($this->slide_id);
                if ($oldSlide && $oldSlide->image_url) {
                    Storage::disk('public')->delete($oldSlide->image_url);
                }
            }

            $filename = 'slides/' . Str::uuid() . '.webp';

            $manager = ImageManager::gd();
            $imageConverted = $manager->read($this->image->getRealPath())->toWebp(80);

            Storage::disk('public')->put($filename, (string) $imageConverted);

            $data['image_url'] = $filename;
        }

        HeroSlide::updateOrCreate(['id' => $this->slide_id], $data);

        $this->dispatch('swal', [
            'title' => $this->slide_id ? '¡Actualizado!' : '¡Creado!',
            'text'  => 'El slide ha sido guardado correctamente',
            'icon'  => 'success',
        ]);

        $this->closeModal();
    }

    public function edit($id)
    {
        $slide = HeroSlide::findOrFail($id);

        $this->slide_id      = $slide->id;
        $this->current_image = $slide->image_url;
        $this->subtitle      = $slide->subtitle;
        $this->title         = $slide->title;
        $this->text          = $slide->text;
        $this->btn1_text     = $slide->btn1_text;
        $this->btn1_url      = $slide->btn1_url;
        $this->btn1_icon     = $slide->btn1_icon;
        $this->btn2_text     = $slide->btn2_text;
        $this->btn2_url      = $slide->btn2_url;
        $this->btn2_icon     = $slide->btn2_icon;
        $this->order         = $slide->order;
        $this->is_active     = $slide->is_active;

        $this->openModal();
    }

    public function deleteConfirm($id)
    {
        $this->dispatch('confirm-delete', id: $id);
    }

    #[On('delete-confirmed')]
    public function destroy($id)
    {
        $slide = HeroSlide::findOrFail($id);

        if ($slide->image_url) {
            Storage::disk('public')->delete($slide->image_url);
        }

        $slide->delete();

        $this->dispatch('swal', [
            'title' => 'Eliminado',
            'text'  => 'Slide eliminado correctamente',
            'icon'  => 'success',
        ]);
    }
}
