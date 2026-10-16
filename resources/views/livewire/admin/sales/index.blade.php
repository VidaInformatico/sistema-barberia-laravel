<div>
    <div class="page-container">
        <header class="page-header">
            <div class="flex items-center gap-3">
                <div class="header-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                <div>
                    <h2 class="text-2xl font-black tracking-tight text-slate-800 leading-none">Módulo de Ventas</h2>
                    <p class="text-slate-400 text-[11px] font-bold uppercase tracking-wider mt-1">Historial general de
                        cobros y facturas</p>
                </div>
            </div>
            <a href="{{ route('admin.sales.create') }}" class="btn-primary">
                <i class="fas fa-cash-register"></i> Nuevo Cobro (POS)
            </a>
        </header>

        <div class="toolbar-container flex flex-col lg:flex-row gap-4 items-center justify-between">
            <div class="search-input-group relative w-full lg:flex-1">
                <input type="text" wire:model.live.debounce.500ms="search" class="search-input w-full"
                    placeholder="Buscar por ticket, cliente o barbero...">
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
                    <select wire:model.live="filterBarber"
                        class="bg-transparent border-none p-0 pr-8 text-xs font-bold text-slate-600 focus:ring-0 cursor-pointer uppercase">
                        <option value="">Todos los barberos</option>
                        @foreach ($barbers as $barber)
                            <option value="{{ $barber->id }}">{{ $barber->user->name }}</option>
                        @endforeach
                    </select>
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
                    <a href="{{ route('admin.sales.export', ['type' => 'pdf', 'search' => $search, 'barber' => $filterBarber, 'from' => $fromDate, 'to' => $toDate]) }}"
                        target="_blank"
                        class="flex items-center justify-center bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 px-3 py-2 rounded-lg text-xs font-bold uppercase transition duration-200 gap-1.5 shadow-sm">
                        <i class="fas fa-file-pdf text-sm"></i> PDF
                    </a>

                    <a href="{{ route('admin.sales.export', ['type' => 'excel', 'search' => $search, 'barber' => $filterBarber, 'from' => $fromDate, 'to' => $toDate]) }}"
                        class="flex items-center justify-center bg-green-50 hover:bg-green-100 text-green-600 border border-green-200 px-3 py-2 rounded-lg text-xs font-bold uppercase transition duration-200 gap-1.5 shadow-sm">
                        <i class="fas fa-file-excel text-sm"></i> Excel
                    </a>
                </div>

            </div>
        </div>

        <div class="block md:hidden space-y-3">
            @forelse ($sales as $item)
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                        <div>
                            <span class="text-xs text-slate-400 font-semibold block">N° Factura</span>
                            <span class="text-base font-black text-indigo-600 cursor-pointer hover:underline"
                                wire:click="showDetails({{ $item->id }})">
                                {{ $item->sale_number }}
                            </span>
                        </div>
                        <div class="text-right">
                            <span
                                class="px-2 py-0.5 text-[10px] font-black rounded uppercase block mb-1 {{ $item->credit > 0 ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' }}">
                                {{ $item->credit > 0 ? 'CRÉDITO' : 'CONTADO' }}
                            </span>
                            <span class="text-base font-black text-slate-800">
                                {{ $company->currency_code }}{{ number_format($item->total, 2) }}
                            </span>
                        </div>
                    </div>

                    <!-- Detalles secundarios -->
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <span class="text-slate-400 font-medium block">Cliente:</span>
                            <span class="font-bold text-slate-700 block truncate">
                                {{ $item->client ? $item->client->name . ' ' . $item->client->last_name : 'Cliente General' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-medium block">Atendido por:</span>
                            <span class="font-semibold text-slate-600 block truncate">
                                {{ $item->barber->user->name ?? $item->creator->name }}
                            </span>
                        </div>
                    </div>

                    <!-- Botones de Acción / Gestión -->
                    <div class="pt-2 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button wire:click="showDetails({{ $item->id }})"
                            class="p-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors"
                            title="Ver Detalles">
                            <i class="fas fa-eye text-sm"></i>
                        </button>
                        <button wire:click="deleteConfirm({{ $item->id }})"
                            class="p-2 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 transition-colors"
                            title="Anular">
                            <i class="fas fa-ban text-sm"></i>
                        </button>
                        <a href="{{ route('admin.sales.edit', Crypt::encrypt($item->id)) }}"
                            class="p-2 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-600 transition-colors"
                            title="Editar">
                            <i class="fas fa-edit text-sm"></i>
                        </a>
                        <a href="{{ route('admin.sales.ticket', Crypt::encrypt($item->id)) }}" target="_blank"
                            class="p-2 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-600 transition-colors"
                            title="Ticket">
                            <i class="fas fa-print text-sm"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="bg-white p-8 rounded-xl border border-slate-200 text-center">
                    <i class="fas fa-cash-register text-slate-200 text-3xl mb-2 block"></i>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">No se encontraron ventas
                        registradas</p>
                </div>
            @endforelse
        </div>

        <div class="datatable-container hidden md:block overflow-x-auto">
            <table class="datatable w-full">
                <thead class="datatable-thead">
                    <tr>
                        <th class="datatable-th text-right">Gestión</th>
                        <th class="datatable-th">N° Factura</th>
                        <th class="datatable-th">Cliente</th>
                        <th class="datatable-th">Atendido por</th>
                        <th class="datatable-th">Método</th>
                        <th class="datatable-th">Total Pagado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($sales as $item)
                        <tr class="datatable-tr">
                            <td class="datatable-td text-right">
                                <div class="action-group inline-flex gap-1">
                                    <button wire:click="showDetails({{ $item->id }})"
                                        class="btn-action p-1.5 rounded bg-[#f5f5f5] hover:bg-[#eeeeee]"
                                        title="Ver Detalles">
                                        <i class="fas fa-eye text-xs"></i>
                                    </button>
                                    <button wire:click="deleteConfirm({{ $item->id }})"
                                        class="btn-action p-1.5 rounded bg-[#ffebee] hover:bg-[#ffcdd2] text-[#c62828]"
                                        title="Anular">
                                        <i class="fas fa-ban text-xs"></i>
                                    </button>
                                    <a href="{{ route('admin.sales.edit', Crypt::encrypt($item->id)) }}"
                                        class="btn-action p-1.5 rounded bg-[#fff8e1] hover:bg-[#ffecb3] text-[#f57f17]"
                                        title="Editar">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>
                                    <a href="{{ route('admin.sales.ticket', Crypt::encrypt($item->id)) }}"
                                        target="_blank"
                                        class="btn-action p-1.5 rounded bg-[#e8f5e9] hover:bg-[#c8e6c9] text-[#2e7d32]"
                                        title="Ticket">
                                        <i class="fas fa-print text-xs"></i>
                                    </a>
                                </div>
                            </td>
                            <td class="datatable-td">
                                <span class="text-sm font-black text-indigo-600 cursor-pointer hover:underline"
                                    wire:click="showDetails({{ $item->id }})">
                                    {{ $item->sale_number }}
                                </span>
                            </td>
                            <td class="datatable-td">
                                <div class="text-sm font-bold text-slate-700">
                                    {{ $item->client ? $item->client->name . ' ' . $item->client->last_name : 'Cliente General' }}
                                </div>
                                <div class="text-[10px] text-slate-400 font-semibold">
                                    Cajero: {{ $item->creator->name ?? 'Sistema' }}
                                </div>
                            </td>
                            <td class="datatable-td">
                                <span
                                    class="text-xs text-slate-600 font-semibold">{{ $item->barber->user->name ?? $item->creator->name }}</span>
                            </td>
                            <td class="datatable-td">
                                <span
                                    class="px-2 py-1 text-[9px] font-black rounded uppercase {{ $item->credit > 0 ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' }}">
                                    {{ $item->credit > 0 ? 'CRÉDITO' : 'CONTADO' }}
                                </span>
                            </td>
                            <td class="datatable-td">
                                <span
                                    class="text-sm font-black text-slate-800">{{ $company->currency_code }}{{ number_format($item->total, 2) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-16 text-center">
                                <i class="fas fa-cash-register text-slate-200 text-3xl mb-3 block"></i>
                                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">No se encontraron
                                    ventas registradas</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 px-2">{{ $sales->links() }}</div>
    </div>

    @if ($viewingSale)
        <div class="modal-overlay" style="z-index: 60;">
            <div class="modal-backdrop" wire:click="closeDetails"></div>
            <div class="modal-container max-w-md">
                <div class="modal-header bg-slate-50">
                    <div class="text-left">
                        <span
                            class="text-[10px] font-black text-indigo-600 uppercase tracking-widest block leading-none">Resumen
                            de Venta</span>
                        <h3 class="modal-title text-base mt-0.5">{{ $viewingSale->sale_number }}</h3>
                    </div>
                    <button wire:click="closeDetails" class="text-slate-400 hover:text-slate-600">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
                <div class="modal-body text-xs text-slate-600">
                    <div class="space-y-1.5 border-b border-dashed border-slate-200 pb-3 mb-3">
                        <div class="flex justify-between"><strong>Fecha:</strong>
                            <span>{{ $viewingSale->created_at->format('d/m/Y H:i A') }}</span>
                        </div>
                        <div class="flex justify-between"><strong>Cliente:</strong>
                            <span>{{ $viewingSale->client->name ?? 'General' }}</span>
                        </div>
                        <div class="flex justify-between"><strong>Barbero:</strong>
                            <span>{{ $viewingSale->barber->user->name ?? 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="space-y-2 border-b border-dashed border-slate-200 pb-3 mb-3">
                        <span class="font-black text-slate-500 uppercase tracking-wide text-[10px] block">Ítems:</span>
                        @foreach ($viewingSale->items as $detail)
                            <div>
                                <div class="flex justify-between font-medium">
                                    <span>{{ $detail->quantity }}x
                                        {{ $detail->product->name ?? $detail->product_name }}</span>
                                    <span
                                        class="font-bold text-slate-700">{{ $company->currency_code }}{{ number_format($detail->total, 2) }}</span>
                                </div>

                                @if ($detail->comboItems && $detail->comboItems->count() > 0)
                                    <div class="pl-3 mt-1 space-y-1">
                                        @foreach ($detail->comboItems as $comboItem)
                                            <div class="flex justify-between text-[11px] text-slate-400">
                                                <span>- {{ (int) $comboItem->quantity }}x
                                                    {{ $comboItem->product_name ?? '' }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <div class="space-y-1.5 text-right font-bold text-slate-500">
                        <div class="flex justify-between"><span>Subtotal:</span> <span
                                class="text-slate-700">{{ $company->currency_code }}{{ number_format($viewingSale->gross_total, 2) }}</span>
                        </div>
                        <div class="flex justify-between"><span>Impuesto:</span> <span
                                class="text-slate-700">{{ $company->currency_code }}{{ number_format($viewingSale->tax_total, 2) }}</span>
                        </div>
                        <div class="flex justify-between"><span>Descuento:</span> <span
                                class="text-red-500">-{{ $company->currency_code }}{{ number_format($viewingSale->discount_total, 2) }}</span>
                        </div>
                        <div class="flex justify-between border-t pt-1.5 text-slate-800 text-sm font-black">
                            <span>Total Cobrado:</span> <span
                                class="text-indigo-600">{{ $company->currency_code }}{{ number_format($viewingSale->total, 2) }}</span>
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
                    title: '¿Anular esta Venta?',
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
