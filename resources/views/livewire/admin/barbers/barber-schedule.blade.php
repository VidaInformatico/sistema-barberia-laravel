<div>
    <div class="page-container">
        <header class="page-header">
            <div class="flex items-center gap-3">
                <div class="header-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black tracking-tight text-slate-800 leading-none">Horarios de Barberos</h2>
                    <p class="text-slate-400 text-[11px] font-bold uppercase tracking-wider mt-1">
                        Configuración de las jornadas y turnos de trabajo semanales
                    </p>
                </div>
            </div>

            <button wire:click="create" class="btn-primary">
                <i class="fas fa-plus"></i>
                Asignar Horario
            </button>
        </header>

        <div class="toolbar-container">
            <div class="search-input-group relative w-full lg:flex-1">
                <input type="text" wire:model.live.debounce.500ms="search" class="search-input"
                    placeholder="Buscar por barbero...">
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
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="block md:hidden space-y-3">
            @forelse ($schedules as $item)
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm space-y-3"
                    wire:key="schedule-card-{{ $item->id }}">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                        <div class="flex items-center gap-3">
                            <img src="{{ $item->barber->user->avatar_url }}" alt="{{ $item->barber->full_name }}"
                                class="w-10 h-10 rounded-full object-cover border border-slate-200">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-slate-700">{{ $item->barber->full_name }}</span>
                                <span class="text-xs text-slate-400">{{ $item->barber->specialty ?? 'General' }}</span>
                            </div>
                        </div>
                        <div>
                            @if ($item->is_active)
                                <span
                                    class="px-2 py-1 text-[10px] font-black bg-emerald-100 text-emerald-600 rounded-full uppercase">Activo</span>
                            @else
                                <span
                                    class="px-2 py-1 text-[10px] font-black bg-slate-100 text-slate-400 rounded-full uppercase">Inactivo
                                    (Cerrado)</span>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <span class="text-slate-400 font-medium block">Día de la Semana:</span>
                            <span
                                class="inline-block mt-1 px-2.5 py-0.5 text-xs font-bold text-slate-700 bg-slate-100 rounded-lg">
                                {{ $item->day_name }}
                            </span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-medium block">Jornada Laboral:</span>
                            <span
                                class="inline-block mt-1 text-xs font-black text-indigo-600 bg-indigo-50/70 border border-indigo-100/50 px-2 py-0.5 rounded-md">
                                <i class="far fa-clock mr-1 text-[10px]"></i>
                                {{ substr($item->start_time, 0, 5) }} - {{ substr($item->end_time, 0, 5) }}
                            </span>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button wire:click="edit({{ $item->id }})"
                            class="p-2 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-600 transition-colors"
                            title="Editar">
                            <i class="fas fa-edit text-sm"></i>
                        </button>
                        <button wire:click="deleteConfirm({{ $item->id }})"
                            class="p-2 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 transition-colors"
                            title="Eliminar">
                            <i class="fas fa-trash-alt text-sm"></i>
                        </button>
                    </div>
                </div>
            @empty
                <div class="bg-white p-8 rounded-xl border border-slate-200 text-center">
                    <i class="fas fa-clock text-slate-200 text-3xl mb-3 block"></i>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">No hay horarios definidos para
                        los barberos</p>
                </div>
            @endforelse
        </div>

        <div class="datatable-container hidden md:block overflow-x-auto">
            <table class="datatable w-full">
                <thead class="datatable-thead">
                    <tr>
                        <th class="datatable-th">Barbero</th>
                        <th class="datatable-th">Día de la Semana</th>
                        <th class="datatable-th">Jornada Laboral</th>
                        <th class="datatable-th">Estado de Atención</th>
                        <th class="datatable-th text-right">Gestión</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($schedules as $item)
                        <tr class="datatable-tr" wire:key="schedule-row-{{ $item->id }}">
                            <td class="datatable-td">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $item->barber->user->avatar_url }}"
                                        alt="{{ $item->barber->full_name }}"
                                        class="w-9 h-9 rounded-full object-cover border border-slate-200">
                                    <div class="flex flex-col">
                                        <span
                                            class="text-sm font-bold text-slate-700">{{ $item->barber->full_name }}</span>
                                        <span
                                            class="text-xs text-slate-400">{{ $item->barber->specialty ?? 'General' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="datatable-td">
                                <span class="px-3 py-1 text-xs font-bold text-slate-700 bg-slate-100 rounded-lg">
                                    {{ $item->day_name }}
                                </span>
                            </td>
                            <td class="datatable-td">
                                <span
                                    class="text-xs font-black text-indigo-600 bg-indigo-50/70 border border-indigo-100/50 px-2.5 py-1 rounded-md">
                                    <i class="far fa-clock mr-1"></i>
                                    {{ substr($item->start_time, 0, 5) }} - {{ substr($item->end_time, 0, 5) }}
                                </span>
                            </td>
                            <td class="datatable-td">
                                @if ($item->is_active)
                                    <span
                                        class="px-2 py-1 text-[10px] font-black bg-emerald-100 text-emerald-600 rounded-full uppercase">Activo</span>
                                @else
                                    <span
                                        class="px-2 py-1 text-[10px] font-black bg-slate-100 text-slate-400 rounded-full uppercase">Inactivo
                                        (Cerrado)</span>
                                @endif
                            </td>
                            <td class="datatable-td text-right">
                                <div class="action-group">
                                    <button wire:click="edit({{ $item->id }})"
                                        class="btn-action hover:bg-amber-50 hover:text-amber-600 border-r"
                                        title="Editar">
                                        <i class="fas fa-edit text-xs"></i>
                                    </button>
                                    <button wire:click="deleteConfirm({{ $item->id }})"
                                        class="btn-action hover:bg-red-50 hover:text-red-600" title="Eliminar">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-16 text-center">
                                <i class="fas fa-clock text-slate-200 text-3xl mb-3 block"></i>
                                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">No hay horarios
                                    definidos para los barberos</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 px-2">
            {{ $schedules->links() }}
        </div>
    </div>

    @if ($isOpen)
        <div class="modal-overlay">
            <div class="modal-backdrop" wire:click="closeModal"></div>

            <div class="modal-container max-w-2xl">
                <div class="modal-header">
                    <div class="flex items-center gap-3">
                        <div class="modal-icon-box">
                            <i class="fas {{ $schedule_id ? 'fa-edit' : 'fa-plus' }}"></i>
                        </div>
                        <h3 class="modal-title">
                            {{ $schedule_id ? 'Editar Horario' : 'Asignar Nuevo Horario' }}
                        </h3>
                    </div>
                    <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div class="col-span-2">
                            <label class="modal-label">Barbero</label>
                            <div class="modal-input-group">
                                <i class="fas fa-user-tie modal-input-icon"></i>
                                <select wire:model="barber_id" class="modal-input"
                                    {{ $schedule_id ? 'disabled' : '' }}>
                                    <option value="">Seleccione un barbero...</option>
                                    @foreach ($barbers as $barber)
                                        <option value="{{ $barber->id }}">{{ $barber->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('barber_id')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-span-2">
                            <label class="modal-label">Día Operativo</label>
                            <div class="modal-input-group">
                                <i class="fas fa-calendar-day modal-input-icon"></i>
                                <select wire:model="day_of_week" class="modal-input">
                                    <option value="">Seleccione un día...</option>
                                    @foreach ($days_list as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('day_of_week')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="modal-label">Hora de Apertura / Entrada</label>
                            <div class="modal-input-group">
                                <i class="far fa-clock modal-input-icon"></i>
                                <input type="time" wire:model="start_time" class="modal-input">
                            </div>
                            @error('start_time')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="modal-label">Hora de Cierre / Salida</label>
                            <div class="modal-input-group">
                                <i class="far fa-clock modal-input-icon"></i>
                                <input type="time" wire:model="end_time" class="modal-input">
                            </div>
                            @error('end_time')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-span-2 py-2">
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <div class="relative">
                                    <input type="checkbox" wire:model="is_active" class="sr-only peer">
                                    <div
                                        class="w-10 h-5 bg-slate-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600">
                                    </div>
                                </div>
                                <span
                                    class="text-xs font-bold text-slate-600 uppercase group-hover:text-indigo-600 transition-colors">¿Horario
                                    Operativo Activo?</span>
                            </label>
                            @error('is_active')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button wire:click="closeModal" class="btn-modal-secondary">Cancelar</button>
                    <button wire:click="store" class="btn-modal-primary" wire:loading.attr="disabled">
                        <span class="relative z-10" wire:loading.remove wire:target="store">
                            {{ $schedule_id ? 'Actualizar' : 'Guardar' }}
                        </span>
                        <span class="relative z-10" wire:loading wire:target="store">
                            Procesando...
                        </span>
                        <i
                            class="fas fa-check-circle text-indigo-400 group-hover:text-white transition-colors text-xs relative z-10"></i>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('confirm-delete', data => {
                Swal.fire({
                    title: '¿Eliminar Horario de Trabajo?',
                    text: "Esta acción no se puede deshacer.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#4F46E5',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch('delete-confirmed', {
                            id: data.id
                        });
                    }
                });
            });
        });
    </script>
</div>
