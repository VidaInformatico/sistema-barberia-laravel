<div>
    <div class="page-container">
        <header class="page-header">
            <div class="flex items-center gap-3">
                <div class="header-icon">
                    <i class="fas fa-id-card"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black tracking-tight text-slate-800 leading-none">Barberos</h2>
                    <p class="text-slate-400 text-[11px] font-bold uppercase tracking-wider mt-1">
                        Gestión de perfiles profesionales y especialidades de los barberos
                    </p>
                </div>
            </div>

            <button wire:click="create" class="btn-primary">
                <i class="fas fa-plus"></i>
                Nuevo Barbero
            </button>
        </header>

        <div class="toolbar-container">
            <div class="search-input-group relative w-full lg:flex-1">
                <input type="text" wire:model.live.debounce.500ms="search" class="search-input"
                    placeholder="Buscar por nombre o especialidad...">
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
            @forelse ($barbers as $item)
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                        <div class="flex items-center gap-3">
                            <img src="{{ $item->user->avatar_url }}" alt="{{ $item->full_name }}"
                                class="w-10 h-10 rounded-full object-cover border border-slate-200">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-slate-700">{{ $item->full_name }}</span>
                                <span class="text-xs text-slate-400">{{ $item->user->email }}</span>
                            </div>
                        </div>
                        <div>
                            @if ($item->is_available)
                                <span
                                    class="px-2 py-1 text-[10px] font-black bg-emerald-100 text-emerald-600 rounded-full uppercase">Disponible</span>
                            @else
                                <span
                                    class="px-2 py-1 text-[10px] font-black bg-rose-100 text-rose-600 rounded-full uppercase">No
                                    Disponible</span>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <span class="text-slate-400 font-medium block">Especialidad:</span>
                            <span
                                class="font-bold text-slate-600 block truncate">{{ $item->specialty ?? 'General' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-medium block">Experiencia:</span>
                            <span class="font-bold text-slate-600 block">{{ $item->experience_years }} años</span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-medium block">Instagram:</span>
                            @if ($item->instagram)
                                <a href="https://instagram.com/{{ ltrim($item->instagram, '@') }}" target="_blank"
                                    class="text-xs font-bold text-indigo-600 hover:underline">
                                    <i class="fab fa-instagram mr-1"></i>{{ $item->instagram }}
                                </a>
                            @else
                                <span class="text-xs text-slate-400">-</span>
                            @endif
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
                    <i class="fas fa-id-card text-slate-200 text-3xl mb-3 block"></i>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">No hay perfiles de barberos
                        configurados</p>
                </div>
            @endforelse
        </div>

        <div class="datatable-container hidden md:block overflow-x-auto">
            <table class="datatable w-full">
                <thead class="datatable-thead">
                    <tr>
                        <th class="datatable-th">Barbero</th>
                        <th class="datatable-th">Especialidad</th>
                        <th class="datatable-th">Experiencia</th>
                        <th class="datatable-th">Instagram</th>
                        <th class="datatable-th">Disponibilidad</th>
                        <th class="datatable-th text-right">Gestión</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($barbers as $item)
                        <tr class="datatable-tr">
                            <td class="datatable-td">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $item->user->avatar_url }}" alt="{{ $item->full_name }}"
                                        class="w-9 h-9 rounded-full object-cover border border-slate-200">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-slate-700">{{ $item->full_name }}</span>
                                        <span class="text-xs text-slate-400">{{ $item->user->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="datatable-td">
                                <span
                                    class="text-xs font-bold text-slate-600">{{ $item->specialty ?? 'General' }}</span>
                            </td>
                            <td class="datatable-td">
                                <span class="text-xs font-bold text-slate-600">{{ $item->experience_years }}
                                    años</span>
                            </td>
                            <td class="datatable-td">
                                @if ($item->instagram)
                                    <a href="https://instagram.com/{{ ltrim($item->instagram, '@') }}" target="_blank"
                                        class="text-xs font-bold text-indigo-600 hover:underline">
                                        <i class="fab fa-instagram mr-1"></i>{{ $item->instagram }}
                                    </a>
                                @else
                                    <span class="text-xs text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="datatable-td">
                                @if ($item->is_available)
                                    <span
                                        class="px-2 py-1 text-[10px] font-black bg-emerald-100 text-emerald-600 rounded-full uppercase">Disponible</span>
                                @else
                                    <span
                                        class="px-2 py-1 text-[10px] font-black bg-rose-100 text-rose-600 rounded-full uppercase">No
                                        Disponible</span>
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
                            <td colspan="6" class="py-16 text-center">
                                <i class="fas fa-id-card text-slate-200 text-3xl mb-3 block"></i>
                                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">No hay perfiles
                                    de barberos configurados</p>
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

            <div class="modal-container max-w-2xl">
                <div class="modal-header">
                    <div class="flex items-center gap-3">
                        <div class="modal-icon-box">
                            <i class="fas {{ $barber_id ? 'fa-edit' : 'fa-plus' }}"></i>
                        </div>
                        <h3 class="modal-title">
                            {{ $barber_id ? 'Editar Perfil de Barbero' : 'Nuevo Perfil de Barbero' }}
                        </h3>
                    </div>
                    <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div class="col-span-2">
                            <label class="modal-label">Usuario (Solo con Rol Barbero)</label>
                            <div class="modal-input-group">
                                <i class="fas fa-user-tie modal-input-icon"></i>
                                <select wire:model="user_id" class="modal-input">
                                    <option value="">Seleccione un usuario...</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->full_name }}
                                            ({{ $user->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('user_id')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="modal-label">Especialidad</label>
                            <div class="modal-input-group">
                                <i class="fas fa-star modal-input-icon"></i>
                                <input type="text" wire:model="specialty" class="modal-input"
                                    placeholder="Ej: Degradados, Barba Clásica">
                            </div>
                            @error('specialty')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="modal-label">Años de Experiencia</label>
                            <div class="modal-input-group">
                                <i class="fas fa-history modal-input-icon"></i>
                                <input type="number" wire:model="experience_years" class="modal-input"
                                    placeholder="Ej: 5">
                            </div>
                            @error('experience_years')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-span-2">
                            <label class="modal-label">Instagram</label>
                            <div class="modal-input-group">
                                <i class="fab fa-instagram modal-input-icon"></i>
                                <input type="text" wire:model="instagram" class="modal-input"
                                    placeholder="Ej: @barber_name">
                            </div>
                            @error('instagram')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-span-2">
                            <label class="modal-label">Biografía</label>
                            <textarea wire:model="bio" class="modal-input min-h-[100px]"
                                placeholder="Breve descripción de la trayectoria del barbero..."></textarea>
                            @error('bio')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-span-2 py-2">
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <div class="relative">
                                    <input type="checkbox" wire:model="is_available" class="sr-only peer">
                                    <div
                                        class="w-10 h-5 bg-slate-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600">
                                    </div>
                                </div>
                                <span
                                    class="text-xs font-bold text-slate-600 uppercase group-hover:text-indigo-600 transition-colors">¿Está
                                    disponible para citas?</span>
                            </label>
                            @error('is_available')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button wire:click="closeModal" class="btn-modal-secondary">Cancelar</button>
                    <button wire:click="store" class="btn-modal-primary" wire:loading.attr="disabled">
                        <span class="relative z-10" wire:loading.remove wire:target="store">
                            {{ $barber_id ? 'Actualizar' : 'Guardar' }}
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
                    title: '¿Eliminar Perfil de Barbero?',
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
