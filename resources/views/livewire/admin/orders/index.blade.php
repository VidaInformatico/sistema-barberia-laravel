<div>
    <div class="page-container">
        <header class="page-header">
            <div class="flex items-center gap-3">
                <div class="header-icon">
                    <i class="fas fa-box-open"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black tracking-tight text-slate-800 leading-none">Mis Órdenes</h2>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.orders.export', ['type' => 'pdf', 'search' => $search, 'selectedStatus' => $selectedStatus, 'selectedDate' => $selectedDate]) }}" target="_blank" class="bg-rose-50 text-rose-600 px-3 py-1.5 rounded-lg text-xs font-bold uppercase border border-rose-200">
                    <i class="fas fa-file-pdf"></i> PDF
                </a>
                <a href="{{ route('admin.orders.export', ['type' => 'excel', 'search' => $search, 'selectedStatus' => $selectedStatus, 'selectedDate' => $selectedDate]) }}" target="_blank" class="bg-emerald-50 text-emerald-600 px-3 py-1.5 rounded-lg text-xs font-bold uppercase border border-emerald-200">
                    <i class="fas fa-file-excel"></i> Excel
                </a>
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

        <!-- Vista Móvil (Tarjetas) -->
        <div class="block md:hidden space-y-3">
            @forelse ($orders as $item)
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm space-y-3"
                    wire:key="order-card-{{ $item->id }}">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                        <div class="flex flex-col pr-2">
                            <span
                                class="text-sm font-black text-slate-800 leading-tight">{{ $item->order_number }}</span>
                            <span class="text-[10px] text-slate-400 font-medium">ID:
                                {{ $item->payment_id ?? 'N/A' }}</span>
                        </div>
                        <div class="text-right shrink-0">
                            <span
                                class="text-xs font-black text-slate-900 block">{{ $company->currency_code }}{{ number_format($item->total, 2) }}</span>
                            <span
                                class="px-2 py-0.5 text-[9px] font-black rounded-full uppercase tracking-wider bg-slate-100 text-slate-700 border border-slate-200 inline-block mt-1">
                                {{ $item->state?->name ?? 'Pendiente' }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <span class="text-slate-400 font-medium block">Pasarela:</span>
                            <span class="text-xs font-bold uppercase text-slate-700 block mt-0.5">
                                <i
                                    class="fas {{ $item->payment_gateway === 'paypal' ? 'fa-credit-card text-indigo-500' : 'fa-money-bill-wave text-emerald-500' }} mr-1"></i>
                                {{ $item->payment_gateway }}
                            </span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-medium block">Fecha:</span>
                            <span class="text-xs font-bold text-slate-600 block mt-0.5">
                                <i class="far fa-calendar-alt mr-1 text-slate-400"></i>
                                {{ $item->created_at->format('d/m/Y h:i A') }}
                            </span>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-slate-100 flex items-center justify-end">
                        <button wire:click="showOrder({{ $item->id }})"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-lg transition-all shadow-sm active:scale-95">
                            <i class="fas fa-truck text-[10px]"></i>
                            Seguimiento
                        </button>
                    </div>
                </div>
            @empty
                <div class="bg-white p-8 rounded-xl border border-slate-200 text-center">
                    <i class="fas fa-box-open text-slate-200 text-4xl mb-3 block"></i>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">No se encontraron órdenes
                        registradas</p>
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
                                    <span class="text-[10px] text-slate-400 font-medium">ID:
                                        {{ $item->payment_id ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="datatable-td">
                                <span
                                    class="text-xs font-bold text-slate-600">{{ $item->created_at->format('d/m/Y h:i A') }}</span>
                            </td>
                            <td class="datatable-td">
                                <span class="text-xs font-bold uppercase text-slate-700">
                                    <i
                                        class="fas {{ $item->payment_gateway === 'paypal' ? 'fa-credit-card text-indigo-500' : 'fa-money-bill-wave text-emerald-500' }} mr-1"></i>
                                    {{ $item->payment_gateway }}
                                </span>
                            </td>
                            <td class="datatable-td">
                                <span
                                    class="text-xs font-black text-slate-900">{{ $company->currency_code }}{{ number_format($item->total, 2) }}</span>
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
                                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">No se encontraron
                                    órdenes registradas</p>
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
                            <span class="text-sm font-black text-slate-800"
                                style="color: {{ $selectedOrder->state?->color ?? '#1e293b' }}">
                                <i class="fas fa-circle text-[10px] mr-1"
                                    style="color: {{ $selectedOrder->state?->color ?? '#64748b' }}"></i>
                                {{ $selectedOrder->state?->name ?? 'Sin estado' }}
                            </span>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider block">Monto
                                Total</span>
                            <span
                                class="text-base font-black text-emerald-600">{{ $company->currency_code }}{{ number_format($selectedOrder->total, 2) }}</span>
                        </div>
                    </div>

                    {{-- Formulario para Agregar Nuevo Seguimiento --}}
                    <div class="bg-indigo-50/50 border border-indigo-100 rounded-xl p-4">
                        <h4
                            class="text-xs font-black uppercase tracking-wider text-indigo-900 mb-3 flex items-center gap-2">
                            <i class="fas fa-plus-circle text-indigo-600"></i>
                            Actualizar Estado / Registrar Seguimiento
                        </h4>

                        <div class="grid grid-cols-1 gap-3">
                            <div>
                                <label class="modal-label">Nuevo Estado</label>
                                <select wire:model="new_state_id" class="modal-input bg-white">
                                    <option value="">-- Seleccionar Estado --</option>
                                    @foreach ($orderStates as $st)
                                        <option value="{{ $st->id }}">{{ $st->name }}</option>
                                    @endforeach
                                </select>
                                @error('new_state_id')
                                    <span class="text-red-500 text-[10px] font-bold block mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="modal-label">Nota o Comentario (Opcional)</label>
                                <textarea wire:model="tracking_comment" rows="2" class="modal-input bg-white min-h-[70px]"
                                    placeholder="Ej: El paquete fue entregado a la paquetería o N° de guía..."></textarea>
                                @error('tracking_comment')
                                    <span class="text-red-500 text-[10px] font-bold block mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="flex justify-end pt-1">
                                <button wire:click="addTracking" wire:loading.attr="disabled"
                                    class="btn-primary py-2 text-xs">
                                    <span wire:loading.remove wire:target="addTracking">
                                        <i class="fas fa-save mr-1"></i> Guardar Seguimiento
                                    </span>
                                    <span wire:loading wire:target="addTracking">
                                        Guardando...
                                    </span>
                                </button>
                            </div>
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
                                    {{-- Punto de la línea de tiempo con el color de cada estado --}}
                                    <div class="absolute -left-[31px] top-0.5 w-4 h-4 rounded-full border-2 border-white ring-4 ring-slate-100"
                                        style="background-color: {{ $tracking->state?->color ?? '#0f172a' }};">
                                    </div>

                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1">
                                        <span class="text-xs font-black text-slate-800">
                                            {{ $tracking->state?->name ?? 'Cambio de Estado' }}
                                            @if ($tracking->user)
                                                <span class="text-[10px] text-slate-400 font-normal">por
                                                    {{ $tracking->user->name }}</span>
                                            @endif
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

                <div class="modal-footer">
                    <button wire:click="closeDetailModal" type="button" class="btn-modal-secondary">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
