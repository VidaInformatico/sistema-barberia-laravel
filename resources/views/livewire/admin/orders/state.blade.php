<div>
    <div class="page-container">
        <header class="page-header">
            <div class="flex items-center gap-3">
                <div class="header-icon">
                    <i class="fas fa-truck-loading"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black tracking-tight text-slate-800 leading-none">Estados de Orden</h2>
                    <p class="text-slate-400 text-[11px] font-bold uppercase tracking-wider mt-1">
                        Gestión del flujo y estados de los pedidos
                    </p>
                </div>
            </div>

            <button wire:click="create" class="btn-primary">
                <i class="fas fa-plus"></i>
                Nuevo Estado
            </button>
        </header>

        <div class="toolbar-container">
            <div class="search-input-group relative w-full lg:flex-1">
                <input type="text" wire:model.live.debounce.500ms="search" class="search-input"
                    placeholder="Buscar por nombre o slug...">
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

        <!-- Vista Móvil (Tarjetas) -->
        <div class="block md:hidden space-y-3">
            @forelse ($states as $item)
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm space-y-3"
                    wire:key="state-card-{{ $item->id }}">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                        <div class="flex flex-col pr-2">
                            <span class="text-sm font-bold text-slate-700 leading-tight">{{ $item->name }}</span>
                            <span class="text-[11px] text-slate-400 font-mono mt-0.5">{{ $item->slug }}</span>
                        </div>
                        <div
                            class="flex items-center gap-1.5 shrink-0 bg-slate-50 px-2 py-1 rounded-lg border border-slate-100">
                            <span class="w-3.5 h-3.5 rounded-full border border-slate-200 shadow-sm"
                                style="background-color: {{ $item->color }};"></span>
                            <span class="text-xs font-bold text-slate-600 font-mono">{{ $item->color }}</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs">
                        <div>
                            <span class="text-slate-400 font-medium block mb-1">Propiedades:</span>
                            <div class="flex flex-wrap gap-1">
                                @if ($item->is_initial)
                                    <span
                                        class="px-2 py-0.5 text-[10px] font-black bg-blue-100 text-blue-600 rounded-full uppercase">Inicial</span>
                                @endif
                                @if ($item->is_final)
                                    <span
                                        class="px-2 py-0.5 text-[10px] font-black bg-emerald-100 text-emerald-600 rounded-full uppercase">Final</span>
                                @endif
                                @if ($item->is_canceled)
                                    <span
                                        class="px-2 py-0.5 text-[10px] font-black bg-rose-100 text-rose-600 rounded-full uppercase">Cancelado</span>
                                @endif
                                @if (!$item->is_initial && !$item->is_final && !$item->is_canceled)
                                    <span
                                        class="px-2 py-0.5 text-[10px] font-black bg-slate-100 text-slate-400 rounded-full uppercase">Proceso</span>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-2 pt-2">
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
                </div>
            @empty
                <div class="bg-white p-8 rounded-xl border border-slate-200 text-center">
                    <i class="fas fa-truck-loading text-slate-200 text-3xl mb-3 block"></i>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">No hay estados configurados
                    </p>
                </div>
            @endforelse
        </div>

        <!-- Vista Escritorio (Tabla) -->
        <div class="datatable-container hidden md:block overflow-x-auto">
            <table class="datatable w-full">
                <thead class="datatable-thead">
                    <tr>
                        <th class="datatable-th">Nombre / Slug</th>
                        <th class="datatable-th">Color</th>
                        <th class="datatable-th">Propiedades</th>
                        <th class="datatable-th text-right">Gestión</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($states as $item)
                        <tr class="datatable-tr" wire:key="state-row-{{ $item->id }}">
                            <td class="datatable-td">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-slate-700">{{ $item->name }}</span>
                                    <span class="text-[11px] text-slate-400 font-mono">{{ $item->slug }}</span>
                                </div>
                            </td>
                            <td class="datatable-td">
                                <div class="flex items-center gap-2">
                                    <span class="w-4 h-4 rounded-full border border-slate-200 shadow-sm"
                                        style="background-color: {{ $item->color }};"></span>
                                    <span class="text-xs font-bold text-slate-600 font-mono">{{ $item->color }}</span>
                                </div>
                            </td>
                            <td class="datatable-td">
                                <div class="flex flex-wrap gap-1">
                                    @if ($item->is_initial)
                                        <span
                                            class="px-2 py-0.5 text-[10px] font-black bg-blue-100 text-blue-600 rounded-full uppercase">Inicial</span>
                                    @endif
                                    @if ($item->is_final)
                                        <span
                                            class="px-2 py-0.5 text-[10px] font-black bg-emerald-100 text-emerald-600 rounded-full uppercase">Final</span>
                                    @endif
                                    @if ($item->is_canceled)
                                        <span
                                            class="px-2 py-0.5 text-[10px] font-black bg-rose-100 text-rose-600 rounded-full uppercase">Cancelado</span>
                                    @endif
                                    @if (!$item->is_initial && !$item->is_final && !$item->is_canceled)
                                        <span
                                            class="px-2 py-0.5 text-[10px] font-black bg-slate-100 text-slate-400 rounded-full uppercase">Proceso</span>
                                    @endif
                                </div>
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
                            <td colspan="4" class="py-16 text-center">
                                <i class="fas fa-truck-loading text-slate-200 text-3xl mb-3 block"></i>
                                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">No hay estados
                                    configurados</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if (method_exists($states, 'links'))
            <div class="mt-4 px-2">
                {{ $states->links() }}
            </div>
        @endif
    </div>

    @if ($isOpen)
        <div class="modal-overlay">
            <div class="modal-backdrop" wire:click="closeModal"></div>

            <div class="modal-container max-w-lg">
                <div class="modal-header">
                    <div class="flex items-center gap-3">
                        <div class="modal-icon-box">
                            <i class="fas {{ $state_id ? 'fa-edit' : 'fa-plus' }}"></i>
                        </div>
                        <h3 class="modal-title">
                            {{ $state_id ? 'Editar Estado' : 'Nuevo Estado' }}
                        </h3>
                    </div>
                    <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="grid grid-cols-1 gap-4">

                        <div>
                            <label class="modal-label">Nombre del Estado</label>
                            <div class="modal-input-group">
                                <i class="fas fa-tag modal-input-icon"></i>
                                <input type="text" wire:model.live="name" class="modal-input"
                                    placeholder="Ej: Pendiente, En Camino, Entregado">
                            </div>
                            @error('name')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="modal-label">Slug</label>
                            <div class="modal-input-group">
                                <i class="fas fa-link modal-input-icon"></i>
                                <input type="text" wire:model="slug" class="modal-input"
                                    placeholder="ej-pendiente">
                            </div>
                            @error('slug')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="modal-label">Color Distintivo</label>
                            <div class="flex items-center gap-2">
                                <input type="color" wire:model="color"
                                    class="h-10 w-14 rounded border border-slate-200 cursor-pointer p-1">
                                <input type="text" wire:model="color" class="modal-input" placeholder="#6c757d">
                            </div>
                            @error('color')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="pt-2 border-t border-slate-100 flex flex-col gap-3">
                            <label class="text-xs font-bold text-slate-500 uppercase">Configuración de Flujo</label>

                            <label class="flex items-center gap-3 cursor-pointer group">
                                <div class="relative">
                                    <input type="checkbox" wire:model="is_initial" class="sr-only peer">
                                    <div
                                        class="w-10 h-5 bg-slate-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600">
                                    </div>
                                </div>
                                <span
                                    class="text-xs font-bold text-slate-600 uppercase group-hover:text-indigo-600 transition-colors">¿Es
                                    Estado Inicial?</span>
                            </label>
                            @error('is_initial')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror

                            <label class="flex items-center gap-3 cursor-pointer group">
                                <div class="relative">
                                    <input type="checkbox" wire:model="is_final" class="sr-only peer">
                                    <div
                                        class="w-10 h-5 bg-slate-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600">
                                    </div>
                                </div>
                                <span
                                    class="text-xs font-bold text-slate-600 uppercase group-hover:text-indigo-600 transition-colors">¿Es
                                    Estado Final (Completado)?</span>
                            </label>
                            @error('is_final')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror

                            <label class="flex items-center gap-3 cursor-pointer group">
                                <div class="relative">
                                    <input type="checkbox" wire:model="is_canceled" class="sr-only peer">
                                    <div
                                        class="w-10 h-5 bg-slate-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600">
                                    </div>
                                </div>
                                <span
                                    class="text-xs font-bold text-slate-600 uppercase group-hover:text-indigo-600 transition-colors">¿Es
                                    Estado Cancelado?</span>
                            </label>
                            @error('is_canceled')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button wire:click="closeModal" class="btn-modal-secondary">Cancelar</button>
                    <button wire:click="store" class="btn-modal-primary" wire:loading.attr="disabled">
                        <span class="relative z-10" wire:loading.remove wire:target="store">
                            {{ $state_id ? 'Actualizar' : 'Guardar' }}
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
                    title: '¿Eliminar Estado?',
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
