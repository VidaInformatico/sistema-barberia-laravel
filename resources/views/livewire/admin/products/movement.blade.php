<div>
    <div class="page-container">
        <header class="page-header">
            <div class="flex items-center gap-3">
                <div class="header-icon">
                    <i class="fas fa-exchange-alt"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black tracking-tight text-slate-800 leading-none">Kardex de Inventario</h2>
                </div>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.stock-movements.export', ['type' => 'pdf', 'search' => $search, 'filterType' => $filterType]) }}" target="_blank" class="bg-rose-50 text-rose-600 px-3 py-1.5 rounded-lg text-xs font-bold uppercase border border-rose-200">
                        <i class="fas fa-file-pdf"></i> PDF
                    </a>
                    <a href="{{ route('admin.stock-movements.export', ['type' => 'excel', 'search' => $search, 'filterType' => $filterType]) }}" target="_blank" class="bg-emerald-50 text-emerald-600 px-3 py-1.5 rounded-lg text-xs font-bold uppercase border border-emerald-200">
                        <i class="fas fa-file-excel"></i> Excel
                    </a>
                </div>
                <button wire:click="create" class="btn-primary">
                    <i class="fas fa-sliders-h"></i>
                    Ajuste de Stock
                </button>
            </div>

        </header>

        <div class="toolbar-container">
            <div class="search-input-group relative w-full lg:flex-1">
                <input type="text" wire:model.live.debounce.500ms="search" class="search-input"
                    placeholder="Buscar por producto, SKU, motivo o usuario responsable...">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                    <i class="fas fa-search text-sm"></i>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-between w-full lg:w-auto gap-3">
                <div class="flex items-center bg-slate-50 px-3 py-2 rounded-lg border border-slate-100">
                    <label
                        class="text-[10px] font-black text-slate-400 uppercase border-r border-slate-200 pr-3 mr-3 leading-none">
                        Tipo
                    </label>
                    <select wire:model.live="filterType"
                        class="bg-transparent border-none p-0 pr-8 text-sm font-bold text-slate-700 focus:ring-0 cursor-pointer">
                        <option value="">Todos los movimientos</option>
                        <option value="in">Entradas (+)</option>
                        <option value="out">Salidas (-)</option>
                    </select>
                </div>

                <div class="per-page-select flex items-center bg-slate-50 px-3 py-2 rounded-lg border border-slate-100">
                    <label
                        class="text-[10px] font-black text-slate-400 uppercase border-r border-slate-200 pr-3 mr-3 leading-none">
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

        <!-- Vista Móvil (Tarjetas) -->
        <div class="block md:hidden space-y-3">
            @forelse ($movements as $item)
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm space-y-3"
                    wire:key="movement-card-{{ $item->id }}">
                    <!-- Encabezado: Fecha/Hora, Tipo de Operación y Cantidad -->
                    <div class="flex items-start justify-between border-b border-slate-100 pb-2.5">
                        <div class="flex flex-col">
                            <span
                                class="text-xs font-bold text-slate-700">{{ $item->created_at->format('d/m/Y') }}</span>
                            <span
                                class="text-[10px] text-slate-400 mt-0.5">{{ $item->created_at->format('H:i:s') }}</span>
                        </div>

                        <div class="flex items-center gap-2">
                            @if ($item->type === 'in')
                                <span
                                    class="px-2 py-0.5 text-[10px] font-black bg-emerald-100 text-emerald-700 rounded uppercase">
                                    <i class="fas fa-arrow-down mr-0.5"></i>Entrada
                                </span>
                            @else
                                <span
                                    class="px-2 py-0.5 text-[10px] font-black bg-rose-100 text-rose-700 rounded uppercase">
                                    <i class="fas fa-arrow-up mr-0.5"></i>Salida
                                </span>
                            @endif

                            <span
                                class="text-base font-black {{ $item->type === 'in' ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $item->type === 'in' ? '+' : '-' }}{{ $item->quantity }}
                            </span>
                        </div>
                    </div>

                    <!-- Producto -->
                    <div>
                        <span
                            class="text-sm font-bold text-slate-700 block leading-tight">{{ $item->product->name }}</span>
                        <span class="text-xs font-mono text-slate-400 block mt-0.5">{{ $item->product->sku }}</span>
                    </div>

                    <!-- Balance de Stock -->
                    <div class="grid grid-cols-2 gap-2 bg-slate-50 p-2.5 rounded-lg border border-slate-100 text-xs">
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Stock Anterior</span>
                            <span class="font-bold text-slate-600 mt-0.5 block">{{ $item->stock_before }} u.</span>
                        </div>
                        <div class="text-right">
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Stock Final</span>
                            <span class="font-black text-slate-700 mt-0.5 block">{{ $item->stock_after }} u.</span>
                        </div>
                    </div>

                    <!-- Motivo, Referencia Polimórfica y Responsable -->
                    <div class="space-y-1.5 pt-1 text-xs">
                        @if ($item->reason)
                            <p class="text-slate-600 font-medium italic leading-relaxed">
                                {{ $item->reason }}
                            </p>
                        @endif

                        <div class="flex items-center justify-between flex-wrap gap-2 pt-1 border-t border-slate-50">
                            <div>
                                @if ($item->reference_id)
                                    <span
                                        class="text-[9px] font-black bg-indigo-50 border border-indigo-100 text-indigo-600 px-1.5 py-0.5 rounded uppercase tracking-tight inline-block">
                                        <i class="fas fa-link mr-1 text-[8px]"></i>
                                        {{ class_basename($item->reference_type) }} #{{ $item->reference_id }}
                                    </span>
                                @endif
                            </div>

                            <span class="text-[11px] font-bold text-slate-500">
                                <i
                                    class="far fa-user text-slate-400 mr-1 text-[10px]"></i>{{ $item->user->name ?? 'Sistema' }}
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white p-8 rounded-xl border border-slate-200 text-center">
                    <i class="fas fa-exchange-alt text-slate-200 text-3xl mb-3 block"></i>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">No se registran movimientos en
                        el almacén</p>
                </div>
            @endforelse
        </div>

        <!-- Vista Escritorio (Tabla) -->
        <div class="datatable-container hidden md:block overflow-x-auto">
            <table class="datatable w-full">
                <thead class="datatable-thead">
                    <tr>
                        <th class="datatable-th">Fecha / Producto</th>
                        <th class="datatable-th text-center">Operación</th>
                        <th class="datatable-th text-right">Cant.</th>
                        <th class="datatable-th text-right">Stock (Ant. → Act.)</th>
                        <th class="datatable-th">Motivo / Origen</th>
                        <th class="datatable-th">Responsable</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($movements as $item)
                        <tr class="datatable-tr" wire:key="movement-row-{{ $item->id }}">
                            <td class="datatable-td">
                                <div class="flex flex-col">
                                    <!-- Producto Arriba -->
                                    <span class="text-sm font-bold text-slate-800">{{ $item->product->name ?? 'Producto eliminado' }}</span>
                                    <!-- SKU y Fecha Abajo -->
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="text-[10px] font-mono font-semibold bg-slate-100 text-slate-600 px-1 rounded">{{ $item->product->sku ?? 'S/N' }}</span>
                                        <span class="text-[10px] text-slate-400">•</span>
                                        <span class="text-[10px] text-slate-400">{{ $item->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="datatable-td text-center">
                                @if ($item->type === 'in')
                                    <span class="px-2.5 py-0.5 text-[10px] font-black bg-emerald-100 text-emerald-700 rounded uppercase">
                                        <i class="fas fa-arrow-down mr-1"></i>Entrada
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 text-[10px] font-black bg-rose-100 text-rose-700 rounded uppercase">
                                        <i class="fas fa-arrow-up mr-1"></i>Salida
                                    </span>
                                @endif
                            </td>
                            <td class="datatable-td text-right font-black text-sm {{ $item->type === 'in' ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $item->type === 'in' ? '+' : '-' }}{{ $item->quantity }}
                            </td>
                            <td class="datatable-td text-right">
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold text-slate-700">{{ $item->stock_after }} u.</span>
                                    <span class="text-[10px] text-slate-400">Ant: {{ $item->stock_before }} u.</span>
                                </div>
                            </td>
                            <td class="datatable-td">
                                <div class="flex flex-col max-w-xs">
                                    <span class="text-xs text-slate-600 font-medium italic leading-normal">{{ $item->reason ?: 'Sin motivo' }}</span>
        
                                    @if ($item->reference_id)
                                        <span class="text-[9px] font-black bg-indigo-50 border border-indigo-100 text-indigo-600 px-1.5 py-0.5 rounded uppercase tracking-tight mt-1 w-max">
                                            <i class="fas fa-link mr-1 text-[8px]"></i>
                                            {{ class_basename($item->reference_type) }} #{{ $item->reference_id }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="datatable-td whitespace-nowrap">
                                <span class="text-xs font-bold text-slate-600">
                                    <i class="far fa-user text-slate-400 mr-1.5 text-[10px]"></i>{{ trim(($item->user->name ?? '') . ' ' . ($item->user->last_name ?? '')) ?: 'Sistema' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-16 text-center">
                                <i class="fas fa-exchange-alt text-slate-200 text-3xl mb-3 block"></i>
                                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">No se registran movimientos en el almacén</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if (method_exists($movements, 'links'))
            <div class="mt-4 px-2">
                {{ $movements->links() }}
            </div>
        @endif
    </div>

    @if ($isOpen)
        <div class="modal-overlay">
            <div class="modal-backdrop" wire:click="closeModal"></div>

            <div class="modal-container max-w-2xl">
                <div class="modal-header">
                    <div class="flex items-center gap-3">
                        <div class="modal-icon-box">
                            <i class="fas fa-sliders-h"></i>
                        </div>
                        <h3 class="modal-title">Realizar Ajuste de Inventario</h3>
                    </div>
                    <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div class="col-span-2">
                            <label class="modal-label">Producto a Ajustar</label>
                            <div class="modal-input-group">
                                <i class="fas fa-box modal-input-icon"></i>
                                <select wire:model.live="product_id" class="modal-input">
                                    <option value="">Seleccione un producto...</option>
                                    @foreach ($products as $prod)
                                        <option value="{{ $prod->id }}">{{ $prod->name }} (SKU:
                                            {{ $prod->sku }})</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('product_id')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="modal-label">Tipo de Movimiento</label>
                            <div class="grid grid-cols-2 gap-3 mt-1">
                                <label
                                    class="flex items-center justify-center gap-2 p-3 rounded-xl border cursor-pointer font-bold text-xs uppercase transition-all {{ $type === 'in' ? 'bg-emerald-50 border-emerald-300 text-emerald-700' : 'bg-white border-slate-200 text-slate-500 hover:bg-slate-50' }}">
                                    <input type="radio" wire:model.live="type" value="in" class="sr-only">
                                    <i class="fas fa-plus-circle text-sm"></i> Entrada (+)
                                </label>
                                <label
                                    class="flex items-center justify-center gap-2 p-3 rounded-xl border cursor-pointer font-bold text-xs uppercase transition-all {{ $type === 'out' ? 'bg-rose-50 border-rose-300 text-rose-700' : 'bg-white border-slate-200 text-slate-500 hover:bg-slate-50' }}">
                                    <input type="radio" wire:model.live="type" value="out" class="sr-only">
                                    <i class="fas fa-minus-circle text-sm"></i> Salida (-)
                                </label>
                            </div>
                            @error('type')
                                <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="modal-label">Cantidad de Unidades</label>
                            <div class="modal-input-group">
                                <i class="fas fa-layer-group modal-input-icon"></i>
                                <input type="number" min="1" wire:model.live="quantity" class="modal-input"
                                    placeholder="Ej: 5">
                            </div>
                            @error('quantity')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-span-2">
                            <label class="modal-label">Motivo o Justificación del Ajuste</label>
                            <textarea wire:model="reason" class="modal-input min-h-[70px]"
                                placeholder="Ej: Desmedro, rotura de empaque, regularización por inventario físico..."></textarea>
                            @error('reason')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-span-2 bg-slate-50 border border-slate-200/60 rounded-xl p-4 mt-1">
                            <h4
                                class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 border-b border-slate-200 pb-1.5">
                                <i class="fas fa-fingerprint mr-1"></i> Panel de Auditoría e Impacto del Kardex
                            </h4>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs">
                                <div
                                    class="bg-white p-2.5 rounded-lg border border-slate-100 flex flex-col justify-center">
                                    <span class="text-[9px] font-bold text-slate-400 uppercase">Usuario
                                        Responsable</span>
                                    <span class="font-bold text-slate-700 mt-0.5 truncate">
                                        <i class="far fa-user text-slate-400 mr-1"></i> {{ $responsible_user_name }}
                                    </span>
                                </div>

                                <div
                                    class="bg-white p-2.5 rounded-lg border border-slate-100 flex flex-col justify-center">
                                    <span class="text-[9px] font-bold text-slate-400 uppercase">Stock Actual
                                        (Antes)</span>
                                    <span class="font-bold text-slate-600 mt-0.5">
                                        {{ $product_id ? $current_stock . ' unidades' : '---' }}
                                    </span>
                                </div>

                                <div
                                    class="bg-white p-2.5 rounded-lg border border-slate-100 flex flex-col justify-center">
                                    <span class="text-[9px] font-bold text-slate-400 uppercase">Stock Proyectado
                                        (Después)</span>
                                    <span
                                        class="font-black mt-0.5 {{ $type === 'in' ? 'text-emerald-600' : 'text-rose-600' }}">
                                        {{ $product_id ? $projected_stock . ' unidades' : '---' }}
                                    </span>
                                </div>
                            </div>

                            <div class="mt-3 pt-3 border-t border-slate-200/60 grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="text-[9px] font-bold text-slate-400 uppercase block">Clase de Origen
                                        Polimórfico (Reference Type)</label>
                                    <input type="text"
                                        class="modal-input bg-slate-100/80 text-slate-400 text-xs font-mono mt-1"
                                        value="{{ $reference_type ?? 'App\Models\StockMovement (Ajuste Manual Administrativo)' }}"
                                        disabled>
                                </div>
                                <div>
                                    <label class="text-[9px] font-bold text-slate-400 uppercase block">ID de Origen
                                        (Reference ID)</label>
                                    <input type="text"
                                        class="modal-input bg-slate-100/80 text-slate-400 text-xs font-mono mt-1"
                                        value="{{ $reference_id ?? 'Autogenerado por Almacén' }}" disabled>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button wire:click="closeModal" class="btn-modal-secondary">Cancelar</button>
                    <button wire:click="store" class="btn-modal-primary" wire:loading.attr="disabled">
                        <span class="relative z-10" wire:loading.remove wire:target="store">
                            Aplicar Ajuste
                        </span>
                        <span class="relative z-10" wire:loading wire:target="store">
                            Procesando...
                        </span>
                        <i
                            class="fas fa-check-circle text-indigo-400 group-hover:text-white transition-colors text-xs relative z-10"></i>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
