<div class="page-container bg-white">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-8 space-y-4">

            <header class="page-header border-b border-slate-100 pb-4 mb-4">
                <div class="flex items-center gap-3">
                    <div>
                        <h2 class="text-2xl font-black tracking-tight text-slate-800 leading-none">Nueva Operación de
                            Caja</h2>
                        <p class="text-indigo-600 text-xs font-black uppercase tracking-wider mt-1">Terminal de
                            Facturación:
                            {{ $sale_number }}
                        </p>
                        @error('sale_number')
                            <span class="text-red-500 text-[10px] font-bold block mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </header>

            <div class="bg-slate-50 p-4 border border-slate-100 relative">
                <label class="modal-label font-black text-slate-700">Identificar Cliente</label>
                <div class="bg-white border border-slate-200 p-4 rounded-sm shadow-sm">
                    <h2
                        class="text-xs font-bold uppercase tracking-widest text-amber-600 mb-3 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span
                                class="w-5 h-5 bg-amber-50 text-amber-600 flex items-center justify-center text-[10px] font-mono border border-amber-200">01</span>
                            Asignar Cliente
                        </div>
                    </h2>

                    <div class="w-full">
                        @if ($selectedClient)
                            <div
                                class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3 bg-slate-50 border border-slate-200 rounded-sm shadow-xs transition-all">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div
                                        class="w-9 h-9 shrink-0 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-xs">
                                        {{ strtoupper(substr($selectedClient->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="text-xs font-bold text-slate-800 truncate">
                                            {{ $selectedClient->name }} {{ $selectedClient->last_name ?? '' }}
                                        </h4>
                                        <p
                                            class="text-[11px] text-slate-500 flex flex-wrap items-center gap-x-2 gap-y-0.5 mt-0.5">
                                            <span class="truncate"><i class="fas fa-envelope mr-1 text-slate-400"></i>
                                                {{ $selectedClient->email ?? 'Sin Correo' }}</span>
                                            <span class="hidden sm:inline">•</span>
                                            <span class="truncate"><i class="fas fa-phone mr-1 text-slate-400"></i>
                                                {{ $selectedClient->phone ?? 'Sin Teléfono' }}</span>
                                        </p>
                                    </div>
                                </div>

                                <button type="button" wire:click="removeClient"
                                    class="self-end sm:self-center text-slate-400 hover:text-red-600 p-1.5 rounded-full hover:bg-red-50 transition-colors shrink-0"
                                    title="Quitar cliente">
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                            </div>
                        @else
                            <div
                                class="flex items-center justify-between p-3 bg-slate-50 border border-dashed border-slate-300 rounded-sm hover:border-amber-500 transition-all">
                                <div class="flex items-center gap-2.5 text-slate-500">
                                    <div
                                        class="w-8 h-8 rounded-full bg-slate-200/60 flex items-center justify-center text-slate-500">
                                        <i class="fas fa-user text-xs"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-slate-700">Ningún cliente seleccionado</p>
                                        <p class="text-[11px] text-slate-400">Selecciona o registra un cliente para la
                                            venta
                                        </p>
                                    </div>
                                </div>

                                <button type="button" wire:click="$dispatch('openClientModal')"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold rounded shadow-sm transition-colors shrink-0">
                                    <i class="fas fa-user-plus text-[11px]"></i>
                                    <span>Elegir Cliente</span>
                                </button>
                            </div>
                        @endif

                        @error('clientId')
                            <span
                                class="text-xs text-red-600 mt-2 block font-semibold uppercase tracking-wider">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="bg-slate-50 p-4 border border-slate-100 relative">
                <div class="flex justify-between items-center mb-2">
                    <label class="modal-label font-black text-slate-700">2. Escanear o Buscar Ítems</label>

                    <button type="button" wire:click="toggleSearchMode"
                        class="text-xs font-bold px-2.5 py-1 rounded-md transition-colors flex items-center gap-1.5 {{ $searchMode === 'barcode' ? 'bg-indigo-100 text-indigo-700' : 'bg-emerald-100 text-emerald-700' }}">
                        @if ($searchMode === 'barcode')
                            <i class="fas fa-barcode"></i> Modo Lector
                        @else
                            <i class="fas fa-search"></i> Modo Búsqueda
                        @endif
                    </button>
                </div>

                <div class="relative mt-1">
                    <input type="text" id="searchProduct"
                        @if ($searchMode === 'search') wire:model.live.debounce.300ms="searchProduct" @else wire:model="searchProduct" @endif
                        wire:keydown.enter.prevent="searchByBarcode" class="modal-input pl-10"
                        placeholder="{{ $searchMode === 'barcode' ? 'Escanee el código de barras...' : 'Escriba el nombre del producto...' }}"
                        autofocus>

                    <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                        <i class="fas {{ $searchMode === 'barcode' ? 'fa-barcode' : 'fa-search' }} text-xs"></i>
                    </div>
                </div>

                @if ($searchMode === 'search' && !empty($searchProduct))
                    <div
                        class="absolute z-40 left-4 right-4 mt-1 bg-white border border-slate-200 rounded-lg shadow-xl divide-y divide-slate-100 overflow-hidden">
                        @forelse ($productResults as $p)
                            <div wire:click="addProductToCart({{ $p['id'] }})"
                                class="p-3 hover:bg-indigo-50 cursor-pointer flex justify-between items-center transition-colors">
                                <span class="font-bold text-slate-700 text-xs">
                                    <i class="fas fa-plus-circle text-indigo-500 mr-2"></i>{{ $p['name'] }}
                                    @if (!empty($p['barcode']))
                                        <span class="text-[10px] text-slate-400 ml-2">({{ $p['barcode'] }})</span>
                                    @endif
                                </span>
                                <span class="text-xs font-black text-indigo-600">
                                    {{ $company->currency_code }}{{ number_format($p['price'], 2) }}
                                </span>
                            </div>
                        @empty
                            <div class="p-3 text-slate-500 text-xs text-center">
                                <i class="fas fa-info-circle mr-2"></i>No se encontraron productos.
                            </div>
                        @endforelse
                    </div>
                @endif
            </div>

            <div class="border border-slate-100 overflow-x-auto">
                <table class="w-full min-w-[600px] text-left border-collapse">
                    <thead class="bg-slate-100 text-[10px] uppercase font-black text-slate-500 tracking-wider">
                        <tr>
                            <th class="p-3">Producto</th>
                            <th class="p-3 text-center w-28">Cant.</th>
                            <th class="p-3 w-32">Pre. Unit.</th>
                            <th class="p-3 text-center w-28">Desc(%).</th>
                            <th class="p-3 w-32">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @forelse($items as $index => $item)
                            <tr class="hover:bg-slate-50 font-medium">
                                <td class="p-3 font-bold text-slate-700 flex flex-col gap-1">
                                    <div class="flex items-center gap-3">
                                        <button type="button" wire:click="removeItem({{ $index }})"
                                            class="text-red-400 hover:text-red-600 transition-colors">
                                            <i class="fas fa-minus-circle text-base"></i>
                                        </button>
                                        <span>{{ $item['name'] }}</span>
                                        @if (isset($item['type']) && $item['type'] === 'combo')
                                            <span
                                                class="px-1.5 py-0.5 bg-indigo-100 text-indigo-700 rounded text-[9px] uppercase font-black">Combo</span>
                                        @endif
                                    </div>

                                    {{-- Listado anidado de componentes del combo --}}
                                    @if (isset($item['combo_components']) && !empty($item['combo_components']))
                                        <div class="ml-8 mt-1 space-y-0.5 border-l-2 border-slate-200 pl-3">
                                            @foreach ($item['combo_components'] as $comp)
                                                <p class="text-[9px] text-slate-400 font-normal">
                                                    <i class="fas fa-arrow-turn-up fa-rotate-90 mr-1"></i>
                                                    {{ $comp['quantity'] }}x {{ $comp['name'] }}
                                                </p>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td class="p-3 text-center">
                                    <input type="number" value="{{ $item['quantity'] }}"
                                        wire:change="updateQuantity({{ $index }}, $event.target.value)"
                                        class="w-16 px-1 text-xs text-center border border-slate-200 rounded-md font-bold focus:ring-0">
                                </td>
                                <td class="p-3 text-slate-500">{{ number_format($item['price'], 2) }}</td>
                                <td class="p-3 text-center">
                                    <input type="number" value="{{ $item['discount_rate'] }}"
                                        wire:change="updateDiscount({{ $index }}, $event.target.value)"
                                        class="w-16 px-1 text-xs text-center border border-slate-200 rounded-md font-bold focus:ring-0">
                                </td>
                                <td class="p-3 font-black text-slate-800">
                                    {{ number_format($item['gross_subtotal'], 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5"
                                    class="py-12 text-center text-slate-400 uppercase font-black tracking-widest text-[10px]">
                                    <i class="fas fa-shopping-basket text-2xl block mb-2 text-slate-200"></i>El carrito
                                    está vacío
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @error('items')
                <span class="text-red-500 text-[10px] font-bold block">{{ $message }}</span>
            @enderror
        </div>

        <div class="lg:col-span-4 space-y-4">
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 space-y-4">
                <h3 class="text-xs font-black uppercase text-slate-500 tracking-wider border-b pb-2">3. Liquidación y
                    Cierre
                </h3>

                <div>
                    <label class="modal-label">Asignar Barbero / Estilista</label>
                    <div class="relative">
                        <span
                            class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400 text-xs">
                            <i class="fa-solid fa-scissors"></i>
                        </span>
                        <select wire:model="barber_id" class="modal-input bg-none text-xs pl-8">
                            <option value="">-- Atendido por --</option>
                            @foreach ($barbers as $b)
                                <option value="{{ $b->id }}">{{ $b->user->name ?? '' }}
                                    {{ $b->user->last_name ?? '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('barber_id')
                        <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="modal-label">Método</label>
                    <div class="relative">
                        <span
                            class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400 text-xs">
                            <i class="fa-solid fa-file-invoice-dollar"></i>
                        </span>
                        <select wire:model="method" class="modal-input bg-none text-xs pl-8">
                            <option value="CONTADO">CONTADO</option>
                            <option value="CREDITO">CRÉDITO</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="modal-label">Forma de Pago</label>
                    <div class="relative">
                        <span
                            class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400 text-xs">
                            <i class="fa-solid fa-wallet"></i>
                        </span>
                        <select wire:model="selectedMethod" wire:change="addPaymentFromSelect"
                            class="modal-input bg-none text-xs pl-8">
                            <option value="">Seleccione...</option>
                            @foreach ($paymentMethods as $method)
                                @php $used = collect($payments)->pluck('method_id')->contains($method->id); @endphp
                                <option value="{{ $method->id }}" @if ($used) disabled @endif>
                                    {{ $method->name }} {{ $method->is_efectivo ? '(Efectivo)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <table class="w-full">

                        <thead>
                            <tr
                                class="text-[9px] uppercase font-black text-slate-400 tracking-tighter border-b border-slate-100">
                                <th class="pb-2 text-left px-2">Detalle</th>
                                <th class="pb-2 text-right px-2">Monto</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-50">
                            @foreach ($payments as $index => $payment)
                                @php $mInfo = collect($paymentMethods)->firstWhere('id', $payment['method_id']); @endphp

                                <tr class="group">
                                    <td class="py-3 px-2">
                                        <button wire:click="removePaymentRow({{ $index }})"
                                            class="text-slate-400 hover:text-rose-500 transition-colors p-1">
                                            <i class="fas fa-times-circle"></i>
                                        </button>
                                        <!-- Texto del nombre reducido de text-sm a text-xs -->
                                        <div class="text-xs font-bold text-slate-700">{{ $mInfo->name ?? '-' }}
                                        </div>
                                        <input type="text"
                                            wire:model.live="payments.{{ $index }}.reference"
                                            placeholder="Referencia..."
                                            class="text-xs text-slate-500 bg-transparent border-none p-0 focus:ring-0 w-full h-6">
                                    </td>

                                    <td class="py-3 px-2 text-right">
                                        <div class="inline-flex items-center bg-slate-100 rounded-md px-2 py-1">
                                            <input type="number" step="0.01"
                                                wire:model.live="payments.{{ $index }}.amount"
                                                class="w-16 bg-transparent border-none p-0 text-right text-sm font-black text-orange-600 focus:ring-0 [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none">
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>

                <div class="flex divide-x divide-slate-100 border-b border-slate-100 bg-white">

                    <div class="flex-1 p-4 text-center">
                        <span class="block text-[10px] uppercase font-bold text-slate-400">Recibido</span>
                        <span class="text-lg font-bold text-slate-700">
                            {{ number_format($this->paid, 2) }}
                        </span>
                    </div>

                    <div class="flex-1 p-4 text-center">
                        @if ($this->change > 0)
                            <span class="block text-[10px] uppercase font-bold text-orange-500">Vuelto</span>
                            <span class="text-lg font-bold text-orange-600">
                                {{ number_format($this->change, 2) }}
                            </span>
                        @else
                            <span class="block text-[10px] uppercase font-bold text-slate-400">Faltante</span>
                            <span class="text-lg font-bold text-rose-500">
                                {{ number_format(max(0, $total - $this->paid), 2) }}
                            </span>
                        @endif
                    </div>

                </div>

                <div>
                    <label class="modal-label">Notas de Transacción</label>
                    <textarea wire:model="notes" class="modal-input min-h-[60px] text-xs" placeholder="Comentarios del ticket..."></textarea>
                </div>

                <div class="bg-white p-3 border border-slate-200 rounded-lg space-y-2">
                    <div class="flex justify-between items-center text-xs font-bold text-slate-500">
                        <span>Subtotal bruto:</span>
                        <span>{{ $company->currency_code }}{{ number_format(collect($items)->sum('gross_subtotal'), 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-xs font-bold text-slate-500">
                        <span>Impuesto:</span>
                        <span>{{ $company->currency_code }}{{ number_format(collect($items)->sum('itbis_amount'), 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-xs font-bold text-slate-500">
                        <span>Descuento:</span>
                        <span>{{ $company->currency_code }}{{ number_format(collect($items)->sum('discount_amount'), 2) }}</span>
                    </div>
                    <div
                        class="flex justify-between items-center border-t border-dashed pt-2 font-black text-slate-800">
                        <span class="text-xs">NETO A COBRAR:</span>
                        <span
                            class="text-xl text-indigo-600">{{ $company->currency_code }}{{ number_format($total, 2) }}</span>
                    </div>
                </div>

                <button type="button" wire:click="store"
                    class="btn-primary w-full justify-center py-3 text-xs font-black tracking-wider uppercase"
                    wire:loading.attr="disabled">
                    <i class="fas fa-check-double"></i> Procesar Pago e Imprimir
                </button>
            </div>
        </div>
    </div>

</div>

<script>
    document.addEventListener('livewire:init', function() {
        Livewire.on('print-sale', (event) => {

            document.querySelector('#searchProduct').focus();

            const data = Array.isArray(event) ? event[0] : event;
            let saleUrl = data.url;

            const isAndroid = /Android/i.test(navigator.userAgent);

            if (isAndroid) {
                const url = saleUrl + '?size=' + data.bluetooth_print_size;
                if (data.bluetooth_printing) {
                    printViaBluetooth(url);
                } else {
                    abrirVentanaEmergente(url);
                }
                return;
            }
            const url = saleUrl + '?size=' + data.pc_print_size;
            if (data.direct_pc_printing) {
                printViaPC(url, data.pc_printer_name);
            } else {
                abrirVentanaEmergente(url);
            }
        });
    });
</script>
