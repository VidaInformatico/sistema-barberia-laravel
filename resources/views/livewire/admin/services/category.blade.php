<div>
    <div class="page-container">
        <header class="page-header">
            <div class="flex items-center gap-3">
                <div class="header-icon">
                    <i class="fas fa-concierge-bell"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black tracking-tight text-slate-800 leading-none">Categorías de Servicios
                    </h2>
                    <p class="text-slate-400 text-[11px] font-bold uppercase tracking-wider mt-1">
                        Gestión y organización de los servicios ofrecidos
                    </p>
                </div>
            </div>

            <button wire:click="create" class="btn-primary">
                <i class="fas fa-plus"></i>
                Nueva Categoría
            </button>
        </header>

        <div class="toolbar-container">
            <div class="search-input-group relative w-full lg:flex-1">
                <input type="text" wire:model.live.debounce.500ms="search" class="search-input"
                    placeholder="Buscar por nombre o descripción...">
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

        <!-- Vista Móvil (Tarjetas Refactorizadas) -->
        <div class="block md:hidden space-y-3">
            @forelse ($categories as $item)
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm space-y-3"
                    wire:key="category-card-{{ $item->id }}">
                    <!-- Encabezado: Icono + Nombre + Estado -->
                    <div class="flex items-start justify-between border-b border-slate-100 pb-2.5">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-9 h-9 rounded-lg bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 shrink-0">
                                <i class="{{ $item->icon ?? 'fas fa-folder' }} text-sm"></i>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm font-black text-slate-800">{{ $item->name }}</span>
                                <span class="text-[10px] text-slate-400 font-semibold mt-0.5">
                                    Orden: <span class="badge-id">#{{ $item->sort_order }}</span>
                                </span>
                            </div>
                        </div>

                        <div>
                            @if ($item->is_active)
                                <span
                                    class="px-2.5 py-0.5 text-[10px] font-black bg-emerald-100 text-emerald-700 rounded-full uppercase">
                                    Activo
                                </span>
                            @else
                                <span
                                    class="px-2.5 py-0.5 text-[10px] font-black bg-slate-100 text-slate-400 rounded-full uppercase">
                                    Inactivo
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Descripción -->
                    <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-100 text-xs text-slate-600">
                        <span class="line-clamp-2 italic">
                            {{ $item->description ?? 'Sin descripción' }}
                        </span>
                    </div>

                    <!-- Acciones -->
                    <div class="flex items-center justify-end gap-2 pt-1 border-t border-slate-50">
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
                    <i class="fas fa-concierge-bell text-slate-200 text-3xl mb-3 block"></i>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">No hay categorías configuradas
                    </p>
                </div>
            @endforelse
        </div>

        <!-- Vista Escritorio (Tabla) -->
        <div class="datatable-container hidden md:block overflow-x-auto">
            <table class="datatable w-full">
                <thead class="datatable-thead">
                    <tr>
                        <th class="datatable-th w-20">Orden</th>
                        <th class="datatable-th w-16 text-center">Icono</th>
                        <th class="datatable-th">Nombre / Descripción</th>
                        <th class="datatable-th">Estado</th>
                        <th class="datatable-th text-right w-28">Gestión</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-50">
                    @forelse ($categories as $item)
                        <tr class="datatable-tr" wire:key="category-row-{{ $item->id }}">
                            <td class="datatable-td">
                                <span class="badge-id">#{{ $item->sort_order }}</span>
                            </td>
                            <td class="datatable-td text-center">
                                <div
                                    class="w-8 h-8 rounded-lg bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 mx-auto">
                                    <i class="{{ $item->icon ?? 'fas fa-folder' }} text-sm"></i>
                                </div>
                            </td>
                            <td class="datatable-td">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-slate-700">{{ $item->name }}</span>
                                    <span
                                        class="text-xs text-slate-400 line-clamp-1">{{ $item->description ?? 'Sin descripción' }}</span>
                                </div>
                            </td>
                            <td class="datatable-td">
                                @if ($item->is_active)
                                    <span
                                        class="px-2.5 py-0.5 text-[10px] font-black bg-emerald-100 text-emerald-700 rounded-full uppercase">
                                        Activo
                                    </span>
                                @else
                                    <span
                                        class="px-2.5 py-0.5 text-[10px] font-black bg-slate-100 text-slate-400 rounded-full uppercase">
                                        Inactivo
                                    </span>
                                @endif
                            </td>
                            <td class="datatable-td text-right">
                                <div class="action-group inline-flex">
                                    <button wire:click="edit({{ $item->id }})"
                                        class="btn-action bg-[#fff8e1] hover:bg-[#ffecb3] text-[#f57f17] border-r"
                                        title="Editar">
                                        <i class="fas fa-edit text-xs"></i>
                                    </button>

                                    <button wire:click="deleteConfirm({{ $item->id }})"
                                        class="btn-action bg-[#ffebee] hover:bg-[#ffcdd2] text-[#c62828]"
                                        title="Eliminar">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-16 text-center">
                                <i class="fas fa-concierge-bell text-slate-200 text-3xl mb-3 block"></i>
                                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">
                                    No hay categorías configuradas
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if (method_exists($categories, 'links'))
            <div class="mt-4 px-2">
                {{ $categories->links() }}
            </div>
        @endif
    </div>

    @if ($isOpen)
        <div class="modal-overlay">
            <div class="modal-backdrop" wire:click="closeModal"></div>

            <div class="modal-container max-w-2xl">
                <div class="modal-header">
                    <div class="flex items-center gap-3">
                        <div class="modal-icon-box">
                            <i class="fas {{ $category_id ? 'fa-edit' : 'fa-plus' }}"></i>
                        </div>
                        <h3 class="modal-title">
                            {{ $category_id ? 'Editar Categoría' : 'Nueva Categoría' }}
                        </h3>
                    </div>
                    <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div class="col-span-2">
                            <label class="modal-label">Nombre de la Categoría</label>
                            <div class="modal-input-group">
                                <i class="fas fa-heading modal-input-icon"></i>
                                <input type="text" wire:model="name" class="modal-input"
                                    placeholder="Ej: Consultorías, Mantenimiento, etc.">
                            </div>
                            @error('name')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="modal-label">Icono (Clase FontAwesome)</label>
                            <div class="modal-input-group">
                                <i class="fas fa-icons modal-input-icon"></i>
                                <input type="text" wire:model="icon" class="modal-input"
                                    placeholder="Ej: fas fa-tools">
                            </div>
                            @error('icon')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="modal-label">Orden de Visualización</label>
                            <div class="modal-input-group">
                                <i class="fas fa-sort-numeric-down modal-input-icon"></i>
                                <input type="number" wire:model="sort_order" class="modal-input">
                            </div>
                            @error('sort_order')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-span-2">
                            <label class="modal-label">Descripción</label>
                            <textarea wire:model="description" class="modal-input min-h-[100px]"
                                placeholder="Breve descripción de los servicios que abarca..."></textarea>
                            @error('description')
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
                                    class="text-xs font-bold text-slate-600 uppercase group-hover:text-indigo-600 transition-colors">¿Categoría
                                    activa?</span>
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
                            {{ $category_id ? 'Actualizar' : 'Guardar' }}
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
</div>
