<div>
    <div class="page-container">
        <header class="page-header">
            <div class="flex items-center gap-3">
                <div class="header-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                <div>
                    <h2 class="text-2xl font-black tracking-tight text-slate-800 leading-none">Administrar Créditos</h2>
                    <p class="text-slate-400 text-[11px] font-bold uppercase tracking-wider mt-1">Gestión y control de
                        cuentas por cobrar</p>
                </div>
            </div>
        </header>

        <div class="toolbar-container">
            <div class="flex flex-wrap items-center justify-between w-full gap-4">
                <div class="flex flex-wrap items-center gap-3 flex-1">

                    {{-- Filtro Desde --}}
                    <div
                        class="per-page-select flex items-center bg-slate-50 px-3 py-2 rounded-lg border border-slate-100 flex-1 min-w-[140px] lg:flex-none">
                        <label
                            class="text-[10px] font-black text-slate-400 uppercase border-r border-slate-200 pr-3 mr-3 leading-none">Desde</label>
                        <div class="relative w-full flex items-center">
                            <input type="date" wire:model.live="fromDate"
                                class="bg-transparent border-none p-0 text-xs font-bold text-slate-700 focus:ring-0 cursor-pointer w-full uppercase">
                        </div>
                    </div>

                    {{-- Filtro Hasta --}}
                    <div
                        class="per-page-select flex items-center bg-slate-50 px-3 py-2 rounded-lg border border-slate-100 flex-1 min-w-[140px] lg:flex-none">
                        <label
                            class="text-[10px] font-black text-slate-400 uppercase border-r border-slate-200 pr-3 mr-3 leading-none">Hasta</label>
                        <div class="relative w-full flex items-center">
                            <input type="date" wire:model.live="toDate"
                                class="bg-transparent border-none p-0 text-xs font-bold text-slate-700 focus:ring-0 cursor-pointer w-full uppercase">
                        </div>
                    </div>

                    <div class="search-input-group relative flex-1 min-w-[180px]">
                        <input wire:model.live="search" type="text" placeholder="Buscar por proveedor..."
                            class="search-input">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                            <i class="fas fa-search text-sm"></i>
                        </div>
                    </div>

                </div>

                {{-- Botones de Exportación --}}
                {{-- @can('compras.cuentas-cobrar-reportes') --}}
                <div class="flex items-center gap-2 w-full lg:w-auto">
                    <a href="{{ route('admin.purchases.credits.export', ['type' => 'pdf', 'search' => $search, 'start_date' => $fromDate, 'end_date' => $toDate]) }}"
                        target="_blank"
                        class="btn-action bg-[#ffebee] hover:bg-[#ffcdd2] text-[#c62828] px-4 py-2 rounded-lg text-xs font-black uppercase flex items-center gap-2 flex-1 lg:flex-none justify-center">
                        <i class="fas fa-file-pdf"></i> PDF
                    </a>

                    <a href="{{ route('admin.purchases.credits.export', ['type' => 'excel', 'search' => $search, 'start_date' => $fromDate, 'end_date' => $toDate]) }}"
                        target="_blank"
                        class="btn-action bg-[#e8f5e9] hover:bg-[#c8e6c9] text-[#2e7d32] px-4 py-2 rounded-lg text-xs font-black uppercase flex items-center gap-2 flex-1 lg:flex-none justify-center">
                        <i class="fas fa-file-excel"></i> EXCEL
                    </a>
                </div>
                {{-- @endcan --}}

            </div>
        </div>

        <!-- Vista Móvil (Tarjetas Refactorizadas) -->
        <div class="block md:hidden space-y-3">
            @forelse ($purchases as $item)
                @php
                    $totalAbonado = $item->payments->sum('amount');
                    $pendiente = $item->total - $totalAbonado;
                @endphp
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm space-y-3"
                    wire:key="purchase-card-{{ $item->id }}">
                    <!-- Encabezado: Folio + Estado de Pago -->
                    <div class="flex items-start justify-between border-b border-slate-100 pb-2.5">
                        <div class="flex flex-col">
                            <span class="text-sm font-black text-slate-800">Compra #{{ $item->id }}</span>
                            <span class="text-[10px] text-slate-400 font-semibold mt-0.5">
                                <i class="far fa-clock mr-1"></i>{{ $item->created_at->format('d/m/Y H:i') }}
                            </span>
                        </div>

                        <div>
                            @if ($pendiente > 0)
                                <span
                                    class="px-2.5 py-0.5 text-[10px] font-black bg-amber-100 text-amber-700 rounded-full uppercase">
                                    Pendiente
                                </span>
                            @else
                                <span
                                    class="px-2.5 py-0.5 text-[10px] font-black bg-emerald-100 text-emerald-700 rounded-full uppercase">
                                    Pagado
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
                            <span>Registrado por: {{ $item->user->name ?? 'Sistema' }}</span>
                        </div>
                    </div>

                    <!-- Desglose Financiero -->
                    <div
                        class="grid grid-cols-3 gap-2 bg-slate-50 p-2.5 rounded-lg border border-slate-100 text-xs text-center">
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Total</span>
                            <span class="font-black text-slate-800 text-sm mt-0.5 block">
                                {{ $company->currency_code }}{{ number_format($item->total, 2) }}
                            </span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Abonado</span>
                            <span class="font-bold text-emerald-600 mt-0.5 block">
                                {{ $company->currency_code }}{{ number_format($totalAbonado, 2) }}
                            </span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Pendiente</span>
                            <span
                                class="font-bold {{ $pendiente > 0 ? 'text-amber-600' : 'text-slate-400' }} mt-0.5 block">
                                {{ $company->currency_code }}{{ number_format($pendiente, 2) }}
                            </span>
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="flex items-center justify-end gap-2 pt-1 border-t border-slate-50">
                        {{-- @can('compras.agregar-pago') --}}
                        <button wire:click="openPaymentModal({{ $item->id }})"
                            class="p-2 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 transition-colors"
                            title="Abonar">
                            <i class="fas fa-hand-holding-dollar text-sm"></i>
                        </button>
                        {{-- @endcan --}}

                        {{-- @can('compras.historial-pagos') --}}
                        <a href="{{ route('admin.purchases.payments', ['id' => Crypt::encrypt($item->id)]) }}"
                            class="p-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors"
                            title="Ver Detalles">
                            <i class="fas fa-eye text-sm"></i>
                        </a>
                        {{-- @endcan --}}

                        <a href="{{ route('admin.purchases.ticket', Crypt::encrypt($item->id)) }}" target="_blank"
                            class="p-2 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-600 transition-colors"
                            title="Imprimir Ticket">
                            <i class="fas fa-print text-sm"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="bg-white p-8 rounded-xl border border-slate-200 text-center">
                    <i class="fas fa-file-invoice-dollar text-slate-200 text-3xl mb-3 block"></i>
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
                        <th class="datatable-th text-center w-28">Gestión</th>
                        <th class="datatable-th">Compra / Info</th>
                        <th class="datatable-th">Fecha</th>
                        <th class="datatable-th">Pagos / Pendiente</th>
                        <th class="datatable-th text-right">Total</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-50">
                    @forelse ($purchases as $item)
                        @php
                            $totalAbonado = $item->payments->sum('amount');
                            $pendiente = $item->total - $totalAbonado;
                        @endphp
                        <tr class="datatable-tr" wire:key="purchase-row-{{ $item->id }}">
                            <td class="datatable-td text-center">
                                <div class="action-group inline-flex">
                                    {{-- @can('compras.agregar-pago') --}}
                                    <a href="#" wire:click="openPaymentModal({{ $item->id }})"
                                        class="btn-action bg-[#e3f2fd] hover:bg-[#bbdefb] text-[#0d47a1] border-r"
                                        title="Abonar">
                                        <i class="fas fa-hand-holding-dollar text-xs"></i>
                                    </a>
                                    {{-- @endcan --}}

                                    {{-- @can('compras.historial-pagos') --}}
                                    <a href="{{ route('admin.purchases.payments', ['id' => Crypt::encrypt($item->id)]) }}"
                                        class="btn-action bg-[#f5f5f5] hover:bg-[#eeeeee] border-r"
                                        title="Ver Detalles">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    {{-- @endcan --}}

                                    <a href="{{ route('admin.purchases.ticket', Crypt::encrypt($item->id)) }}"
                                        target="_blank"
                                        class="btn-action bg-[#e8f5e9] hover:bg-[#c8e6c9] text-[#2e7d32]"
                                        title="Imprimir Ticket">
                                        <i class="fas fa-print text-xs"></i>
                                    </a>
                                </div>
                            </td>
                            <td class="datatable-td">
                                <div class="flex flex-col">
                                    <span class="font-black text-slate-800 text-sm">
                                        #{{ $item->id }} -
                                        {{ $item->supplier ? $item->supplier->company_name : 'Proveedor General' }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 font-semibold">
                                        Cajero: {{ $item->user->name ?? 'Sistema' }}
                                    </span>
                                </div>
                            </td>
                            <td class="datatable-td">
                                <span class="text-xs text-slate-600 font-semibold">
                                    {{ $item->created_at->format('d/m/Y H:i') }}
                                </span>
                            </td>
                            <td class="datatable-td">
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-bold text-emerald-600">
                                        Abonado: {{ $company->currency_code }}{{ number_format($totalAbonado, 2) }}
                                    </span>
                                    <span
                                        class="text-[10px] font-bold {{ $pendiente > 0 ? 'text-amber-600' : 'text-slate-400' }}">
                                        Pendiente: {{ $company->currency_code }}{{ number_format($pendiente, 2) }}
                                    </span>
                                </div>
                            </td>
                            <td class="datatable-td text-right">
                                <span class="text-sm font-black text-slate-800">
                                    {{ $company->currency_code }}{{ number_format($item->total, 2) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-16 text-center">
                                <i class="fas fa-file-invoice-dollar text-slate-200 text-3xl mb-3 block"></i>
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

    @if ($showPaymentModal && $purchase)
        <div class="modal-overlay">
            <div class="modal-backdrop" wire:click="$set('showPaymentModal', false)"></div>
            <div class="modal-container max-w-lg">

                <div class="modal-header">
                    <div>
                        <h3 class="modal-title">Resumen de Cobro</h3>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Proveedor:
                            {{ $purchase->supplier->company_name ?? 'Invitado' }}</p>
                    </div>
                    <button wire:click="$set('showPaymentModal', false)">
                        <i class="fas fa-times text-slate-400 hover:text-slate-600 transition-colors"></i>
                    </button>
                </div>

                <div
                    class="px-6 py-4 grid grid-cols-3 gap-4 text-center bg-slate-50/50 border-b border-slate-100 flex-shrink-0">
                    <div>
                        <p class="text-[9px] uppercase font-black text-slate-400 tracking-wider">Total</p>
                        <p class="text-sm font-black text-slate-700 mt-0.5">{{ number_format($purchase->total, 2) }}
                        </p>
                    </div>
                    <div>
                        <p class="text-[9px] uppercase font-black text-slate-400 tracking-wider">Abonado</p>
                        <p class="text-sm font-black text-emerald-600 mt-0.5">
                            {{ number_format($purchase->payments->sum('amount'), 2) }}</p>
                    </div>
                    <div>
                        <p class="text-[9px] uppercase font-black text-slate-400 tracking-wider">Pendiente</p>
                        <p class="text-sm font-black text-orange-600 mt-0.5">
                            {{ number_format($this->pendingAmount, 2) }}</p>
                    </div>
                </div>

                <div class="modal-body space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="modal-label">Fecha de Pago</label>
                            <div class="modal-input-group">
                                <i class="fas fa-calendar-alt modal-input-icon"></i>
                                <input type="date" wire:model="payment_date"
                                    class="modal-input @error('payment_date') border-rose-500 focus:ring-rose-500/20 @enderror">
                            </div>
                            @error('payment_date')
                                <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="modal-label">Método de Pago</label>
                            <div class="modal-input-group">
                                <i class="fas fa-wallet modal-input-icon"></i>
                                <select wire:model="payment_method_id"
                                    class="modal-input appearance-none @error('payment_method_id') border-rose-500 focus:ring-rose-500/20 @enderror">
                                    <option value="">Método de pago...</option>
                                    @foreach ($paymentMethods as $method)
                                        <option value="{{ $method->id }}">{{ $method->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('payment_method_id')
                                <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="modal-label">Monto a Abonar</label>
                        <div class="modal-input-group">
                            <i class="fas fa-money-bill-wave modal-input-icon"></i>
                            <input type="number" step="any" wire:model="amount"
                                class="modal-input font-black text-slate-700 placeholder:font-normal @error('amount') border-rose-500 focus:ring-rose-500/20 @enderror"
                                placeholder="0.00">
                        </div>
                        @error('amount')
                            <span class="text-rose-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>


                    <div>
                        <label class="modal-label">Referencia (Opcional)</label>
                        <div class="modal-input-group">
                            <i class="fas fa-receipt modal-input-icon"></i>
                            <input type="text" wire:model="reference" class="modal-input"
                                placeholder="Ej. Número de operación, cheque...">
                        </div>
                    </div>

                    {{-- Nota Adicional --}}
                    <div>
                        <label class="modal-label">Nota Adicional</label>
                        <div class="modal-input-group">
                            <i class="fas fa-sticky-note modal-input-icon mt-3"></i>
                            <textarea wire:model="note" rows="2" class="modal-input min-h-[60px]"
                                placeholder="Observaciones sobre este cobro..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button wire:click="$set('showPaymentModal', false)" class="btn-modal-secondary">Cancelar</button>
                    <button wire:click="savePayment" class="btn-modal-primary">
                        Registrar Pago
                    </button>
                </div>

            </div>
        </div>
    @endif
</div>
