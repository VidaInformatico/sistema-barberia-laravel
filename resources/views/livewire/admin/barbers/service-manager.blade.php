<div>
    <div class="page-container">
        <header class="page-header">
            <div class="flex items-center gap-3">
                <div class="header-icon">
                    <i class="fas fa-scissors"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black tracking-tight text-slate-800 leading-none">Asignación de Servicios
                    </h2>
                    <p class="text-slate-400 text-[11px] font-bold uppercase tracking-wider mt-1">
                        Gestión de Personal & Precios Personalizados
                    </p>
                </div>
            </div>
        </header>

        <div class="toolbar-container">
            <div class="search-input-group relative w-full lg:flex-1">
                <input type="text" wire:model.live.debounce.500ms="searchBarber" class="search-input"
                    placeholder="Buscar barbero por nombre o especialidad...">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                    <i class="fas fa-search text-sm"></i>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-between w-full lg:w-auto gap-3">
                <div class="per-page-select flex items-center bg-slate-50 px-3 py-2 rounded-lg border border-slate-100">
                    <label
                        class="text-[10px] font-black text-slate-400 uppercase border-r border-slate-200 pr-3 mr-3 leading-none">
                        Mostrar
                    </label>
                    <select wire:model.live="perPage"
                        class="bg-transparent border-none p-0 pr-8 text-sm font-bold text-slate-700 focus:ring-0 cursor-pointer">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="block md:hidden space-y-3">
            @forelse($barbers as $barber)
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm space-y-3"
                    wire:key="barber-service-card-{{ $barber->id }}">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                        <div class="flex items-center gap-3">
                            @if ($barber->user->avatar)
                                <img src="{{ Storage::url($barber->user->avatar) }}"
                                    class="w-10 h-10 rounded-full object-cover ring-2 ring-slate-100">
                            @else
                                <div
                                    class="w-10 h-10 bg-indigo-50 text-indigo-600 flex items-center justify-center rounded-full font-black uppercase text-[10px] ring-2 ring-indigo-100">
                                    {{ Str::substr($barber->user->name, 0, 2) }}
                                </div>
                            @endif
                            <div class="flex flex-col">
                                <span
                                    class="text-sm font-bold text-slate-700 leading-tight">{{ $barber->user->name }}</span>
                                <span
                                    class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-0.5">{{ $barber->specialty ?? 'General' }}</span>
                            </div>
                        </div>
                        <div>
                            @if ($barber->is_available)
                                <span
                                    class="px-2 py-1 text-[10px] font-black bg-emerald-100 text-emerald-600 rounded-full uppercase">Disponible</span>
                            @else
                                <span
                                    class="px-2 py-1 text-[10px] font-black bg-slate-100 text-slate-400 rounded-full uppercase">Inactivo</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs pt-1">
                        <span class="text-slate-400 font-medium block">Servicios Activos:</span>
                        <span
                            class="px-2 py-1 text-[10px] font-black bg-slate-100 text-slate-600 rounded-md uppercase tracking-wide">
                            {{ $barber->services_count }} asignados
                        </span>
                    </div>

                    <div class="pt-2 border-t border-slate-100 flex items-center justify-end">
                        <button wire:click="manageServices({{ $barber->id }})"
                            class="w-full sm:w-auto px-4 py-2 text-xs font-black uppercase tracking-wider text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors flex items-center justify-center gap-2">
                            <i class="fas fa-cog text-xs"></i>
                            <span>Gestionar Servicios</span>
                        </button>
                    </div>
                </div>
            @empty
                <div class="bg-white p-8 rounded-xl border border-slate-200 text-center">
                    <i class="fas fa-scissors text-slate-200 text-3xl mb-3 block"></i>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">No se encontraron barberos
                        registrados</p>
                </div>
            @endforelse
        </div>

        <div class="datatable-container hidden md:block overflow-x-auto">
            <table class="datatable w-full">
                <thead class="datatable-thead">
                    <tr>
                        <th class="datatable-th">Barbero / Especialista</th>
                        <th class="datatable-th">Servicios Activos</th>
                        <th class="datatable-th">Estado</th>
                        <th class="datatable-th text-right">Gestión</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($barbers as $barber)
                        <tr class="datatable-tr" wire:key="barber-service-row-{{ $barber->id }}">
                            <td class="datatable-td">
                                <div class="flex items-center gap-3">
                                    @if ($barber->user->avatar)
                                        <img src="{{ Storage::url($barber->user->avatar) }}"
                                            class="w-8 h-8 rounded-full object-cover ring-2 ring-slate-100">
                                    @else
                                        <div
                                            class="w-8 h-8 bg-indigo-50 text-indigo-600 flex items-center justify-center rounded-full font-black uppercase text-[10px] ring-2 ring-indigo-100">
                                            {{ Str::substr($barber->user->name, 0, 2) }}
                                        </div>
                                    @endif
                                    <div>
                                        <span
                                            class="text-sm font-bold text-slate-700 block leading-tight">{{ $barber->user->name }}</span>
                                        <span
                                            class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block mt-0.5">{{ $barber->specialty ?? 'General' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="datatable-td">
                                <span
                                    class="px-2 py-1 text-[10px] font-black bg-slate-100 text-slate-600 rounded-md uppercase tracking-wide">
                                    {{ $barber->services_count }} asignados
                                </span>
                            </td>
                            <td class="datatable-td">
                                @if ($barber->is_available)
                                    <span
                                        class="px-2 py-1 text-[10px] font-black bg-emerald-100 text-emerald-600 rounded-full uppercase">Disponible</span>
                                @else
                                    <span
                                        class="px-2 py-1 text-[10px] font-black bg-slate-100 text-slate-400 rounded-full uppercase">Inactivo</span>
                                @endif
                            </td>
                            <td class="datatable-td text-right">
                                <div class="action-group inline-flex">
                                    <button wire:click="manageServices({{ $barber->id }})"
                                        class="btn-action hover:bg-indigo-50 hover:text-indigo-600 px-3 py-1.5 flex items-center gap-2"
                                        title="Gestionar Servicios">
                                        <i class="fas fa-cog text-xs"></i>
                                        <span class="text-[10px] font-black uppercase tracking-wider">Gestionar</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-16 text-center">
                                <i class="fas fa-scissors text-slate-200 text-3xl mb-3 block"></i>
                                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">No se encontraron
                                    barberos registrados</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 px-2">
            {{ $barbers->links() }}
        </div>
    </div>

    @if ($isOpen)
        <div class="modal-overlay">
            <div class="modal-backdrop" wire:click="closeModal"></div>

            <div class="modal-container max-w-4xl">
                <div class="modal-header">
                    <div class="flex items-center gap-3">
                        <div class="modal-icon-box">
                            <i class="fas fa-sliders-h"></i>
                        </div>
                        <h3 class="modal-title">
                            Servicios de: <span
                                class="text-indigo-600 font-black">{{ $this->selectedBarber?->user?->name ?? 'Configurar' }}</span>
                        </h3>
                    </div>
                    <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>

                <div class="modal-body p-0">
                    <div class="grid grid-cols-1 md:grid-cols-5 divide-y md:divide-y-0 md:divide-x divide-slate-100">

                        <div class="p-6 md:col-span-2 bg-slate-50/50">
                            <h4 class="text-[10px] font-black uppercase tracking-wider text-slate-400 mb-4 block">
                                Asignar / Editar Servicio</h4>

                            <form wire:submit.prevent="store" class="grid grid-cols-1 gap-4">
                                <div>
                                    <label class="modal-label">Filtrar & Seleccionar Servicio</label>
                                    <input type="text" wire:model.live="searchService"
                                        placeholder="Filtrar por nombre..."
                                        class="modal-input text-xs py-1.5 px-3 mb-2 bg-white">

                                    <div class="modal-input-group">
                                        <i class="fas fa-concierge-bell modal-input-icon"></i>
                                        <select wire:model="service_id" class="modal-input bg-transparent py-2.5">
                                            <option value="">-- Elige un Servicio --</option>
                                            @foreach ($allServices as $serv)
                                                <option value="{{ $serv->id }}">{{ $serv->name }}
                                                    ({{ $company->curency_code }}
                                                    {{ number_format($serv->price, 2) }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('service_id')
                                        <span
                                            class="text-red-500 text-[10px] font-bold block mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label class="modal-label">Precio Personalizado (Opcional)</label>
                                    <div class="modal-input-group relative flex items-center">
                                        <span
                                            class="absolute left-4 top-1/2 -translate-y-1/2 font-bold text-slate-400 text-xs pointer-events-none">
                                            {{ $company->curency_code }}
                                        </span>
                                        <input type="number" step="0.01" wire:model="custom_price"
                                            class="modal-input pl-9 w-full"
                                            placeholder="Vacío para usar precio original">
                                    </div>
                                    <p class="text-[9px] text-slate-400 mt-1 font-medium leading-tight">* Si se deja
                                        vacío, se asume el costo original por defecto.</p>
                                    @error('custom_price')
                                        <span
                                            class="text-red-500 text-[10px] font-bold block mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="pt-2 flex gap-2">
                                    <button type="submit" class="btn-modal-primary flex-1 justify-center py-2.5">
                                        <span wire:loading.remove wire:target="store">Guardar</span>
                                        <span wire:loading wire:target="store">Procesando...</span>
                                        <i class="fas fa-check-circle text-xs text-indigo-300 ml-1"></i>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="p-6 md:col-span-3 flex flex-col justify-between">
                            <div>
                                <h4 class="text-[10px] font-black uppercase tracking-wider text-slate-400 mb-4 block">
                                    Listado de servicios asignados</h4>

                                <div class="overflow-hidden border border-slate-100 rounded-lg bg-white">
                                    <table class="w-full text-left border-collapse text-[11px]">
                                        <thead>
                                            <tr
                                                class="bg-slate-50 font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 text-[9px]">
                                                <th class="p-2.5">Servicio</th>
                                                <th class="p-2.5">Precio Original</th>
                                                <th class="p-2.5">Precio Final</th>
                                                <th class="p-2.5 text-right">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-50">
                                            @forelse($currentBarberServices as $bs)
                                                <tr class="hover:bg-slate-50/50 transition-colors">
                                                    <td
                                                        class="p-2.5 font-bold text-slate-700 uppercase tracking-tight">
                                                        {{ $bs->name }}</td>
                                                    <td class="p-2.5 text-slate-400">{{ $company->curency_code }}
                                                        {{ number_format($bs->price, 2) }}</td>
                                                    <td class="p-2.5">
                                                        @if ($bs->pivot->custom_price)
                                                            <span
                                                                class="text-amber-600 font-black">{{ $company->curency_code }}
                                                                {{ number_format($bs->pivot->custom_price, 2) }}</span>
                                                            <span
                                                                class="text-[8px] font-bold block text-amber-500/80 uppercase tracking-tighter">(Especial)</span>
                                                        @else
                                                            <span
                                                                class="text-emerald-600 font-bold">{{ $company->curency_code }}
                                                                {{ number_format($bs->price, 2) }}</span>
                                                            <span
                                                                class="text-[8px] text-slate-400 block uppercase tracking-tighter">(Base)</span>
                                                        @endif
                                                    </td>
                                                    <td class="p-2.5 text-right">
                                                        <div class="action-group inline-flex">
                                                            <button type="button"
                                                                wire:click="editPivot({{ $bs->id }}, '{{ $bs->pivot->custom_price }}')"
                                                                class="btn-action border-r hover:bg-amber-50 hover:text-amber-600 py-2"
                                                                title="Editar">
                                                                <i class="fas fa-edit text-[10px]"></i>
                                                            </button>
                                                            <button type="button"
                                                                wire:click="deleteConfirm({{ $bs->id }})"
                                                                class="btn-action hover:bg-red-50 hover:text-red-600 py-2"
                                                                title="Quitar">
                                                                <i class="fas fa-trash-alt text-[10px]"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4"
                                                        class="text-center py-10 text-slate-400 uppercase tracking-wider font-bold text-[9px]">
                                                        Sin servicios asociados actualmente
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button wire:click="closeModal" class="btn-modal-secondary">Cerrar Ventana</button>
                </div>
            </div>
        </div>
    @endif

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('confirm-detach', (event) => {
                Swal.fire({
                    title: '¿Desasignar servicio?',
                    text: "El barbero ya no podrá ofrecer este servicio en sus reservas.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#4F46E5',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Sí, remover',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch('detach-confirmed', {
                            serviceId: event.id
                        });
                    }
                });
            });
        });
    </script>
</div>
