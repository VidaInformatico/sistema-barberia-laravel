<div>
    <div class="page-container">
        <header class="page-header">
            <div class="flex items-center gap-3">
                <div class="header-icon">
                    <i class="fas fa-cut"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black tracking-tight text-slate-800 leading-none">Servicios</h2>
                    <p class="text-slate-400 text-[11px] font-bold uppercase tracking-wider mt-1">
                        Gestión y configuración de los servicios ofrecidos
                    </p>
                </div>
            </div>

            <button wire:click="create" class="btn-primary">
                <i class="fas fa-plus"></i>
                Nuevo Servicio
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

        <div class="block md:hidden space-y-3">
            @forelse ($services as $item)
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm space-y-3"
                    wire:key="service-card-{{ $item->id }}">
                    <div class="flex items-start justify-between border-b border-slate-100 pb-2.5">
                        <div class="flex flex-col">
                            <span class="text-sm font-black text-slate-800">{{ $item->name }}</span>
                            <div class="flex items-center gap-1.5 mt-1">
                                <div
                                    class="w-4 h-4 rounded bg-slate-100 flex items-center justify-center text-slate-500 shrink-0">
                                    <i class="{{ $item->category->icon ?? 'fas fa-folder' }} text-[10px]"></i>
                                </div>
                                <span class="text-[11px] font-bold text-slate-500">
                                    {{ $item->category->name ?? 'Sin categoría' }}
                                </span>
                                <span class="text-[10px] text-slate-300">•</span>
                                <span class="text-[10px] text-slate-400 font-semibold">
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
                    <div class="text-xs text-slate-600">
                        <p class="line-clamp-2 italic">
                            {{ $item->description ?? 'Sin descripción' }}
                        </p>
                    </div>

                    <!-- Información Clave: Duración y Precio -->
                    <div
                        class="bg-slate-50 p-2.5 rounded-lg border border-slate-100 flex items-center justify-between text-xs">
                        <div class="flex items-center text-slate-600 font-bold">
                            <i class="far fa-clock text-slate-400 mr-1.5"></i>
                            <span>{{ $item->formatted_duration }}</span>
                        </div>
                        <div class="text-right">
                            <span
                                class="text-[10px] text-slate-400 font-bold block uppercase leading-none mb-0.5">Precio</span>
                            <span class="text-sm font-black text-indigo-600">
                                {{ $item->formatted_price }}
                            </span>
                        </div>
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
                    <i class="fas fa-cut text-slate-200 text-3xl mb-3 block"></i>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">No hay servicios configurados
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
                        <th class="datatable-th">Categoría</th>
                        <th class="datatable-th">Nombre / Descripción</th>
                        <th class="datatable-th">Duración</th>
                        <th class="datatable-th">Precio</th>
                        <th class="datatable-th">Estado</th>
                        <th class="datatable-th text-right w-28">Gestión</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-50">
                    @forelse ($services as $item)
                        <tr class="datatable-tr" wire:key="service-row-{{ $item->id }}">
                            <td class="datatable-td">
                                <span class="badge-id">#{{ $item->sort_order }}</span>
                            </td>
                            <td class="datatable-td">
                                <div class="flex items-center gap-2">
                                    <div
                                        class="w-6 h-6 rounded bg-slate-100 flex items-center justify-center text-slate-500 shrink-0">
                                        <i class="{{ $item->category->icon ?? 'fas fa-folder' }} text-xs"></i>
                                    </div>
                                    <span class="text-xs font-bold text-slate-600">
                                        {{ $item->category->name ?? 'Sin categoría' }}
                                    </span>
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
                                <span class="text-xs font-bold text-slate-600">
                                    <i class="far fa-clock mr-1 text-slate-400"></i>
                                    {{ $item->formatted_duration }}
                                </span>
                            </td>
                            <td class="datatable-td">
                                <span class="text-sm font-black text-indigo-600">
                                    {{ $item->formatted_price }}
                                </span>
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
                            <td colspan="7" class="py-16 text-center">
                                <i class="fas fa-cut text-slate-200 text-3xl mb-3 block"></i>
                                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">
                                    No hay servicios configurados
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if (method_exists($services, 'links'))
            <div class="mt-4 px-2">
                {{ $services->links() }}
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
                            <i class="fas {{ $service_id ? 'fa-edit' : 'fa-plus' }}"></i>
                        </div>
                        <h3 class="modal-title">
                            {{ $service_id ? 'Editar Servicio' : 'Nuevo Servicio' }}
                        </h3>
                    </div>
                    <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div class="col-span-2">
                            <label class="modal-label">Categoría del Servicio</label>
                            <div class="modal-input-group">
                                <i class="fas fa-tags modal-input-icon"></i>
                                <select wire:model="category_id" class="modal-input">
                                    <option value="">Seleccione una categoría...</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('category_id')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-span-2">
                            <label class="modal-label">Nombre del Servicio</label>
                            <div class="modal-input-group">
                                <i class="fas fa-heading modal-input-icon"></i>
                                <input type="text" wire:model="name" class="modal-input"
                                    placeholder="Ej: Corte de Cabello Clásico, Perfilado de Barba">
                            </div>
                            @error('name')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="modal-label">Duración (Minutos)</label>
                            <div class="modal-input-group">
                                <i class="far fa-clock modal-input-icon"></i>
                                <input type="number" wire:model="duration_minutes" class="modal-input"
                                    placeholder="Ej: 30">
                            </div>
                            @error('duration_minutes')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="modal-label">Precio ({{ $company->curency_code }})</label>
                            <div class="modal-input-group">
                                <i class="fas fa-dollar-sign modal-input-icon"></i>
                                <input type="number" step="0.01" wire:model="price" class="modal-input"
                                    placeholder="Ej: 25.00">
                            </div>
                            @error('price')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-span-2">
                            <label class="modal-label">URL de la Imagen (Opcional)</label>
                            <div class="modal-input-group">
                                <i class="fas fa-image modal-input-icon"></i>
                                <input type="text" wire:model="image" class="modal-input"
                                    placeholder="Ruta de la imagen o clase de icono alternativo">
                            </div>
                            @error('image')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-span-2">
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
                                placeholder="Breve descripción del servicio prestado y detalles..."></textarea>
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
                                    class="text-xs font-bold text-slate-600 uppercase group-hover:text-indigo-600 transition-colors">¿Servicio
                                    activo?</span>
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
                            {{ $service_id ? 'Actualizar' : 'Guardar' }}
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
