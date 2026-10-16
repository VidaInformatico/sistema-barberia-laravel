<div class="min-h-screen antialiased">
    <div class="page-container">
        <header class="page-header">
            <div class="flex items-center gap-3">
                <div class="header-icon">
                    <i class="fas fa-store"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black tracking-tight text-slate-800 leading-none">Sucursales</h2>
                    <p class="text-slate-400 text-[11px] font-bold uppercase tracking-wider mt-1">
                        Gestión de las sedes de la empresa y sus estados
                    </p>
                </div>
            </div>

            <button wire:click="create" class="btn-primary">
                <i class="fas fa-plus"></i>
                Nueva Sucursal
            </button>
        </header>

        <div class="toolbar-container">
            <div class="search-input-group relative w-full lg:flex-1">
                <input type="text" wire:model.live.debounce.500ms="search" class="search-input"
                    placeholder="Buscar sucursal por nombre, correo, teléfono o dirección...">
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
            @forelse ($branches as $branch)
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm space-y-3"
                    wire:key="branch-card-{{ $branch->id }}">
                    <!-- Encabezado: Icono + Nombre + Estado -->
                    <div class="flex items-start justify-between border-b border-slate-100 pb-2.5">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-9 h-9 rounded-lg bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 shrink-0">
                                <i class="fas fa-store text-sm"></i>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm font-black text-slate-800">{{ $branch->name }}</span>
                                <span class="text-[10px] text-slate-400 font-semibold mt-0.5">
                                    ID: <span class="badge-id">#{{ $branch->id }}</span>
                                </span>
                            </div>
                        </div>

                        <div>
                            @if ($branch->active)
                                <span
                                    class="px-2.5 py-0.5 text-[10px] font-black bg-emerald-100 text-emerald-700 rounded-full uppercase">
                                    Activa
                                </span>
                            @else
                                <span
                                    class="px-2.5 py-0.5 text-[10px] font-black bg-slate-100 text-slate-400 rounded-full uppercase">
                                    Inactiva
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Información de contacto y dirección -->
                    <div
                        class="space-y-1.5 bg-slate-50 p-2.5 rounded-lg border border-slate-100 text-xs text-slate-600">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-envelope text-slate-300 w-4 text-center"></i>
                            <span class="truncate font-medium">{{ $branch->email ?: 'Sin correo electrónico' }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-phone text-slate-300 w-4 text-center"></i>
                            <span class="font-medium">{{ $branch->phone ?: 'Sin teléfono' }}</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <i class="fas fa-map-marker-alt text-slate-300 w-4 text-center mt-0.5"></i>
                            <span
                                class="leading-relaxed font-medium">{{ $branch->address ?: 'Sin dirección registrada' }}</span>
                        </div>
                    </div>

                    {{-- SECCIÓN DE IMPRESORAS (Si aplica) --}}
                    @if ($branch->direct_printing)
                        <div class="bg-slate-50 p-2 rounded-lg border border-slate-100 flex items-center gap-2">
                            <i class="fas fa-print text-slate-400 text-xs"></i>
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Impresión
                                directa habilitada</span>
                        </div>
                    @endif

                    <!-- Acciones -->
                    <div class="flex items-center justify-end gap-2 pt-1 border-t border-slate-50">
                        <button wire:click="edit({{ $branch->id }})"
                            class="p-2 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-600 transition-colors"
                            title="Editar">
                            <i class="fas fa-edit text-sm"></i>
                        </button>

                        <button wire:click="deleteConfirm({{ $branch->id }})"
                            class="p-2 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 transition-colors"
                            title="Eliminar">
                            <i class="fas fa-trash-alt text-sm"></i>
                        </button>
                    </div>
                </div>
            @empty
                <div class="bg-white p-8 rounded-xl border border-slate-200 text-center">
                    <i class="fas fa-store text-slate-200 text-3xl mb-3 block"></i>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">No hay sucursales configuradas
                    </p>
                </div>
            @endforelse
        </div>

        <!-- Vista Escritorio (Tabla) -->
        <div class="datatable-container hidden md:block overflow-x-auto">
            <table class="datatable w-full">
                <thead class="datatable-thead">
                    <tr>
                        <th class="datatable-th w-16 text-center">Icono</th>
                        <th class="datatable-th">Sucursal / Contacto</th>
                        <th class="datatable-th">Dirección</th>
                        <th class="datatable-th">Estado</th>
                        <th class="datatable-th text-right w-28">Gestión</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-50">
                    @forelse ($branches as $branch)
                        <tr class="datatable-tr" wire:key="branch-row-{{ $branch->id }}">
                            <td class="datatable-td text-center">
                                <div
                                    class="w-8 h-8 rounded-lg bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 mx-auto">
                                    <i class="fas fa-store text-sm"></i>
                                </div>
                            </td>
                            <td class="datatable-td">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-slate-700">{{ $branch->name }}</span>
                                    <div class="flex items-center gap-3 text-xs text-slate-400 mt-0.5">
                                        <span><i
                                                class="fas fa-envelope mr-1"></i>{{ $branch->email ?: 'Sin correo' }}</span>
                                        <span>•</span>
                                        <span><i
                                                class="fas fa-phone mr-1"></i>{{ $branch->phone ?: 'Sin teléfono' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="datatable-td">
                                <span class="text-xs text-slate-600 line-clamp-1 font-medium">
                                    {{ $branch->address ?: 'Sin dirección registrada' }}
                                </span>
                            </td>
                            <td class="datatable-td">
                                @if ($branch->active)
                                    <span
                                        class="px-2.5 py-0.5 text-[10px] font-black bg-emerald-100 text-emerald-700 rounded-full uppercase">
                                        Activa
                                    </span>
                                @else
                                    <span
                                        class="px-2.5 py-0.5 text-[10px] font-black bg-slate-100 text-slate-400 rounded-full uppercase">
                                        Inactiva
                                    </span>
                                @endif
                            </td>
                            <td class="datatable-td text-right">
                                <div class="action-group inline-flex">
                                    <button wire:click="edit({{ $branch->id }})"
                                        class="btn-action bg-[#fff8e1] hover:bg-[#ffecb3] text-[#f57f17] border-r"
                                        title="Editar">
                                        <i class="fas fa-edit text-xs"></i>
                                    </button>

                                    <button wire:click="deleteConfirm({{ $branch->id }})"
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
                                <i class="fas fa-store text-slate-200 text-3xl mb-3 block"></i>
                                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">
                                    No hay sucursales configuradas
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if (method_exists($branches, 'links'))
            <div class="mt-4 px-2">
                {{ $branches->links() }}
            </div>
        @endif
    </div>

    @if ($isOpen)
        <div class="modal-overlay">
            <div class="modal-backdrop" wire:click="closeModal"></div>

            <div class="modal-container max-w-xl">
                <div class="modal-header">
                    <div class="flex items-center gap-3">
                        <div class="modal-icon-box">
                            <i class="fas {{ $branch_id ? 'fa-edit' : 'fa-store' }}"></i>
                        </div>
                        <h3 class="modal-title">
                            {{ $branch_id ? 'Editar Sucursal' : 'Nueva Sucursal' }}
                        </h3>
                    </div>
                    <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                
                        <div class="col-span-2">
                            <label class="modal-label">Nombre de la Sucursal</label>
                            <div class="modal-input-group">
                                <i class="fas fa-store modal-input-icon"></i>
                                <input type="text" wire:model="name" class="modal-input"
                                    placeholder="Ej: Sucursal Central, Sede Norte, etc.">
                            </div>
                            @error('name')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>
                
                        <div>
                            <label class="modal-label">Teléfono</label>
                            <div class="modal-input-group">
                                <i class="fas fa-phone modal-input-icon"></i>
                                <input type="text" wire:model="phone" class="modal-input"
                                    placeholder="Ej: 999 999 999">
                            </div>
                            @error('phone')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>
                
                        <div>
                            <label class="modal-label">Correo Electrónico</label>
                            <div class="modal-input-group">
                                <i class="fas fa-envelope modal-input-icon"></i>
                                <input type="email" wire:model="email" class="modal-input"
                                    placeholder="Ej: sucursal@empresa.com">
                            </div>
                            @error('email')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>
                
                        <div class="col-span-2">
                            <label class="modal-label">Dirección</label>
                            <textarea wire:model="address" class="modal-input min-h-[80px]" placeholder="Ej: Av. Principal 123..."></textarea>
                            @error('address')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>
                
                        <div class="col-span-2 pt-3 border-t border-slate-100 flex items-center justify-between">
                            <h4 class="text-[10px] font-black uppercase text-slate-800 tracking-widest">
                                Configuración de Impresión
                            </h4>
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input wire:model.live="direct_printing" type="checkbox"
                                    class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/20 w-4 h-4">
                                <span class="text-[11px] font-bold text-slate-600 uppercase group-hover:text-indigo-600 transition-colors">
                                    Impresión Directa
                                </span>
                            </label>
                        </div>
                
                        <div>
                            <label class="modal-label">Tamaño de Papel</label>
                            <div class="modal-input-group">
                                <i class="fas fa-file-alt modal-input-icon"></i>
                                <select wire:model="print_size" class="modal-input cursor-pointer">
                                    <option value="">Seleccione tamaño...</option>
                                    <option value="80">80mm (Estándar)</option>
                                    <option value="58">58mm (Pequeña)</option>
                                </select>
                            </div>
                            @error('print_size')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>
                
                        @if ($direct_printing)
                            <div>
                                <label class="modal-label">Nombre de Impresora</label>
                                <div class="modal-input-group">
                                    <i class="fas fa-print modal-input-icon"></i>
                                    <input type="text" wire:model="printer_name" class="modal-input"
                                        placeholder="Ej: Printer_POS">
                                </div>
                                @error('printer_name')
                                    <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif
                
                        <div class="py-2 flex items-center">
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <div class="relative">
                                    <input type="checkbox" wire:model="active" class="sr-only peer">
                                    <div
                                        class="w-10 h-5 bg-slate-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600">
                                    </div>
                                </div>
                                <span class="text-xs font-bold text-slate-600 uppercase group-hover:text-indigo-600 transition-colors">
                                    ¿Sucursal activa?
                                </span>
                            </label>
                            @error('active')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>
                
                    </div>
                </div>

                <div class="modal-footer">
                    <button wire:click="closeModal" class="btn-modal-secondary">Cancelar</button>
                    <button wire:click="store" class="btn-modal-primary" wire:loading.attr="disabled">
                        <span class="relative z-10" wire:loading.remove wire:target="store">
                            {{ $branch_id ? 'Actualizar' : 'Guardar' }}
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
