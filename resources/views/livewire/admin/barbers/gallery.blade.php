<div>
    <div class="page-container">
        <header class="page-header">
            <div class="flex items-center gap-3">
                <div class="header-icon">
                    <i class="fas fa-images"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black tracking-tight text-slate-800 leading-none">Galería de Trabajos</h2>
                    <p class="text-slate-400 text-[11px] font-bold uppercase tracking-wider mt-1">
                        Catálogo visual de cortes y acabados realizados por los barberos
                    </p>
                </div>
            </div>

            <button wire:click="create" class="btn-primary">
                <i class="fas fa-camera"></i>
                Subir Foto
            </button>
        </header>

        <div class="toolbar-container">
            <div class="search-input-group relative w-full lg:flex-1">
                <input type="text" wire:model.live.debounce.500ms="search" class="search-input"
                    placeholder="Buscar por título, descripción o barbero...">
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
            @forelse ($photos as $item)
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm space-y-3"
                    wire:key="photo-card-{{ $item->id }}">
                    <!-- Encabezado: Título + Estado Destacada -->
                    <div class="flex items-start justify-between border-b border-slate-100 pb-2.5">
                        <div class="flex flex-col">
                            <span class="text-sm font-black text-slate-800">{{ $item->title }}</span>
                            <span class="text-[10px] text-slate-400 font-semibold mt-0.5">
                                Orden: <span class="badge-id">#{{ $item->sort_order }}</span>
                            </span>
                        </div>

                        <div>
                            @if ($item->is_featured)
                                <span
                                    class="px-2.5 py-0.5 text-[10px] font-black bg-indigo-100 text-indigo-700 rounded-full uppercase flex items-center gap-1">
                                    <i class="fas fa-star text-[9px]"></i> Destacada
                                </span>
                            @else
                                <span
                                    class="px-2.5 py-0.5 text-[10px] font-black bg-slate-100 text-slate-400 rounded-full uppercase">
                                    Normal
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Vista previa de foto y detalles -->
                    <div class="flex items-center gap-3 bg-slate-50 p-2.5 rounded-lg border border-slate-100">
                        <div
                            class="relative w-16 h-16 rounded-lg overflow-hidden border border-slate-200 bg-slate-100 shadow-sm shrink-0">
                            <img src="{{ $item->image_url }}" alt="{{ $item->title }}"
                                class="w-full h-full object-cover">
                        </div>
                        <div class="flex flex-col justify-center min-w-0">
                            <span class="text-[10px] text-slate-400 font-bold uppercase">Barbero</span>
                            <span class="text-xs font-bold text-slate-700 truncate">
                                {{ $item->barber->full_name ?? 'Sin asignar' }}
                            </span>
                            <p class="text-[11px] text-slate-500 line-clamp-1 mt-0.5 italic">
                                {{ $item->description ?? 'Sin descripción' }}
                            </p>
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
                    <i class="fas fa-images text-slate-200 text-3xl mb-3 block"></i>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">No hay fotos en la galería de
                        trabajos</p>
                </div>
            @endforelse
        </div>

        <!-- Vista Escritorio (Tabla) -->
        <div class="datatable-container hidden md:block overflow-x-auto">
            <table class="datatable w-full">
                <thead class="datatable-thead">
                    <tr>
                        <th class="datatable-th w-24">Foto / Trabajo</th>
                        <th class="datatable-th">Detalles</th>
                        <th class="datatable-th">Barbero</th>
                        <th class="datatable-th w-20">Orden</th>
                        <th class="datatable-th">Destacada</th>
                        <th class="datatable-th text-right w-28">Gestión</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-50">
                    @forelse ($photos as $item)
                        <tr class="datatable-tr" wire:key="photo-row-{{ $item->id }}">
                            <td class="datatable-td">
                                <div
                                    class="relative w-16 h-16 rounded-lg overflow-hidden border border-slate-200 bg-slate-100 shadow-sm">
                                    <img src="{{ $item->image_url }}" alt="{{ $item->title }}"
                                        class="w-full h-full object-cover">
                                </div>
                            </td>
                            <td class="datatable-td">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-slate-700">{{ $item->title }}</span>
                                    <span class="text-xs text-slate-400 max-w-xs truncate mt-0.5">
                                        {{ $item->description ?? 'Sin descripción' }}
                                    </span>
                                </div>
                            </td>
                            <td class="datatable-td">
                                <span class="text-xs font-bold text-slate-600">
                                    {{ $item->barber->full_name ?? 'Sin asignar' }}
                                </span>
                            </td>
                            <td class="datatable-td">
                                <span class="badge-id">
                                    #{{ $item->sort_order }}
                                </span>
                            </td>
                            <td class="datatable-td">
                                @if ($item->is_featured)
                                    <span
                                        class="px-2.5 py-1 text-[10px] font-black bg-indigo-100 text-indigo-700 rounded-full uppercase inline-flex items-center gap-1">
                                        <i class="fas fa-star text-[9px]"></i> Destacada
                                    </span>
                                @else
                                    <span
                                        class="px-2.5 py-1 text-[10px] font-black bg-slate-100 text-slate-400 rounded-full uppercase">
                                        Normal
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
                            <td colspan="6" class="py-16 text-center">
                                <i class="fas fa-images text-slate-200 text-3xl mb-3 block"></i>
                                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">
                                    No hay fotos en la galería de trabajos
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if (method_exists($photos, 'links'))
            <div class="mt-4 px-2">
                {{ $photos->links() }}
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
                            <i class="fas {{ $gallery_photo_id ? 'fa-edit' : 'fa-camera' }}"></i>
                        </div>
                        <h3 class="modal-title">
                            {{ $gallery_photo_id ? 'Editar Foto de Galería' : 'Subir Nueva Imagen' }}
                        </h3>
                    </div>
                    <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div class="col-span-2">
                            <label class="modal-label">Barbero Ejecutor del Trabajo</label>
                            <div class="modal-input-group">
                                <i class="fas fa-user-tie modal-input-icon"></i>
                                <select wire:model="barber_id" class="modal-input">
                                    <option value="">Seleccione al barbero responsable...</option>
                                    @foreach ($barbers as $barber)
                                        <option value="{{ $barber->id }}">{{ $barber->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('barber_id')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="modal-label">Título del Trabajo / Estilo</label>
                            <div class="modal-input-group">
                                <i class="fas fa-heading modal-input-icon"></i>
                                <input type="text" wire:model="title" class="modal-input"
                                    placeholder="Ej: Mid Fade con barba alineada">
                            </div>
                            @error('title')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="modal-label">Orden de Visualización</label>
                            <div class="modal-input-group">
                                <i class="fas fa-sort-numeric-down modal-input-icon"></i>
                                <input type="number" wire:model="sort_order" class="modal-input"
                                    placeholder="Ej: 0, 1, 2">
                            </div>
                            @error('sort_order')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-span-2">
                            <label class="modal-label">Descripción del Detalle</label>
                            <textarea wire:model="description" class="modal-input min-h-[80px]"
                                placeholder="Breve nota sobre técnicas, productos usados u observaciones..."></textarea>
                            @error('description')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-span-2">
                            <label class="modal-label">Archivo de Imagen (PNG, JPG, JPEG - Máx. 3MB)</label>
                            <div
                                class="mt-1 flex flex-col items-center justify-center border-2 border-dashed border-slate-200 rounded-xl p-4 bg-slate-50/50 hover:bg-slate-50 transition-colors relative">
                                <input type="file" wire:model="image"
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                                <div class="text-center" wire:loading.remove wire:target="image">
                                    <i class="fas fa-cloud-upload-alt text-2xl text-slate-400 mb-2"></i>
                                    <p class="text-xs font-bold text-slate-600">Haz clic o arrastra para seleccionar
                                        una imagen</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5">El archivo se procesará
                                        automáticamente</p>
                                </div>

                                <div class="text-center" wire:loading wire:target="image">
                                    <i class="fas fa-spinner fa-spin text-2xl text-indigo-500 mb-2"></i>
                                    <p class="text-xs font-bold text-indigo-600">Procesando imagen...</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5">Por favor espera un momento</p>
                                </div>
                            </div>
                            @error('image')
                                <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div
                            class="col-span-2 flex gap-4 justify-center items-center mt-2 bg-slate-50 p-3 rounded-xl border border-slate-100">
                            @if ($existing_image && !$image)
                                <div class="text-center" wire:loading.remove wire:target="image">
                                    <span class="text-[10px] font-black text-slate-400 uppercase block mb-1">Imagen
                                        Actual</span>
                                    <img src="{{ $existing_image }}"
                                        class="w-24 h-24 object-cover rounded-lg border border-slate-200 shadow-sm">
                                </div>
                            @endif

                            @if ($image)
                                <div class="text-center" wire:loading.remove wire:target="image">
                                    <span class="text-[10px] font-black text-indigo-500 uppercase block mb-1">Nueva
                                        Previa</span>
                                    <img src="{{ $image->temporaryUrl() }}"
                                        class="w-24 h-24 object-cover rounded-lg border-2 border-indigo-500 shadow-sm">
                                </div>
                            @endif
                        </div>

                        <div class="col-span-2 py-1">
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <div class="relative">
                                    <input type="checkbox" wire:model="is_featured" class="sr-only peer">
                                    <div
                                        class="w-10 h-5 bg-slate-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600">
                                    </div>
                                </div>
                                <span
                                    class="text-xs font-bold text-slate-600 uppercase group-hover:text-indigo-600 transition-colors">¿Destacar
                                    esta foto en el portafolio principal?</span>
                            </label>
                            @error('is_featured')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button wire:click="closeModal" class="btn-modal-secondary">Cancelar</button>
                    <button wire:click="store" class="btn-modal-primary" wire:loading.attr="disabled">
                        <span class="relative z-10" wire:loading.remove wire:target="store">
                            {{ $gallery_photo_id ? 'Actualizar' : 'Guardar' }}
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
                    title: '¿Eliminar Fotografía?',
                    text: "Esta foto ya no aparecerá en el portafolio público.",
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
