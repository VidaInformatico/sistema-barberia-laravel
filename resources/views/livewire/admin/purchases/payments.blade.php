<div class="page-container">
    <header class="page-header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.purchases.credit') }}" class="text-slate-400 hover:text-slate-800 transition-colors mr-1">
                <i class="fas fa-arrow-left text-lg"></i>
            </a>
            <div class="header-icon"><i class="fas fa-money-bill-wave"></i></div>
            <div>
                <h2 class="text-2xl font-black tracking-tight text-slate-800 leading-none">Administrar Pagos</h2>
                <p class="text-slate-400 text-[11px] font-bold uppercase tracking-wider mt-1">Historial y conciliación de transacciones</p>
            </div>
        </div>
    </header>

    <div class="toolbar-container">
        <div class="flex flex-wrap items-center justify-between w-full gap-4">
            <div class="flex flex-wrap items-center gap-3 flex-1">
                
                {{-- Filtro Desde --}}
                <div class="per-page-select flex items-center bg-slate-50 px-3 py-2 rounded-lg border border-slate-100 flex-1 min-w-[140px] lg:flex-none">
                    <label class="text-[10px] font-black text-slate-400 uppercase border-r border-slate-200 pr-3 mr-3 leading-none">Desde</label>
                    <div class="relative w-full flex items-center">
                        <input type="date" wire:model.live="fromDate"
                            class="bg-transparent border-none p-0 text-xs font-bold text-slate-700 focus:ring-0 cursor-pointer w-full uppercase">
                    </div>
                </div>

                {{-- Filtro Hasta --}}
                <div class="per-page-select flex items-center bg-slate-50 px-3 py-2 rounded-lg border border-slate-100 flex-1 min-w-[140px] lg:flex-none">
                    <label class="text-[10px] font-black text-slate-400 uppercase border-r border-slate-200 pr-3 mr-3 leading-none">Hasta</label>
                    <div class="relative w-full flex items-center">
                        <input type="date" wire:model.live="toDate"
                            class="bg-transparent border-none p-0 text-xs font-bold text-slate-700 focus:ring-0 cursor-pointer w-full uppercase">
                    </div>
                </div>

                <div class="search-input-group relative flex-1 min-w-[180px]">
                    <input wire:model.live="search" type="text" placeholder="Buscar..."
                        class="search-input">
                    <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                        <i class="fas fa-search text-sm"></i>
                    </div>
                </div>

            </div>

            {{-- Botones de Exportación --}}
            {{-- @can('compras.historial-pagos-reportes') --}}
                <div class="flex items-center gap-2 w-full lg:w-auto">
                    <a href="{{ route('admin.purchases.payments.export', ['type' => 'pdf', 'search' => $search, 'from' => $fromDate, 'to' => $toDate]) }}"
                        target="_blank"
                        class="btn-action bg-[#ffebee] hover:bg-[#ffcdd2] text-[#c62828] px-4 py-2 rounded-lg text-xs font-black uppercase flex items-center gap-2 flex-1 lg:flex-none justify-center">
                        <i class="fas fa-file-pdf"></i> PDF
                    </a>

                    <a href="{{ route('admin.purchases.payments.export', ['type' => 'excel', 'search' => $search, 'from' => $fromDate, 'to' => $toDate]) }}"
                        target="_blank"
                        class="btn-action bg-[#e8f5e9] hover:bg-[#c8e6c9] text-[#2e7d32] px-4 py-2 rounded-lg text-xs font-black uppercase flex items-center gap-2 flex-1 lg:flex-none justify-center">
                        <i class="fas fa-file-excel"></i> EXCEL
                    </a>
                </div>
            {{-- @endcan --}}

        </div>
    </div>

    <div class="datatable-container">
        <div class="hidden md:block">
            <table class="datatable">
                <thead class="datatable-thead">
                    <tr>
                        <th class="datatable-th">Info</th>
                        <th class="datatable-th">Método</th>
                        <th class="datatable-th">Monto</th>
                        <th class="datatable-th">Fecha</th>
                        <th class="datatable-th text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($payments as $payment)
                        <tr class="datatable-tr">
                            <td class="datatable-td">
                                <span class="font-black text-slate-800 text-sm">
                                    {{ isset($payment->paymentable->supplier) 
                                        ? trim(($payment->paymentable->supplier->company_name ?? '')) 
                                        : 'Proveedor Contado' }}
                                </span>
                            </td>
                            <td class="datatable-td">
                                <span class="text-xs text-slate-600 font-semibold uppercase">{{ $payment->method->name }}</span>
                            </td>
                            <td class="datatable-td">
                                <span class="text-sm font-black text-emerald-600">
                                    {{ $company->currency_code }}{{ number_format($payment->amount, 2) }}
                                </span>
                            </td>
                            <td class="datatable-td">
                                <span class="text-xs text-slate-600 font-semibold">{{ $payment->created_at->format('d/m/Y H:i') }}</span>
                            </td>
                            <td class="datatable-td text-right">
                                <div class="action-group inline-flex">
                                    <a href="{{ route('admin.purchases.payment.receipt', Crypt::encryptString($payment->id)) }}"
                                        target="_blank" 
                                        class="btn-action bg-[#e3f2fd] hover:bg-[#bbdefb] text-[#0d47a1] border-r"
                                        title="Ver PDF">
                                        <i class="fas fa-file-pdf text-xs"></i>
                                    </a>
                                    {{-- @can('compras.pagos-eliminar') --}}
                                        <button wire:click="deletePayment({{ $payment->id }})"
                                            class="btn-action bg-[#ffebee] hover:bg-[#ffcdd2] text-[#c62828]"
                                            title="Eliminar">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    {{-- @endcan --}}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-16 text-center">
                                <i class="fas fa-money-bill-wave text-slate-200 text-3xl mb-3 block"></i>
                                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">No se encontraron pagos registrados</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- VISTA MOBILE ADAPTADA AL NUEVO DISEÑO --}}
        <div class="md:hidden divide-y divide-slate-100">
            @foreach ($payments as $payment)
                <div class="p-4 flex flex-col gap-3">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="font-black text-slate-800 text-sm">
                                {{ isset($payment->paymentable->supplier) 
                                    ? trim(($payment->paymentable->supplier->company_name ?? '')) 
                                    : 'Proveedor Contado' }}
                            </div>
                            <div class="text-[10px] text-slate-400 font-black uppercase mt-0.5">{{ $payment->method->name }}</div>
                        </div>
                        <div class="text-right">
                            <span class="font-black text-emerald-600 text-sm">
                                {{ $company->currency_code }}{{ number_format($payment->amount, 2) }}
                            </span>
                        </div>
                    </div>

                    <div class="flex justify-between items-center text-[10px] bg-slate-50 px-3 py-2 rounded-lg border border-slate-100">
                        <span class="font-bold text-slate-500">Fecha: <b class="text-slate-700">{{ $payment->created_at->format('d/m/Y H:i') }}</b></span>
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('admin.purchases.payment.receipt', Crypt::encryptString($payment->id)) }}" target="_blank"
                            class="flex-1 bg-slate-800 text-white py-2 rounded-lg text-xs font-bold text-center shadow-sm hover:bg-slate-900 transition-colors">
                            <i class="fas fa-file-pdf mr-1"></i> Comprobante
                        </a>

                        {{-- @can('compras.pagos-eliminar') --}}
                            <button type="button" wire:click="deletePayment({{ $payment->id }})"
                                class="flex-1 bg-[#ffebee] hover:bg-[#ffcdd2] text-[#c62828] py-2 rounded-lg text-xs font-bold shadow-sm transition-colors">
                                <i class="fas fa-trash mr-1"></i> Eliminar
                            </button>
                        {{-- @endcan --}}
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4 px-2">
            {{ $payments->links() }}
        </div>
    </div>
</div>