<?php

namespace App\Livewire\Admin\Barbers;

use App\Models\Barber;
use App\Models\Product;
use App\Models\Service;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class ServiceManager extends Component
{
    use WithPagination;

    public $barber_id = null;
    public $service_id = null;
    public $custom_price = null;

    public $searchBarber = '';
    public $searchService = '';
    public $perPage = 10;
    public $isOpen = false;

    public function updatingSearchBarber()
    {
        $this->resetPage();
    }

    public function render()
    {
        $branchId = auth()->user()->branch_id;

        $barbers = Barber::with('user')
            ->whereHas('user', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->when($this->searchBarber, function ($query) {
                $query->whereHas('user', function ($subQuery) {
                    $subQuery->where('name', 'like', '%' . $this->searchBarber . '%');
                });
            })
            ->withCount('services')
            ->paginate($this->perPage);

        $allServices = Product::where('type', 'service')
            // Si tus productos/servicios también tienen branch_id, descomenta la siguiente línea:
            // ->where('branch_id', $branchId)
            ->where('name', 'like', '%' . $this->searchService . '%')
            ->orderBy('name', 'asc')
            ->get();

        $currentBarberServices = [];
        if ($this->barber_id) {
            $barber = Barber::with('services')->find($this->barber_id);
            $currentBarberServices = $barber ? $barber->services : [];
        }

        return view('livewire.admin.barbers.service-manager', compact(
            'barbers',
            'allServices',
            'currentBarberServices'
        ));
    }

    #[Computed()]
    public function selectedBarber()
    {
        if (!$this->barber_id) {
            return null;
        }

        return Barber::with('user')->find($this->barber_id);
    }

    public function manageServices($barberId)
    {
        $this->resetInputFields();
        $this->barber_id = $barberId;
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
            'barber_id',
            'service_id',
            'custom_price',
            'searchService'
        ]);
        $this->resetValidation();
    }

    /**
     * Guarda o actualiza la relación de un servicio con su precio personalizado
     */
    public function store()
    {
        $rules = [
            'barber_id'    => ['required', 'exists:barbers,id'],
            'service_id'   => ['required', 'exists:services,id'],
            'custom_price' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
        ];

        $this->validate($rules);

        $barber = Barber::findOrFail($this->barber_id);

        // syncWithoutDetaching evita que se borren las relaciones previas si asignas uno nuevo.
        // Si el servicio ya existía, actualiza los datos del pivot (como custom_price).
        $barber->services()->syncWithoutDetaching([
            $this->service_id => [
                'custom_price' => $this->custom_price ?: null
            ]
        ]);

        $this->dispatch('swal', [
            'title' => '¡Asignado!',
            'text'  => 'El servicio y su precio han sido guardados correctamente.',
            'icon'  => 'success',
        ]);

        // Limpiamos los inputs del servicio pero mantenemos el modal abierto para seguir editando el barbero
        $this->reset(['service_id', 'custom_price']);
    }

    /**
     * Carga un servicio asignado al formulario para actualizar su precio personalizado
     */
    public function editPivot($serviceId, $currentCustomPrice)
    {
        $this->service_id = $serviceId;
        $this->custom_price = $currentCustomPrice;
    }

    public function deleteConfirm($serviceId)
    {
        // Despacha confirmación visual pasándole el ID del servicio
        $this->dispatch('confirm-detach', id: $serviceId);
    }

    /**
     * Remueve la relación (Desasignar Servicio del Barbero)
     */
    #[On('detach-confirmed')]
    public function detachService($serviceId)
    {
        if ($this->barber_id) {
            $barber = Barber::findOrFail($this->barber_id);
            $barber->services()->detach($serviceId);

            $this->dispatch('swal', [
                'title' => 'Removido',
                'text'  => 'Servicio desasignado correctamente del barbero.',
                'icon'  => 'success',
            ]);
        }
    }
}
