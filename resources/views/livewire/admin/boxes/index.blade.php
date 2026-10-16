<div class="page-container">
    <header class="page-header">
        <div class="flex items-center gap-3">
            <div class="header-icon">
                <i class="fas fa-cash-register"></i>
            </div>
            <div>
                <h2 class="text-2xl font-black tracking-tight text-slate-800 leading-none">Cajas de Efectivo</h2>
                <p class="text-slate-400 text-[11px] font-bold uppercase tracking-wider mt-1">
                    Control de aperturas, cierres y flujos de efectivo
                </p>
            </div>
        </div>

        <button wire:click="create" class="btn-primary">
            <i class="fas fa-plus"></i>
            Abrir Caja
        </button>
    </header>

    @if (session()->has('message'))
        <div class="mb-6 p-4 rounded-lg bg-orange-50 border-l-4 border-orange-500 shadow-sm flex items-center justify-between"
            x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
            <div class="flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-orange-500 text-lg"></i>
                <span class="text-orange-800 font-bold text-xs uppercase">{{ session('message') }}</span>
            </div>
            <button @click="show = false" class="text-orange-400 hover:text-orange-600">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
    @endif

    <div class="toolbar-container">
        <div class="search-input-group relative w-full lg:flex-1">
            <input type="text" wire:model.live.debounce.500ms="search" class="search-input"
                placeholder="Buscar caja...">
            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                <i class="fas fa-search text-sm"></i>
            </div>
        </div>
    </div>

    <!-- Vista Móvil (Tarjetas) -->
    <div class="block md:hidden space-y-3">
        @forelse ($registers as $reg)
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm space-y-3"
                wire:key="register-card-{{ $reg->id }}">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                    <div class="flex flex-col pr-2">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="text-sm font-bold text-slate-700 leading-tight">{{ $reg->name }}</span>
                            @if ($reg->opened_at)
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-medium bg-blue-100 text-blue-800">
                                    {{ \Carbon\Carbon::parse($reg->opened_at)->format('d M, g:i A') }}
                                </span>
                            @endif
                        </div>
                        <span class="text-[10px] text-slate-400 font-bold uppercase mt-0.5">
                            <i class="far fa-user mr-1"></i>{{ $reg->opener->name ?? 'N/A' }}
                        </span>
                    </div>
                    <div>
                        @if ($reg->status === 'open')
                            <span
                                class="px-2 py-1 text-[10px] font-black bg-emerald-100 text-emerald-600 rounded-full uppercase">Abierta</span>
                        @else
                            <span
                                class="px-2 py-1 text-[10px] font-black bg-slate-100 text-slate-400 rounded-full uppercase">Cerrada</span>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div>
                        <span class="text-slate-400 font-medium block">Monto Apertura:</span>
                        <span class="text-xs font-bold text-slate-400">{{ $company->currency_code }}</span>
                        <span
                            class="text-sm font-bold text-slate-700">{{ number_format($reg->opening_amount, 2) }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-medium block">Saldo Actual:</span>
                        <span class="text-xs font-bold text-blue-400">{{ $company->currency_code }}</span>
                        <span
                            class="text-sm font-black text-blue-600">{{ number_format($reg->current_amount, 2) }}</span>
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-100 flex items-center justify-end">
                    <div class="inline-flex rounded-xl shadow-sm" role="group">
                        <a href="{{ route('admin.boxes.movements', Crypt::encrypt($reg->id)) }}"
                            class="inline-flex items-center gap-2 px-3 py-2 text-[11px] font-black uppercase tracking-wider text-gray-700 bg-white border border-gray-200 rounded-l-xl hover:bg-indigo-50 hover:text-indigo-600 transition-all duration-200"
                            title="Detalles">
                            <i class="fa-solid fa-eye text-[10px]"></i>
                        </a>

                        @if ($reg->status === 'open')
                            <button onclick="closeBox({{ $reg->id }})"
                                class="inline-flex items-center px-3 py-2 text-amber-700 bg-white border-t border-b border-r border-gray-200 hover:bg-amber-50 hover:text-amber-800 transition-all duration-200"
                                title="Cerrar Caja">
                                <i class="fa-solid fa-cash-register text-[12px]"></i>
                            </button>
                            <button wire:click="edit({{ $reg->id }})"
                                class="inline-flex items-center px-3 py-2 text-gray-700 bg-white border-t border-b border-r border-gray-200 hover:bg-amber-50 hover:text-amber-600 transition-all duration-200"
                                title="Editar">
                                <i class="fa-solid fa-pen text-[10px]"></i>
                            </button>
                            <button wire:click="deleteConfirm({{ $reg->id }})"
                                class="inline-flex items-center px-3 py-2 text-gray-700 bg-white border-t border-b border-r border-gray-200 rounded-r-xl hover:bg-rose-50 hover:text-rose-600 transition-all duration-200"
                                title="Eliminar">
                                <i class="fa-solid fa-trash-can text-[10px]"></i>
                            </button>
                        @else
                            <div
                                class="inline-flex items-center px-3 py-2 text-slate-200 bg-white border border-gray-200 rounded-r-xl cursor-not-allowed">
                                <i class="fa-solid fa-lock text-[10px]"></i>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white p-8 rounded-xl border border-slate-200 text-center">
                <i class="fas fa-cash-register text-slate-200 text-3xl mb-3 block"></i>
                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">No hay cajas registradas</p>
            </div>
        @endforelse
    </div>

    <!-- Vista Escritorio (Tabla) -->
    <div class="datatable-container hidden md:block overflow-x-auto">
        <table class="datatable w-full">
            <thead class="datatable-thead">
                <tr>
                    <th class="datatable-th text-left">Gestión</th>
                    <th class="datatable-th">Caja / Responsable</th>
                    <th class="datatable-th text-center">Estado</th>
                    <th class="datatable-th">Monto Apertura</th>
                    <th class="datatable-th">Saldo Actual</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse ($registers as $reg)
                    <tr class="datatable-tr" wire:key="register-row-{{ $reg->id }}">
                        <td class="datatable-td text-left">
                            <div class="inline-flex rounded-xl shadow-sm" role="group">
                                <a href="{{ route('admin.boxes.movements', Crypt::encrypt($reg->id)) }}"
                                    class="inline-flex items-center gap-2 px-3 py-2 text-[11px] font-black uppercase tracking-wider text-gray-700 bg-white border border-gray-200 rounded-l-xl hover:bg-indigo-50 hover:text-indigo-600 transition-all duration-200"
                                    title="Detalles">
                                    <i class="fa-solid fa-eye text-[10px]"></i>
                                </a>

                                @if ($reg->status === 'open')
                                    <button onclick="closeBox({{ $reg->id }})"
                                        class="inline-flex items-center px-3 py-2 text-amber-700 bg-white border-t border-b border-r border-gray-200 hover:bg-amber-50 hover:text-amber-800 transition-all duration-200"
                                        title="Cerrar Caja">
                                        <i class="fa-solid fa-cash-register text-[12px]"></i>
                                    </button>
                                    <button wire:click="edit({{ $reg->id }})"
                                        class="inline-flex items-center px-3 py-2 text-gray-700 bg-white border-t border-b border-r border-gray-200 hover:bg-amber-50 hover:text-amber-600 transition-all duration-200"
                                        title="Editar">
                                        <i class="fa-solid fa-pen text-[10px]"></i>
                                    </button>
                                    <button wire:click="deleteConfirm({{ $reg->id }})"
                                        class="inline-flex items-center px-3 py-2 text-gray-700 bg-white border-t border-b border-r border-gray-200 rounded-r-xl hover:bg-rose-50 hover:text-rose-600 transition-all duration-200"
                                        title="Eliminar">
                                        <i class="fa-solid fa-trash-can text-[10px]"></i>
                                    </button>
                                @else
                                    <div
                                        class="inline-flex items-center px-3 py-2 text-slate-200 bg-white border border-gray-200 rounded-r-xl cursor-not-allowed">
                                        <i class="fa-solid fa-lock text-[10px]"></i>
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="datatable-td">
                            <div class="flex items-center gap-2 mb-0.5">
                                <span class="text-sm font-bold text-slate-700">{{ $reg->name }}</span>

                                @if ($reg->opened_at)
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-blue-100 text-blue-800 tracking-wide">
                                        {{ \Carbon\Carbon::parse($reg->opened_at)->format('d M, g:i A') }}
                                    </span>
                                @endif
                            </div>

                            <span class="text-[10px] text-slate-400 font-bold uppercase block">
                                {{ $reg->opener->name ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="datatable-td text-center">
                            @if ($reg->status === 'open')
                                <span
                                    class="px-2 py-1 text-[10px] font-black bg-emerald-100 text-emerald-600 rounded-full uppercase">Abierta</span>
                            @else
                                <span
                                    class="px-2 py-1 text-[10px] font-black bg-slate-100 text-slate-400 rounded-full uppercase">Cerrada</span>
                            @endif
                        </td>
                        <td class="datatable-td">
                            <span class="text-xs font-bold text-slate-400">{{ $company->currency_code }}</span>
                            <span
                                class="text-sm font-bold text-slate-700">{{ number_format($reg->opening_amount, 2) }}</span>
                        </td>
                        <td class="datatable-td">
                            <span class="text-xs font-bold text-blue-400">{{ $company->currency_code }}</span>
                            <span
                                class="text-sm font-black text-blue-600">{{ number_format($reg->current_amount, 2) }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-16 text-center">
                            <i class="fas fa-cash-register text-slate-200 text-3xl mb-3 block"></i>
                            <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">No hay cajas
                                registradas</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if (method_exists($registers, 'links'))
        <div class="mt-4 px-2">
            {{ $registers->links() }}
        </div>
    @endif

    @if ($isOpen)
        <div class="modal-overlay">
            <div class="modal-backdrop" wire:click="closeModal"></div>
            <div class="modal-container max-w-lg">
                <div class="modal-header">
                    <div class="flex items-center gap-3">
                        <div class="modal-icon-box">
                            <i class="fas {{ $cash_register_id ? 'fa-edit' : 'fa-cash-register' }}"></i>
                        </div>
                        <h3 class="modal-title">{{ $cash_register_id ? 'Editar Caja' : 'Apertura de Caja' }}</h3>
                    </div>
                    <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="modal-label">Nombre de la Caja</label>
                            <div class="modal-input-group">
                                <i class="fas fa-tag modal-input-icon"></i>
                                <input type="text" wire:model="name" class="modal-input"
                                    placeholder="Ej: Caja Principal">
                            </div>
                            @error('name')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="modal-label">Monto Inicial ({{ $company->currency_code }})</label>
                            <div class="modal-input-group">
                                <i class="fas fa-dollar-sign modal-input-icon"></i>
                                <input type="number" step="0.01" wire:model="opening_amount" class="modal-input"
                                    placeholder="0.00">
                            </div>
                            @error('opening_amount')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="modal-label">Notas</label>
                            <textarea wire:model="notes" class="modal-input min-h-[80px]" placeholder="Observaciones opcionales..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button wire:click="closeModal" class="btn-modal-secondary">Cancelar</button>
                    <button wire:click="store" class="btn-modal-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove
                            wire:target="store">{{ $cash_register_id ? 'Actualizar' : 'Guardar' }}</span>
                        <span wire:loading wire:target="store">Procesando...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    function closeBox(id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¡Esta acción cerrará la caja actual!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, cerrar caja',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                Livewire.dispatch('closeBoxEvent', {
                    id: id
                });
            }
        });
    }
</script>
