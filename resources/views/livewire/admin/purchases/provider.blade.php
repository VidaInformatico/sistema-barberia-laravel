<div class="page-container">
    <header class="page-header">
        <div class="flex items-center gap-3">
            <div class="header-icon">
                <i class="fas fa-truck"></i>
            </div>
            <div>
                <h2 class="text-2xl font-black tracking-tight text-slate-800 leading-none">Proveedores</h2>
                <p class="text-slate-400 text-[11px] font-bold uppercase tracking-wider mt-1">
                    Gestión de la base de datos de proveedores
                </p>
            </div>
        </div>

        <button wire:click="create" class="btn-primary">
            <i class="fas fa-plus"></i>
            Nuevo Proveedor
        </button>
    </header>

    <div class="toolbar-container">
        <div class="search-input-group relative w-full lg:flex-1">
            <input type="text" wire:model.live.debounce.500ms="search" class="search-input"
                placeholder="Buscar por nombre o RUC...">
            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                <i class="fas fa-search text-sm"></i>
            </div>
        </div>
    </div>

    <!-- Vista Móvil (Tarjetas Refactorizadas) -->
    <div class="block md:hidden space-y-3">
        @forelse ($suppliers as $item)
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm space-y-3"
                wire:key="supplier-card-{{ $item->id }}">
                <!-- Encabezado: Nombre de la Empresa + Estado -->
                <div class="flex items-start justify-between border-b border-slate-100 pb-2.5">
                    <div class="flex flex-col">
                        <span class="text-sm font-black text-slate-800">{{ $item->company_name }}</span>
                        <span class="text-[10px] font-mono text-slate-400 font-semibold mt-0.5">
                            <i class="id-card mr-1"></i>RUC / ID: {{ $item->tax_id ?: 'Sin Identificación' }}
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
                                class="px-2.5 py-0.5 text-[10px] font-black bg-red-100 text-red-700 rounded-full uppercase">
                                Inactivo
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Detalles del Contacto -->
                <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-100 space-y-1 text-xs">
                    <div class="flex items-center text-slate-700 font-bold">
                        <i class="fas fa-user text-slate-400 mr-2 w-4 text-center"></i>
                        <span>{{ $item->contact_name ?: 'Sin contacto registrado' }}</span>
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
                <i class="fas fa-truck text-slate-200 text-3xl mb-3 block"></i>
                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">No hay proveedores registrados</p>
            </div>
        @endforelse
    </div>

    <!-- Vista Escritorio (Tabla) -->
    <div class="datatable-container hidden md:block overflow-x-auto">
        <table class="datatable w-full">
            <thead class="datatable-thead">
                <tr>
                    <th class="datatable-th">Empresa</th>
                    <th class="datatable-th">Contacto</th>
                    <th class="datatable-th">RUC / Tax ID</th>
                    <th class="datatable-th">Estado</th>
                    <th class="datatable-th text-right w-28">Gestión</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-50">
                @forelse ($suppliers as $item)
                    <tr class="datatable-tr" wire:key="supplier-row-{{ $item->id }}">
                        <td class="datatable-td">
                            <span class="text-sm font-bold text-slate-700">{{ $item->company_name }}</span>
                        </td>
                        <td class="datatable-td text-xs text-slate-500 font-semibold">
                            {{ $item->contact_name ?: 'N/A' }}
                        </td>
                        <td class="datatable-td text-xs font-mono text-slate-600">
                            {{ $item->tax_id ?: 'N/A' }}
                        </td>
                        <td class="datatable-td">
                            <span
                                class="px-2.5 py-0.5 text-[10px] font-black rounded-full uppercase {{ $item->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                {{ $item->is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="datatable-td text-right">
                            <div class="action-group inline-flex">
                                <button wire:click="edit({{ $item->id }})"
                                    class="btn-action bg-[#fff8e1] hover:bg-[#ffecb3] text-[#f57f17] border-r"
                                    title="Editar">
                                    <i class="fas fa-edit text-xs"></i>
                                </button>

                                <button wire:click="deleteConfirm({{ $item->id }})"
                                    class="btn-action bg-[#ffebee] hover:bg-[#ffcdd2] text-[#c62828]" title="Eliminar">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-16 text-center">
                            <i class="fas fa-truck text-slate-200 text-3xl mb-3 block"></i>
                            <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">
                                No hay proveedores registrados
                            </p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if (method_exists($suppliers, 'links'))
        <div class="mt-4 px-2">
            {{ $suppliers->links() }}
        </div>
    @endif

    @if ($isOpen)
        <div class="modal-overlay">
            <div class="modal-backdrop" wire:click="closeModal"></div>
            <div class="modal-container max-w-2xl">
                <div class="modal-header">
                    <h3 class="modal-title">{{ $supplier_id ? 'Editar Proveedor' : 'Nuevo Proveedor' }}</h3>
                    <button wire:click="closeModal"><i class="fas fa-times text-slate-400"></i></button>
                </div>
                <div class="modal-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div class="col-span-2">
                            <label class="modal-label">Nombre de Empresa</label>
                            <div class="modal-input-group">
                                <i class="fas fa-building modal-input-icon"></i>
                                <input type="text" wire:model="company_name" class="modal-input"
                                    placeholder="Ej: Distribuidora S.A.C.">
                            </div>
                            @error('company_name')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="modal-label">Nombre Contacto</label>
                            <div class="modal-input-group">
                                <i class="fas fa-user modal-input-icon"></i>
                                <input type="text" wire:model="contact_name" class="modal-input"
                                    placeholder="Ej: Juan Pérez">
                            </div>
                            @error('contact_name')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="modal-label">Tax ID / RUC</label>
                            <div class="modal-input-group">
                                <i class="fas fa-id-card modal-input-icon"></i>
                                <input type="text" wire:model="tax_id" class="modal-input"
                                    placeholder="Ej: 20123456789">
                            </div>
                            @error('tax_id')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="modal-label">Email</label>
                            <div class="modal-input-group">
                                <i class="fas fa-envelope modal-input-icon"></i>
                                <input type="email" wire:model="email" class="modal-input"
                                    placeholder="Ej: contacto@empresa.com">
                            </div>
                            @error('email')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="modal-label">Teléfono</label>
                            <div class="modal-input-group">
                                <i class="fas fa-phone modal-input-icon"></i>
                                <input type="text" wire:model="phone" class="modal-input"
                                    placeholder="Ej: +51 999 999 999">
                            </div>
                            @error('phone')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-span-2">
                            <label class="modal-label">Dirección</label>
                            <div class="modal-input-group">
                                <i class="fas fa-map-marker-alt modal-input-icon"></i>
                                <input type="text" wire:model="address" class="modal-input"
                                    placeholder="Ej: Av. Principal 123">
                            </div>
                            @error('address')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="modal-label">Ciudad</label>
                            <div class="modal-input-group">
                                <i class="fas fa-city modal-input-icon"></i>
                                <input type="text" wire:model="city" class="modal-input"
                                    placeholder="Ej: Trujillo">
                            </div>
                            @error('city')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="modal-label">Sitio Web</label>
                            <div class="modal-input-group">
                                <i class="fas fa-globe modal-input-icon"></i>
                                <input type="url" wire:model="website" class="modal-input"
                                    placeholder="Ej: https://empresa.com">
                            </div>
                            @error('website')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-span-2">
                            <label class="modal-label">Notas</label>
                            <div class="modal-input-group">
                                <i class="fas fa-sticky-note modal-input-icon"></i>
                                <textarea wire:model="notes" class="modal-input" placeholder="Información adicional..."></textarea>
                            </div>
                            @error('notes')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-span-2 py-2">
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <div class="relative">
                                    <input type="checkbox" wire:model="is_active" class="sr-only peer">
                                    <div
                                        class="w-10 h-5 bg-slate-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600">
                                    </div>
                                </div>
                                <span
                                    class="text-xs font-bold text-slate-600 uppercase group-hover:text-blue-600 transition-colors">¿Proveedor
                                    activo?</span>
                            </label>
                            @error('is_active')
                                <span class="text-red-500 text-[10px] font-bold block mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button wire:click="closeModal" class="btn-modal-secondary">Cancelar</button>
                    <button wire:click="store" class="btn-modal-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove
                            wire:target="store">{{ $supplier_id ? 'Actualizar' : 'Guardar' }}</span>
                        <span wire:loading wire:target="store">Procesando...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('confirm-delete', data => {
                Swal.fire({
                    title: '¿Eliminar Método?',
                    text: "Esta acción no se puede deshacer.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#2563eb',
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
