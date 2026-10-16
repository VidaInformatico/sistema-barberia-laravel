<div>
    <div class="page-container">
        <header class="page-header">
            <div class="flex items-center gap-3">
                <div class="header-icon">
                    <i class="fas fa-images"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black tracking-tight text-slate-800 leading-none">Slides / Carrusel</h2>
                    <p class="text-slate-400 text-[11px] font-bold uppercase tracking-wider mt-1">
                        Gestión de imágenes y promociones principales
                    </p>
                </div>
            </div>

            <button wire:click="create" class="btn-primary">
                <i class="fas fa-plus"></i>
                Nuevo Slide
            </button>
        </header>

        <div class="toolbar-container">
            <div class="search-input-group relative w-full lg:flex-1">
                <input type="text" wire:model.live.debounce.500ms="search" class="search-input"
                    placeholder="Buscar por título o subtítulo...">
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
            @forelse ($slides as $item)
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm space-y-3"
                    wire:key="slide-card-{{ $item->id }}">
                    <!-- Encabezado: Título + Estado -->
                    <div class="flex items-start justify-between border-b border-slate-100 pb-2.5">
                        <div class="flex flex-col">
                            <span class="text-sm font-black text-slate-800">{{ $item->title }}</span>
                            <span class="text-[10px] text-slate-400 font-semibold mt-0.5">
                                Orden: <span class="badge-id">#{{ $item->order }}</span>
                            </span>
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

                    <!-- Vista Previa de Imagen y Subtítulo -->
                    <div class="flex items-center gap-3 bg-slate-50 p-2.5 rounded-lg border border-slate-100">
                        <div
                            class="w-20 h-12 rounded-lg overflow-hidden border border-slate-200 shadow-sm bg-slate-100 shrink-0">
                            <img src="{{ asset('storage/' . $item->image_url) }}" class="w-full h-full object-cover"
                                alt="{{ $item->title }}">
                        </div>
                        <div class="flex flex-col justify-center">
                            <span class="text-[10px] text-slate-400 font-bold uppercase">Subtítulo</span>
                            <span class="text-xs text-indigo-600 font-bold uppercase line-clamp-1">
                                {{ $item->subtitle ?? 'Sin subtítulo' }}
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
                    <i class="fas fa-images text-slate-200 text-3xl mb-3 block"></i>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">No hay slides configurados</p>
                </div>
            @endforelse
        </div>

        <!-- Vista Escritorio (Tabla) -->
        <div class="datatable-container hidden md:block overflow-x-auto">
            <table class="datatable w-full">
                <thead class="datatable-thead">
                    <tr>
                        <th class="datatable-th w-20">Orden</th>
                        <th class="datatable-th w-28">Imagen</th>
                        <th class="datatable-th">Información</th>
                        <th class="datatable-th">Estado</th>
                        <th class="datatable-th text-right w-28">Gestión</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-50">
                    @forelse ($slides as $item)
                        <tr class="datatable-tr" wire:key="slide-row-{{ $item->id }}">
                            <td class="datatable-td">
                                <span class="badge-id">#{{ $item->order }}</span>
                            </td>
                            <td class="datatable-td">
                                <div
                                    class="w-20 h-12 rounded-lg overflow-hidden border border-slate-200 shadow-sm bg-slate-100">
                                    <img src="{{ asset('storage/' . $item->image_url) }}"
                                        class="w-full h-full object-cover" alt="{{ $item->title }}">
                                </div>
                            </td>
                            <td class="datatable-td">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-slate-700">{{ $item->title }}</span>
                                    <span
                                        class="text-[11px] text-indigo-500 font-bold uppercase">{{ $item->subtitle ?? 'Sin subtítulo' }}</span>
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
                                <i class="fas fa-images text-slate-200 text-3xl mb-3 block"></i>
                                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">
                                    No hay slides configurados
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if (method_exists($slides, 'links'))
            <div class="mt-4 px-2">
                {{ $slides->links() }}
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
                            <i class="fas {{ $slide_id ? 'fa-edit' : 'fa-plus' }}"></i>
                        </div>
                        <h3 class="modal-title">
                            {{ $slide_id ? 'Editar Slide' : 'Nuevo Slide' }}
                        </h3>
                    </div>
                    <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div class="col-span-2">
                            <label class="modal-label">Título Principal</label>
                            <div class="modal-input-group">
                                <i class="fas fa-heading modal-input-icon"></i>
                                <input type="text" wire:model="title" class="modal-input"
                                    placeholder="Ej: Nueva Etapa Disponible">
                            </div>
                            @error('title')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="modal-label">Subtítulo</label>
                            <div class="modal-input-group">
                                <i class="fas fa-font modal-input-icon"></i>
                                <input type="text" wire:model="subtitle" class="modal-input"
                                    placeholder="Ej: SubTitulo">
                            </div>
                            @error('subtitle')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="modal-label">Orden de Visualización</label>
                            <div class="modal-input-group">
                                <i class="fas fa-sort-numeric-down modal-input-icon"></i>
                                <input type="number" wire:model="order" class="modal-input">
                            </div>
                            @error('order')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-span-2">
                            <label class="modal-label">Imagen del Slide</label>
                            <div class="mt-1 flex flex-col gap-3">
                                <div class="flex items-center justify-center w-full">
                                    <label
                                        class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-slate-300 rounded-xl cursor-pointer bg-slate-50 hover:bg-slate-100 transition-colors relative overflow-hidden">
                                        @if ($image)
                                            <img src="{{ $image->temporaryUrl() }}"
                                                class="absolute inset-0 w-full h-full object-cover opacity-40">
                                        @elseif ($current_image)
                                            <img src="{{ asset('storage/' . $current_image) }}"
                                                class="absolute inset-0 w-full h-full object-cover opacity-40">
                                        @endif
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6 relative z-10">
                                            <i class="fas fa-cloud-upload-alt text-2xl text-slate-400 mb-2"></i>
                                            <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Subir
                                                Imagen</p>
                                        </div>
                                        <input type="file" wire:model="image" class="hidden" accept="image/*" />
                                    </label>
                                </div>
                                <div wire:loading wire:target="image"
                                    class="text-[10px] text-indigo-600 font-bold animate-pulse text-center">
                                    CARGANDO IMAGEN...
                                </div>
                                @error('image')
                                    <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-span-2">
                            <label class="modal-label">Texto / Descripción</label>
                            <textarea wire:model="text" class="modal-input min-h-[80px]" placeholder="Breve descripción para el slide..."></textarea>
                            @error('text')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-span-2 border-b border-slate-100 my-2 pb-1">
                            <h4 class="text-xs font-black text-slate-400 uppercase tracking-wider">Configuración de
                                Botón 1 (Obligatorio)</h4>
                        </div>

                        <div>
                            <label class="modal-label">Texto Botón 1</label>
                            <div class="modal-input-group">
                                <i class="fas fa-font modal-input-icon"></i>
                                <input type="text" wire:model="btn1_text" class="modal-input"
                                    placeholder="Ej: Ver más">
                            </div>
                            @error('btn1_text')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="modal-label">URL Botón 1</label>
                            <div class="modal-input-group">
                                <i class="fas fa-link modal-input-icon"></i>
                                <input type="text" wire:model="btn1_url" class="modal-input"
                                    placeholder="https://...">
                            </div>
                            @error('btn1_url')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-span-2">
                            <label class="modal-label">Icono Botón 1 (Clase FontAwesome)</label>
                            <div class="modal-input-group">
                                <i class="fas fa-icons modal-input-icon"></i>
                                <input type="text" wire:model="btn1_icon" class="modal-input"
                                    placeholder="Ej: fas fa-arrow-right">
                            </div>
                            @error('btn1_icon')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-span-2 border-b border-slate-100 my-2 pb-1">
                            <h4 class="text-xs font-black text-slate-400 uppercase tracking-wider">Configuración de
                                Botón 2 (Opcional)</h4>
                        </div>

                        <div>
                            <label class="modal-label">Texto Botón 2</label>
                            <div class="modal-input-group">
                                <i class="fas fa-font modal-input-icon"></i>
                                <input type="text" wire:model="btn2_text" class="modal-input"
                                    placeholder="Ej: Contactar">
                            </div>
                            @error('btn2_text')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="modal-label">URL Botón 2</label>
                            <div class="modal-input-group">
                                <i class="fas fa-link modal-input-icon"></i>
                                <input type="text" wire:model="btn2_url" class="modal-input"
                                    placeholder="https://...">
                            </div>
                            @error('btn2_url')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-span-2">
                            <label class="modal-label">Icono Botón 2 (Clase FontAwesome)</label>
                            <div class="modal-input-group">
                                <i class="fas fa-icons modal-input-icon"></i>
                                <input type="text" wire:model="btn2_icon" class="modal-input"
                                    placeholder="Ej: fab fa-whatsapp">
                            </div>
                            @error('btn2_icon')
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
                                    class="text-xs font-bold text-slate-600 uppercase group-hover:text-indigo-600 transition-colors">¿Slide
                                    visible en la web?</span>
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
                            {{ $slide_id ? 'Actualizar' : 'Guardar' }}
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
                    title: '¿Eliminar Slide?',
                    text: "Esta acción no se puede deshacer y se borrará la imagen.",
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
