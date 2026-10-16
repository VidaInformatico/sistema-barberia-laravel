<div>
    <div class="page-container">
        <header class="page-header">
            <div class="flex items-center gap-3">
                <div class="header-icon">
                    <i class="fas fa-box-open"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black tracking-tight text-slate-800 leading-none">Mis Órdenes</h2>
                    <p class="text-slate-400 text-[11px] font-bold uppercase tracking-wider mt-1">
                        Historial de compras y seguimiento en tiempo real
                    </p>
                </div>
            </div>
        </header>

        <div class="toolbar-container">
            <div class="search-input-group relative w-full lg:flex-1">
                <input type="text" wire:model.live.debounce.500ms="search" class="search-input"
                    placeholder="Buscar por N° de orden o ID de pago...">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                    <i class="fas fa-search text-sm"></i>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-between w-full lg:w-auto gap-3">

                <div class="bg-slate-50 px-3 py-2 rounded-lg border border-slate-100 flex items-center">
                    <label
                        class="text-[10px] font-black text-slate-400 uppercase border-r border-slate-200 pr-3 mr-3 leading-none">
                        Estado
                    </label>
                    <select wire:model.live="selectedStatus"
                        class="bg-transparent border-none p-0 pr-8 text-xs font-bold text-slate-700 focus:ring-0 cursor-pointer">
                        <option value="">Todos</option>
                        @foreach ($orderStates as $state)
                            <option value="{{ $state->id }}">{{ $state->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="bg-slate-50 px-3 py-2 rounded-lg border border-slate-100 flex items-center">
                    <label
                        class="text-[10px] font-black text-slate-400 uppercase border-r border-slate-200 pr-3 mr-3 leading-none">
                        Fecha
                    </label>
                    <input type="date" wire:model.live="selectedDate"
                        class="bg-transparent border-none p-0 text-xs font-bold text-slate-700 focus:ring-0 cursor-pointer">
                </div>
            </div>
        </div>

        <!-- Vista Móvil (Tarjetas Refactorizadas) -->
        <div class="block md:hidden space-y-3">
            @forelse ($orders as $item)
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm space-y-3"
                    wire:key="order-card-{{ $item->id }}">
                    <!-- Encabezado: N° Orden, ID de Pago y Estado -->
                    <div class="flex items-start justify-between border-b border-slate-100 pb-2.5">
                        <div class="flex flex-col">
                            <span class="text-sm font-black text-slate-800">{{ $item->order_number }}</span>
                            <span class="text-[10px] text-slate-400 font-medium">
                                ID: {{ $item->payment_id ?? 'N/A' }}
                            </span>
                        </div>

                        <span
                            class="px-2.5 py-1 text-[10px] font-black rounded-full uppercase tracking-wider bg-slate-100 text-slate-700 border border-slate-200">
                            {{ $item->state?->name ?? 'Pendiente' }}
                        </span>
                    </div>

                    <!-- Detalles: Fecha, Pasarela y Total -->
                    <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-100 space-y-2 text-xs">
                        <div class="flex items-center justify-between text-slate-600">
                            <span class="flex items-center gap-1.5 font-bold">
                                <i class="far fa-calendar-alt text-slate-400"></i>
                                {{ $item->created_at->format('d/m/Y h:i A') }}
                            </span>
                            <span class="font-bold text-slate-700 uppercase flex items-center gap-1">
                                <i
                                    class="fas {{ $item->payment_gateway === 'paypal' ? 'fa-credit-card text-indigo-500' : 'fa-money-bill-wave text-emerald-500' }}"></i>
                                {{ $item->payment_gateway }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between pt-1 border-t border-slate-200/60">
                            <span class="text-slate-500 font-medium">Total de la orden:</span>
                            <span class="text-sm font-black text-slate-900">
                                {{ $company->currency_code }}{{ number_format($item->total, 2) }}
                            </span>
                        </div>
                    </div>

                    <!-- Acciones Móvil -->
                    <div class="pt-1">
                        <button wire:click="showOrder({{ $item->id }})"
                            class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-lg transition-all shadow-sm active:scale-[0.98]">
                            <i class="fas fa-truck text-xs"></i>
                            Seguimiento
                        </button>
                    </div>
                </div>
            @empty
                <div class="bg-white p-8 rounded-xl border border-slate-200 text-center">
                    <i class="fas fa-box-open text-slate-200 text-4xl mb-3 block"></i>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">
                        No se encontraron órdenes registradas
                    </p>
                </div>
            @endforelse
        </div>

        <!-- Vista Escritorio (Tabla) -->
        <div class="datatable-container hidden md:block overflow-x-auto">
            <table class="datatable w-full">
                <thead class="datatable-thead">
                    <tr>
                        <th class="datatable-th">N° Orden</th>
                        <th class="datatable-th">Fecha</th>
                        <th class="datatable-th">Pasarela</th>
                        <th class="datatable-th">Total</th>
                        <th class="datatable-th">Estado</th>
                        <th class="datatable-th text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($orders as $item)
                        <tr class="datatable-tr" wire:key="order-row-{{ $item->id }}">
                            <td class="datatable-td">
                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-slate-800">{{ $item->order_number }}</span>
                                    <span class="text-[10px] text-slate-400 font-medium">
                                        ID: {{ $item->payment_id ?? 'N/A' }}
                                    </span>
                                </div>
                            </td>
                            <td class="datatable-td">
                                <span class="text-xs font-bold text-slate-600">
                                    {{ $item->created_at->format('d/m/Y h:i A') }}
                                </span>
                            </td>
                            <td class="datatable-td">
                                <span class="text-xs font-bold uppercase text-slate-700">
                                    <i
                                        class="fas {{ $item->payment_gateway === 'paypal' ? 'fa-credit-card text-indigo-500' : 'fa-money-bill-wave text-emerald-500' }} mr-1"></i>
                                    {{ $item->payment_gateway }}
                                </span>
                            </td>
                            <td class="datatable-td">
                                <span class="text-xs font-black text-slate-900">
                                    {{ $company->currency_code }}{{ number_format($item->total, 2) }}
                                </span>
                            </td>
                            <td class="datatable-td">
                                <span
                                    class="px-2.5 py-1 text-[10px] font-black rounded-full uppercase tracking-wider bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $item->state?->name ?? 'Pendiente' }}
                                </span>
                            </td>
                            <td class="datatable-td text-right">
                                <button wire:click="showOrder({{ $item->id }})"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-lg transition-all shadow-sm active:scale-95">
                                    <i class="fas fa-truck text-[10px]"></i>
                                    Seguimiento
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-16 text-center">
                                <i class="fas fa-box-open text-slate-200 text-4xl mb-3 block"></i>
                                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">
                                    No se encontraron órdenes registradas
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if (method_exists($orders, 'links'))
            <div class="mt-4 px-2">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

    @if ($isDetailOpen && $selectedOrder)
        <div class="modal-overlay">
            <div class="modal-backdrop" wire:click="closeDetailModal"></div>

            <div class="modal-container max-w-2xl">
                {{-- Modal Header --}}
                <div class="modal-header">
                    <div class="flex items-center gap-3">
                        <div class="modal-icon-box">
                            <i class="fas fa-route"></i>
                        </div>
                        <div>
                            <h3 class="modal-title">Seguimiento de Orden</h3>
                            <p class="text-[11px] text-slate-400 font-mono leading-none mt-0.5">
                                {{ $selectedOrder->order_number }}
                            </p>
                        </div>
                    </div>
                    <button wire:click="closeDetailModal" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="modal-body space-y-6">

                    {{-- Resumen de Estado Actual --}}
                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider block">Estado
                                Actual</span>
                            <span
                                class="text-sm font-black text-slate-800">{{ $selectedOrder->state?->name ?? 'En proceso' }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider block">Monto
                                Total</span>
                            <span
                                class="text-base font-black text-emerald-600">{{ $company->currency_code }}{{ number_format($selectedOrder->total, 2) }}</span>
                        </div>
                    </div>

                    {{-- Línea de Tiempo / Tracking Historial --}}
                    <div>
                        <h4
                            class="text-xs font-black uppercase tracking-wider text-slate-400 mb-4 flex items-center gap-2">
                            <i class="fas fa-history text-slate-400"></i>
                            Historial de Estado
                        </h4>

                        <div class="relative pl-6 border-l-2 border-slate-200 space-y-6 ml-2">
                            @forelse($selectedOrder->trackings as $tracking)
                                <div class="relative group">
                                    {{-- Punto de la línea de tiempo --}}
                                    <div
                                        class="absolute -left-[31px] top-0.5 w-4 h-4 rounded-full bg-slate-900 border-2 border-white ring-4 ring-slate-100">
                                    </div>

                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                                        <span class="text-xs font-black text-slate-800">
                                            {{ $tracking->state?->name ?? 'Cambio de Estado' }}
                                        </span>
                                        <span class="text-[10px] font-bold text-slate-400">
                                            {{ $tracking->created_at->format('d/m/Y · h:i A') }}
                                        </span>
                                    </div>
                                    @if ($tracking->comment)
                                        <p
                                            class="text-xs text-slate-500 mt-1 bg-slate-50 p-2.5 rounded-lg border border-slate-100">
                                            {{ $tracking->comment }}
                                        </p>
                                    @endif
                                </div>
                            @empty
                                <p class="text-xs text-slate-400 italic">No hay historial de seguimiento disponible.
                                </p>
                            @endforelse
                        </div>
                    </div>

                    {{-- Detalle de Productos --}}
                    <div>
                        <h4
                            class="text-xs font-black uppercase tracking-wider text-slate-400 mb-3 flex items-center gap-2">
                            <i class="fas fa-shopping-bag text-slate-400"></i>
                            Productos Comprados
                        </h4>
                        <div
                            class="divide-y divide-slate-100 border border-slate-100 rounded-xl overflow-hidden bg-white">
                            @foreach ($selectedOrder->details as $detail)
                                <div
                                    class="p-3.5 flex items-center justify-between gap-3 hover:bg-slate-50 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 font-black text-xs">
                                            {{ $detail->quantity }}x
                                        </div>
                                        <div>
                                            <span
                                                class="text-xs font-bold text-slate-800 block">{{ $detail->product_name }}</span>
                                            <span class="text-[10px] text-slate-400">Precio unitario:
                                                {{ $company->currency_code }}{{ number_format($detail->price, 2) }}</span>
                                        </div>
                                    </div>
                                    <span
                                        class="text-xs font-black text-slate-900">{{ $company->currency_code }}{{ number_format($detail->total, 2) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>

                {{-- Modal Footer --}}
                <div class="modal-footer">
                    <button wire:click="closeDetailModal" type="button" class="btn-modal-secondary">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
