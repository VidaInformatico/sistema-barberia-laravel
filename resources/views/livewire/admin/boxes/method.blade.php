<div>
    <div class="page-container">
        <header class="page-header">
            <div class="flex items-center gap-3">
                <div class="header-icon">
                    <i class="fas fa-credit-card"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black tracking-tight text-slate-800 leading-none">Métodos de Pago</h2>
                    <p class="text-slate-400 text-[11px] font-bold uppercase tracking-wider mt-1">
                        Gestión de los métodos de pago disponibles
                    </p>
                </div>
            </div>

            <button wire:click="create" class="btn-primary">
                <i class="fas fa-plus"></i>
                Nuevo Método
            </button>
        </header>

        <div class="toolbar-container">
            <div class="search-input-group relative w-full lg:flex-1">
                <input type="text" wire:model.live.debounce.500ms="search"
                    class="search-input"
                    placeholder="Buscar por nombre...">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                    <i class="fas fa-search text-sm"></i>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-between w-full lg:w-auto gap-3">
                <div class="per-page-select flex items-center bg-slate-50 px-3 py-2 rounded-lg border border-slate-100">
                    <label class="text-[10px] font-black text-slate-400 uppercase border-r border-slate-200 pr-3 mr-3 leading-none">
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

        <div class="datatable-container">
            <table class="datatable">
                <thead class="datatable-thead">
                    <tr>
                        <th class="datatable-th">Nombre</th>
                        <th class="datatable-th">¿Es Efectivo?</th>
                        <th class="datatable-th text-right">Gestión</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($paymentMethods as $item)
                        <tr class="datatable-tr">
                            <td class="datatable-td">
                                <span class="text-sm font-bold text-slate-700">{{ $item->name }}</span>
                            </td>
                            <td class="datatable-td">
                                @if($item->is_efectivo)
                                    <span class="px-2 py-1 text-[10px] font-black bg-blue-100 text-blue-600 rounded-full uppercase">Sí</span>
                                @else
                                    <span class="px-2 py-1 text-[10px] font-black bg-slate-100 text-slate-400 rounded-full uppercase">No</span>
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
                            <td colspan="3" class="py-16 text-center">
                                <i class="fas fa-credit-card text-slate-200 text-3xl mb-3 block"></i>
                                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">No hay métodos configurados</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 px-2">
            {{ $paymentMethods->links() }}
        </div>
    </div>

    @if ($isOpen)
        <div class="modal-overlay">
            <div class="modal-backdrop" wire:click="closeModal"></div>

            <div class="modal-container max-w-lg">
                <div class="modal-header">
                    <div class="flex items-center gap-3">
                        <div class="modal-icon-box">
                            <i class="fas {{ $payment_method_id ? 'fa-edit' : 'fa-plus' }}"></i>
                        </div>
                        <h3 class="modal-title">
                            {{ $payment_method_id ? 'Editar Método de Pago' : 'Nuevo Método de Pago' }}
                        </h3>
                    </div>
                    <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="grid grid-cols-1 gap-4">
                        
                        <div>
                            <label class="modal-label">Nombre del Método</label>
                            <div class="modal-input-group">
                                <i class="fas fa-credit-card modal-input-icon"></i>
                                <input type="text" wire:model="name" class="modal-input" placeholder="Ej: Efectivo, Visa, Yape, etc.">
                            </div>
                            @error('name') <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span> @enderror
                        </div>

                        <div class="py-2">
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <div class="relative">
                                    <input type="checkbox" wire:model="is_efectivo" class="sr-only peer">
                                    <div class="w-10 h-5 bg-slate-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600"></div>
                                </div>
                                <span class="text-xs font-bold text-slate-600 uppercase group-hover:text-blue-600 transition-colors">¿Es pago en efectivo?</span>
                            </label>
                            @error('is_efectivo') <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span> @enderror
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button wire:click="closeModal" class="btn-modal-secondary">Cancelar</button>
                    <button wire:click="store" class="btn-modal-primary" wire:loading.attr="disabled">
                        <span class="relative z-10" wire:loading.remove wire:target="store">
                            {{ $payment_method_id ? 'Actualizar' : 'Guardar' }}
                        </span>
                        <span class="relative z-10" wire:loading wire:target="store">
                            Procesando...
                        </span>
                        <i class="fas fa-check-circle text-blue-400 group-hover:text-white transition-colors text-xs relative z-10"></i>
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
                        Livewire.dispatch('delete-confirmed', { id: data.id });
                    }
                });
            });
        });
    </script>
</div>