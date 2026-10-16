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
                            {{ $purchase_number }}
                        </p>
                        @error('purchase_number')
                            <span class="text-red-500 text-[10px] font-bold block mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </header>

            <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 relative">
                <label class="modal-label font-black text-slate-700">1. Identificar Proveedor</label>
                @if (!$selectedSupplier)
                    <div class="relative mt-1">
                        <input type="text" wire:model.live.debounce.300ms="searchSupplier" class="modal-input pl-10"
                            placeholder="Escriba nombre o correo del proveedor...">
                        <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"><i
                                class="fas fa-search text-xs"></i></div>
                    </div>
                    @if (!empty($supplierResults))
                        <div
                            class="absolute z-50 left-4 right-4 mt-1 bg-white border border-slate-200 rounded-lg shadow-xl divide-y divide-slate-100 overflow-hidden">
                            @foreach ($supplierResults as $c)
                                <div wire:click="selectSupplier({{ json_encode($c) }})"
                                    class="p-3 hover:bg-indigo-50 cursor-pointer flex justify-between items-center transition-colors">
                                    <span class="font-bold text-slate-700 text-xs">{{ $c['company_name'] }}</span>
                                    <span
                                        class="text-[10px] bg-slate-100 px-2 py-0.5 rounded text-slate-400 font-medium">{{ $c['email'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @else
                    <div
                        class="mt-2 p-3 bg-emerald-50 border border-emerald-100 rounded-lg flex justify-between items-center">
                        <div class="flex items-center gap-2 text-emerald-800 font-bold text-xs">
                            <i class="fas fa-check-circle"></i> <span>{{ $selectedSupplier['company_name'] }}
                                ({{ $selectedSupplier['email'] }})</span>
                        </div>
                        <button type="button" wire:click="removeSupplier"
                            class="text-xs text-red-500 hover:text-red-700 font-bold uppercase">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                @endif
                @error('selectedSupplier')
                    <span class="text-red-500 text-[10px] font-bold block mt-1">{{ $message }}</span>
                @enderror
            </div>

            <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 relative">
                <label class="modal-label font-black text-slate-700">2. Escanear o Buscar Ítems</label>
                <div class="relative mt-1">
                    <input type="text" wire:model.live.debounce.300ms="searchProduct" class="modal-input pl-10"
                        placeholder="Escriba el nombre del servicio o producto a añadir...">
                    <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"><i
                            class="fas fa-search text-xs"></i></div>
                </div>
                @if (!empty($searchProduct))
                    <div
                        class="absolute z-40 left-4 right-4 mt-1 bg-white border border-slate-200 rounded-lg shadow-xl divide-y divide-slate-100 overflow-hidden">
                        @forelse ($productResults as $p)
                            <div wire:click="addProductToCart({{ $p['id'] }})"
                                class="p-3 hover:bg-indigo-50 cursor-pointer flex justify-between items-center transition-colors">
                                <span class="font-bold text-slate-700 text-xs">
                                    <i class="fas fa-plus-circle text-indigo-500 mr-2"></i>{{ $p['name'] }}
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

            <div class="border border-slate-100 rounded-xl overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-100 text-[10px] uppercase font-black text-slate-500 tracking-wider">
                        <tr>
                            <th class="p-3">Producto</th>
                            <th class="p-3 text-center w-28">Cant.</th>
                            <th class="p-3 w-32">Precio Unit.</th>
                            <th class="p-3 text-center w-28">Desc(%).</th>
                            <th class="p-3 w-32">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @forelse($items as $index => $item)
                            <tr class="hover:bg-slate-50 font-medium">
                                <td class="p-3 font-bold text-slate-700 flex items-center gap-3">
                                    <button type="button" wire:click="removeItem({{ $index }})"
                                        class="text-red-400 hover:text-red-600 transition-colors inline-flex items-center justify-center">
                                        <i class="fas fa-minus-circle text-base"></i>
                                    </button>
                                    <span>{{ $item['name'] }}</span>
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

                <div class="mb-4">
                    <span
                        class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-cash-register text-xs"></i>
                        Origen de Fondos
                    </span>

                    <label for="use_cash_register"
                        class="flex items-center justify-between w-full min-h-[42px] px-3 py-2 bg-white border border-slate-200 rounded-lg shadow-sm cursor-pointer hover:bg-slate-50 transition-colors">

                        <span class="text-xs font-semibold text-slate-600 select-none">
                            ¿Usar dinero de caja para la compra?
                        </span>

                        <div class="relative inline-flex items-center select-none">
                            <input type="checkbox" wire:model="use_cash_register" id="use_cash_register"
                                class="sr-only peer" />

                            <div
                                class="w-9 h-5 bg-slate-200 rounded-full peer-checked:bg-emerald-500 transition-colors duration-200">
                            </div>

                            <div
                                class="absolute left-0.5 top-0.5 bg-white w-4 h-4 rounded-full shadow-sm transition-transform duration-200 peer-checked:translate-x-4 pointer-events-none">
                            </div>
                        </div>
                    </label>

                    @error('use_cash_register')
                        <span class="text-red-500 text-[10px] font-bold block mt-1">{{ $message }}</span>
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
                                <option value="{{ $method->id }}">
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
                        <span class="text-xl text-indigo-600">{{ $company->currency_code }}{{ number_format($total, 2) }}</span>
                    </div>
                </div>

                <button type="button" wire:click="update"
                    class="btn-primary w-full justify-center py-3 text-xs font-black tracking-wider uppercase"
                    wire:loading.attr="disabled">
                    <i class="fas fa-check-double"></i> Procesar Pago e Imprimir
                </button>
            </div>
        </div>
    </div>
</div>
