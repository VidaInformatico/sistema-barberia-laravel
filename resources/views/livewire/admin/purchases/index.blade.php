<div>
    <div class="page-container">
        <header class="page-header">
            <div class="flex items-center gap-3">
                <div class="header-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                <div>
                    <h2 class="text-2xl font-black tracking-tight text-slate-800 leading-none">Módulo de Compras</h2>
                    <p class="text-slate-400 text-[11px] font-bold uppercase tracking-wider mt-1">Historial general de
                        compras y facturas</p>
                </div>
            </div>
            <a href="{{ route('admin.purchases.create') }}" class="btn-primary">
                <i class="fas fa-cash-register"></i> Nueva Compra
            </a>
        </header>

        <div class="toolbar-container flex flex-col lg:flex-row gap-4 items-center justify-between">
            <!-- Buscador Principal -->
            <div class="search-input-group relative w-full lg:flex-1">
                <input type="text" wire:model.live.debounce.500ms="search" class="search-input w-full"
                    placeholder="Buscar por ticket, proveedor o user...">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                    <i class="fas fa-search text-sm"></i>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-end w-full lg:w-auto gap-3">

                <div class="flex items-center bg-slate-50 px-3 py-2 rounded-lg border border-slate-100">
                    <label
                        class="text-[10px] font-black text-slate-400 uppercase border-r border-slate-200 pr-3 mr-3 leading-none">Desde</label>
                    <input type="date" wire:model.live="fromDate"
                        class="bg-transparent border-none p-0 text-xs font-bold text-slate-600 focus:ring-0 cursor-pointer">
                </div>

                <div class="flex items-center bg-slate-50 px-3 py-2 rounded-lg border border-slate-100">
                    <label
                        class="text-[10px] font-black text-slate-400 uppercase border-r border-slate-200 pr-3 mr-3 leading-none">Hasta</label>
                    <input type="date" wire:model.live="toDate"
                        class="bg-transparent border-none p-0 text-xs font-bold text-slate-600 focus:ring-0 cursor-pointer">
                </div>

                <div class="flex items-center bg-slate-50 px-3 py-2 rounded-lg border border-slate-100">
                    <label
                        class="text-[10px] font-black text-slate-400 uppercase border-r border-slate-200 pr-3 mr-3 leading-none">Mostrar</label>
                    <select wire:model.live="perPage"
                        class="bg-transparent border-none p-0 pr-8 text-sm font-bold text-slate-700 focus:ring-0 cursor-pointer">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>

                <div class="flex items-center gap-2 ml-2">
                    <a href="{{ route('admin.purchases.export', ['type' => 'pdf', 'search' => $search, 'from' => $fromDate, 'to' => $toDate]) }}"
                        target="_blank"
                        class="flex items-center justify-center bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 px-3 py-2 rounded-lg text-xs font-bold uppercase transition duration-200 gap-1.5 shadow-sm">
                        <i class="fas fa-file-pdf text-sm"></i> PDF
                    </a>

                    <a href="{{ route('admin.purchases.export', ['type' => 'excel', 'search' => $search, 'from' => $fromDate, 'to' => $toDate]) }}"
                        class="flex items-center justify-center bg-green-50 hover:bg-green-100 text-green-600 border border-green-200 px-3 py-2 rounded-lg text-xs font-bold uppercase transition duration-200 gap-1.5 shadow-sm">
                        <i class="fas fa-file-excel text-sm"></i> Excel
                    </a>
                </div>

            </div>
        </div>

        <!-- Vista Móvil (Tarjetas Refactorizadas) -->
        <div class="block md:hidden space-y-3">
            @forelse ($purchases as $item)
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm space-y-3"
                    wire:key="purchase-card-{{ $item->id }}">
                    <!-- Encabezado: Factura + Método de Pago -->
                    <div class="flex items-start justify-between border-b border-slate-100 pb-2.5">
                        <div class="flex flex-col">
                            <span class="text-sm font-black text-indigo-600 cursor-pointer hover:underline"
                                wire:click="showDetails({{ $item->id }})">
                                Factura #{{ $item->purchase_number }}
                            </span>
                            <span class="text-[10px] text-slate-400 font-semibold mt-0.5">
                                <i
                                    class="far fa-clock mr-1"></i>{{ $item->created_at ? $item->created_at->format('d/m/Y H:i') : 'N/A' }}
                            </span>
                        </div>

                        <div>
                            @if ($item->credit > 0)
                                <span
                                    class="px-2.5 py-0.5 text-[10px] font-black bg-amber-100 text-amber-700 rounded-full uppercase">
                                    Crédito
                                </span>
                            @else
                                <span
                                    class="px-2.5 py-0.5 text-[10px] font-black bg-emerald-100 text-emerald-700 rounded-full uppercase">
                                    Contado
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Proveedor y Usuario -->
                    <div class="flex flex-col space-y-1 text-xs">
                        <div class="flex items-center text-slate-700 font-bold">
                            <i class="fas fa-truck text-slate-400 mr-1.5 w-4 text-center"></i>
                            <span>{{ $item->supplier ? $item->supplier->company_name : 'Proveedor General' }}</span>
                        </div>
                        <div class="flex items-center text-slate-400 font-medium text-[11px]">
                            <i class="fas fa-user-tie mr-1.5 w-4 text-center"></i>
                            <span>Atendido por: {{ $item->user->name ?? 'Sistema' }}</span>
                        </div>
                    </div>

                    <!-- Desglose Financiero -->
                    <div
                        class="bg-slate-50 p-2.5 rounded-lg border border-slate-100 flex justify-between items-center text-xs">
                        <span class="text-slate-400 font-bold text-[10px] uppercase">Total Pagado</span>
                        <span class="font-black text-slate-800 text-base">
                            {{ $company->currency_code }}{{ number_format($item->total, 2) }}
                        </span>
                    </div>

                    <!-- Acciones -->
                    <div class="flex items-center justify-end gap-2 pt-1 border-t border-slate-50">
                        <button wire:click="showDetails({{ $item->id }})"
                            class="p-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors"
                            title="Ver Detalles">
                            <i class="fas fa-eye text-sm"></i>
                        </button>

                        <a href="{{ route('admin.purchases.edit', Crypt::encrypt($item->id)) }}"
                            class="p-2 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-600 transition-colors"
                            title="Editar">
                            <i class="fas fa-edit text-sm"></i>
                        </a>

                        <button wire:click="deleteConfirm({{ $item->id }})"
                            class="p-2 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 transition-colors"
                            title="Anular">
                            <i class="fas fa-ban text-sm"></i>
                        </button>

                        <a href="{{ route('admin.purchases.ticket', Crypt::encrypt($item->id)) }}" target="_blank"
                            class="p-2 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-600 transition-colors"
                            title="Ticket">
                            <i class="fas fa-print text-sm"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="bg-white p-8 rounded-xl border border-slate-200 text-center">
                    <i class="fas fa-cash-register text-slate-200 text-3xl mb-3 block"></i>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">No se encontraron compras
                        registradas</p>
                </div>
            @endforelse
        </div>

        <!-- Vista Escritorio (Tabla) -->
        <div class="datatable-container hidden md:block overflow-x-auto">
            <table class="datatable w-full">
                <thead class="datatable-thead">
                    <tr>
                        <th class="datatable-th text-center w-36">Gestión</th>
                        <th class="datatable-th">N° Factura</th>
                        <th class="datatable-th">Proveedor</th>
                        <th class="datatable-th">Atendido por</th>
                        <th class="datatable-th">Método</th>
                        <th class="datatable-th text-right">Total Pagado</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-50">
                    @forelse ($purchases as $item)
                        <tr class="datatable-tr" wire:key="purchase-row-{{ $item->id }}">
                            <td class="datatable-td text-center">
                                <div class="action-group inline-flex">
                                    <button wire:click="showDetails({{ $item->id }})"
                                        class="btn-action bg-[#f5f5f5] hover:bg-[#eeeeee] border-r"
                                        title="Ver Detalles">
                                        <i class="fas fa-eye text-xs"></i>
                                    </button>

                                    <button wire:click="deleteConfirm({{ $item->id }})"
                                        class="btn-action bg-[#ffebee] hover:bg-[#ffcdd2] text-[#c62828] border-r"
                                        title="Anular">
                                        <i class="fas fa-ban text-xs"></i>
                                    </button>

                                    <a href="{{ route('admin.purchases.edit', Crypt::encrypt($item->id)) }}"
                                        class="btn-action bg-[#fff8e1] hover:bg-[#ffecb3] text-[#f57f17] border-r"
                                        title="Editar">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>

                                    <a href="{{ route('admin.purchases.ticket', Crypt::encrypt($item->id)) }}"
                                        target="_blank"
                                        class="btn-action bg-[#e8f5e9] hover:bg-[#c8e6c9] text-[#2e7d32]"
                                        title="Ticket">
                                        <i class="fas fa-print text-xs"></i>
                                    </a>
                                </div>
                            </td>
                            <td class="datatable-td">
                                <span class="text-sm font-black text-indigo-600 cursor-pointer hover:underline"
                                    wire:click="showDetails({{ $item->id }})">
                                    {{ $item->purchase_number }}
                                </span>
                            </td>
                            <td class="datatable-td">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-slate-700">
                                        {{ $item->supplier ? $item->supplier->company_name : 'Proveedor General' }}
                                    </span>
                                </div>
                            </td>
                            <td class="datatable-td">
                                <span class="text-xs text-slate-600 font-semibold">
                                    {{ $item->user->name ?? 'Sistema' }}
                                </span>
                            </td>
                            <td class="datatable-td">
                                <span
                                    class="px-2.5 py-0.5 text-[10px] font-black rounded-full uppercase {{ $item->credit > 0 ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' }}">
                                    {{ $item->credit > 0 ? 'Crédito' : 'Contado' }}
                                </span>
                            </td>
                            <td class="datatable-td text-right">
                                <span class="text-sm font-black text-slate-800">
                                    {{ $company->currency_code }}{{ number_format($item->total, 2) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-16 text-center">
                                <i class="fas fa-cash-register text-slate-200 text-3xl mb-3 block"></i>
                                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">
                                    No se encontraron compras registradas
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if (method_exists($purchases, 'links'))
            <div class="mt-4 px-2">
                {{ $purchases->links() }}
            </div>
        @endif
    </div>

    @if ($viewingPurchase)
        <div class="modal-overlay" style="z-index: 60;">
            <div class="modal-backdrop" wire:click="closeDetails"></div>
            <div class="modal-container max-w-md">
                <div class="modal-header bg-slate-50">
                    <div class="text-left">
                        <span
                            class="text-[10px] font-black text-indigo-600 uppercase tracking-widest block leading-none">Resumen
                            de Venta</span>
                        <h3 class="modal-title text-base mt-0.5">{{ $viewingPurchase->purchase_number }}</h3>
                    </div>
                    <button wire:click="closeDetails" class="text-slate-400 hover:text-slate-600">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
                <div class="modal-body text-xs text-slate-600">
                    <div class="space-y-1.5 border-b border-dashed border-slate-200 pb-3 mb-3">
                        <div class="flex justify-between"><strong>Fecha:</strong>
                            <span>{{ $viewingPurchase->created_at->format('d/m/Y H:i A') }}</span>
                        </div>
                        <div class="flex justify-between"><strong>Proveedor:</strong>
                            <span>{{ $viewingPurchase->supplier->company_name ?? 'General' }}</span>
                        </div>
                    </div>
                    <div class="space-y-2 border-b border-dashed border-slate-200 pb-3 mb-3">
                        <span class="font-black text-slate-500 uppercase tracking-wide text-[10px] block">Ítems:</span>
                        @foreach ($viewingPurchase->items as $detail)
                            <div class="flex justify-between font-medium">
                                <span>{{ $detail->quantity }}x {{ $detail->product->name ?? 'Servicio' }}</span>
                                <span
                                    class="font-bold text-slate-700">{{ $company->currency_code }}{{ number_format($detail->total, 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="space-y-1.5 text-right font-bold text-slate-500">
                        <div class="flex justify-between"><span>Subtotal:</span> <span
                                class="text-slate-700">{{ $company->currency_code }}{{ number_format($viewingPurchase->gross_total, 2) }}</span>
                        </div>
                        <div class="flex justify-between"><span>Impuesto:</span> <span
                                class="text-slate-700">{{ $company->currency_code }}{{ number_format($viewingPurchase->tax_total, 2) }}</span>
                        </div>
                        <div class="flex justify-between"><span>Descuento:</span> <span
                                class="text-red-500">-{{ $company->currency_code }}{{ number_format($viewingPurchase->discount_total, 2) }}</span>
                        </div>
                        <div class="flex justify-between border-t pt-1.5 text-slate-800 text-sm font-black">
                            <span>Total Cobrado:</span> <span
                                class="text-indigo-600">{{ $company->currency_code }}{{ number_format($viewingPurchase->total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('confirm-void', data => {
                Swal.fire({
                    title: '¿Anular esta Compra?',
                    text: "Se revocará la transacción financiera del balance diario.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#EF4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Sí, anular',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch('void-confirmed', {
                            id: data.id
                        });
                    }
                });
            });
        });
    </script>
</div>
