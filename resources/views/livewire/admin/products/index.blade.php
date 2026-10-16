<div>
    <div class="page-container">
        <header class="page-header">
            <div class="flex items-center gap-3">
                <div class="header-icon">
                    <i class="fas fa-boxes"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black tracking-tight text-slate-800 leading-none">Productos e Inventario
                    </h2>
                </div>

            </div>
            <div class="flex items-center gap-3 flex-wrap">
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.products.export', ['type' => 'pdf', 'search' => $search]) }}" target="_blank"
                        class="bg-rose-50 text-rose-600 px-3 py-1.5 rounded-lg text-xs font-bold uppercase border border-rose-200">
                        <i class="fas fa-file-pdf"></i> PDF
                    </a>
                    <a href="{{ route('admin.products.export', ['type' => 'excel', 'search' => $search]) }}"
                        target="_blank"
                        class="bg-emerald-50 text-emerald-600 px-3 py-1.5 rounded-lg text-xs font-bold uppercase border border-emerald-200">
                        <i class="fas fa-file-excel"></i> Excel
                    </a>
                </div>
                <button wire:click="create" class="btn-primary">
                    <i class="fas fa-plus"></i>
                    Nuevo Producto
                </button>
            </div>

        </header>

        <div class="toolbar-container">
            <div class="search-input-group relative w-full lg:flex-1">
                <input type="text" wire:model.live.debounce.500ms="search" class="search-input"
                    placeholder="Buscar por nombre, SKU o categoría...">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                    <i class="fas fa-search text-sm"></i>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-between w-full lg:w-auto gap-3">
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

        <div class="block md:hidden space-y-3">
            @forelse ($products as $item)
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm space-y-3"
                    wire:key="product-card-{{ $item->id }}">
                    <div class="flex items-start justify-between gap-3 border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div
                                class="w-12 h-12 rounded-lg overflow-hidden border border-slate-200 bg-slate-50 flex-shrink-0">
                                @if ($item->image_url)
                                    <img src="{{ $item->image_url }}" alt="{{ $item->name }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <div
                                        class="w-full h-full flex items-center justify-center text-slate-300 bg-slate-100">
                                        <i class="fas fa-box text-base"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex flex-col min-w-0">
                                <span
                                    class="text-sm font-bold text-slate-700 truncate leading-tight">{{ $item->name }}</span>
                                <span
                                    class="text-[11px] font-bold text-indigo-500 mt-0.5 uppercase tracking-wider truncate">{{ $item->category->name }}</span>
                            </div>
                        </div>

                        <div class="shrink-0">
                            @if ($item->is_active)
                                <span
                                    class="px-2 py-0.5 text-[10px] font-black bg-emerald-100 text-emerald-600 rounded-full uppercase">Activo</span>
                            @else
                                <span
                                    class="px-2 py-0.5 text-[10px] font-black bg-slate-100 text-slate-400 rounded-full uppercase">Inactivo</span>
                            @endif
                        </div>
                    </div>

                    <!-- Descripción (si existe) -->
                    @if ($item->description)
                        <p class="text-xs text-slate-400 line-clamp-2">
                            {{ $item->description }}
                        </p>
                    @endif

                    <!-- Detalles: SKU, Precio y Stock -->
                    <div class="grid grid-cols-2 gap-2 bg-slate-50 p-2.5 rounded-lg border border-slate-100 text-xs">
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">SKU / Precio</span>
                            <span class="font-mono font-bold text-slate-600 block">{{ $item->sku }}</span>
                            <span
                                class="font-black text-slate-800 text-sm block mt-0.5">{{ $item->formatted_price }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-slate-400 block text-[10px] uppercase font-bold mb-1">Existencias</span>
                            <div>
                                @if ($item->isOutOfStock())
                                    <span
                                        class="px-2 py-0.5 text-[10px] font-black bg-red-100 text-red-600 rounded uppercase inline-block">Agotado</span>
                                @elseif($item->hasLowStock())
                                    <span
                                        class="px-2 py-0.5 text-[10px] font-black bg-amber-100 text-amber-600 rounded uppercase inline-block">Stock
                                        Bajo</span>
                                @else
                                    <span
                                        class="px-2 py-0.5 text-[10px] font-black bg-emerald-100 text-emerald-600 rounded uppercase inline-block">Estable</span>
                                @endif
                            </div>
                            <span class="text-xs font-bold text-slate-600 mt-1 block">{{ $item->stock }}
                                unidades</span>
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="flex items-center justify-end gap-2 pt-1 border-t border-slate-50">
                        <button wire:click="edit({{ $item->id }})"
                            class="p-2 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-600 transition-colors"
                            title="Editar">
                            <i class="fas fa-edit text-sm"></i>
                        </button>
                        <button wire:click="deleteConfirm({{ $item->id }})"
                            class="p-2 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 transition-colors"
                            title="Eliminar">
                            <i class="fas fa-trash-alt text-sm"></i>
                        </button>
                    </div>
                </div>
            @empty
                <div class="bg-white p-8 rounded-xl border border-slate-200 text-center">
                    <i class="fas fa-boxes text-slate-200 text-3xl mb-3 block"></i>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">No hay productos registrados
                        en el inventario</p>
                </div>
            @endforelse
        </div>

        <!-- Vista Escritorio (Tabla) -->
        <div class="datatable-container hidden md:block overflow-x-auto">
            <table class="datatable w-full">
                <thead class="datatable-thead">
                    <tr>
                        <th class="datatable-th">Producto</th>
                        <th class="datatable-th">SKU / Categoría</th>
                        <th class="datatable-th">Precio Venta</th>
                        <th class="datatable-th">Existencias</th>
                        <th class="datatable-th">Estado</th>
                        <th class="datatable-th text-right">Gestión</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($products as $item)
                        <tr class="datatable-tr" wire:key="product-row-{{ $item->id }}">
                            <td class="datatable-td">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-lg overflow-hidden border border-slate-200 bg-slate-50 flex-shrink-0">
                                        @if ($item->image_url)
                                            <img src="{{ $item->image_url }}" alt="{{ $item->name }}"
                                                class="w-full h-full object-cover">
                                        @else
                                            <div
                                                class="w-full h-full flex items-center justify-center text-slate-300 bg-slate-100">
                                                <i class="fas fa-box text-sm"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-slate-700">{{ $item->name }}</span>
                                        <span
                                            class="text-xs text-slate-400 max-w-xs truncate">{{ $item->description ?? 'Sin descripción' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="datatable-td">
                                <div class="flex flex-col">
                                    <span
                                        class="text-xs font-mono font-bold text-slate-600">{{ $item->sku }}</span>
                                    <span
                                        class="text-[11px] font-bold text-indigo-500 mt-0.5 uppercase tracking-wider">{{ $item->category->name }}</span>
                                </div>
                            </td>
                            <td class="datatable-td">
                                <span class="text-sm font-black text-slate-700">
                                    {{ $item->formatted_price }}
                                </span>
                            </td>
                            <td class="datatable-td">
                                <div class="flex flex-col">
                                    @if ($item->isOutOfStock())
                                        <span
                                            class="px-2 py-0.5 text-[10px] font-black bg-red-100 text-red-600 rounded uppercase w-max">Agotado</span>
                                    @elseif($item->hasLowStock())
                                        <span
                                            class="px-2 py-0.5 text-[10px] font-black bg-amber-100 text-amber-600 rounded uppercase w-max">Stock
                                            Bajo</span>
                                    @else
                                        <span
                                            class="px-2 py-0.5 text-[10px] font-black bg-emerald-100 text-emerald-600 rounded uppercase w-max">Estable</span>
                                    @endif
                                    <span class="text-xs font-bold text-slate-600 mt-1">{{ $item->stock }}
                                        unidades</span>
                                </div>
                            </td>
                            <td class="datatable-td">
                                @if ($item->is_active)
                                    <span
                                        class="px-2 py-1 text-[10px] font-black bg-emerald-100 text-emerald-600 rounded-full uppercase">Activo</span>
                                @else
                                    <span
                                        class="px-2 py-1 text-[10px] font-black bg-slate-100 text-slate-400 rounded-full uppercase">Inactivo</span>
                                @endif
                            </td>
                            <td class="datatable-td text-right">
                                <div class="action-group">
                                    <button wire:click="edit({{ $item->id }})"
                                        class="btn-action hover:bg-amber-50 hover:text-amber-600 border-r"
                                        title="Editar">
                                        <i class="fas fa-edit text-xs"></i>
                                    </button>
                                    <button wire:click="deleteConfirm({{ $item->id }})"
                                        class="btn-action hover:bg-red-50 hover:text-red-600" title="Eliminar">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-16 text-center">
                                <i class="fas fa-boxes text-slate-200 text-3xl mb-3 block"></i>
                                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">No hay productos
                                    registrados en el inventario</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if (method_exists($products, 'links'))
            <div class="mt-4 px-2">
                {{ $products->links() }}
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
                            <i class="fas {{ $product_id ? 'fa-edit' : 'fa-plus' }}"></i>
                        </div>
                        <h3 class="modal-title">
                            {{ $product_id ? 'Editar Ficha de Producto' : 'Registrar Nuevo Producto' }}
                        </h3>
                    </div>
                    <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div class="col-span-2">
                            <label class="modal-label">Tipo de Producto</label>
                            <select wire:model.live="type" class="modal-input">
                                <option value="product">Producto Simple</option>
                                <option value="combo">Combo (Pack de productos)</option>
                            </select>
                        </div>

                        @if ($type === 'combo')
                            <div class="col-span-2 space-y-4">
                                <div
                                    class="p-4 bg-indigo-50 border border-indigo-200 rounded-xl flex items-center justify-between">
                                    <div>
                                        <p class="text-xs font-bold text-indigo-900">Configuración de Combo</p>
                                        <p class="text-[10px] text-indigo-600">Define los productos que componen este
                                            pack.</p>
                                    </div>
                                    <button type="button" wire:click="$dispatch('openComboModal')"
                                        class="px-4 py-2 bg-indigo-600 text-white text-xs font-bold rounded-lg hover:bg-indigo-700 transition-colors">
                                        <i class="fas fa-plus mr-1"></i> Agregar Producto
                                    </button>
                                </div>

                                <div class="datatable-container overflow-x-auto">
                                    <table class="datatable w-full">
                                        <thead class="datatable-thead">
                                            <tr>
                                                <th class="datatable-th">Producto</th>
                                                <th class="datatable-th text-center w-32">Cantidad</th>
                                                <th class="datatable-th text-right">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-50">
                                            @forelse($this->comboItemsWithDetails as $index => $item)
                                                <tr class="datatable-tr" wire:key="combo-item-{{ $item['id'] }}">
                                                    <td class="datatable-td">
                                                        <span
                                                            class="text-sm font-bold text-slate-700">{{ $item['name'] }}</span>
                                                        <span class="block text-xs text-slate-500 font-normal">SKU:
                                                            {{ $item['sku'] }}</span>
                                                    </td>
                                                    <td class="datatable-td text-center">
                                                        <input type="number" min="1"
                                                            wire:model.live="selectedComboItems.{{ $index }}.quantity"
                                                            class="w-20 text-center text-xs font-bold py-1 border border-slate-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                                    </td>
                                                    <td class="datatable-td text-right">
                                                        <button type="button"
                                                            wire:click="removeComboItem({{ $index }})"
                                                            class="btn-action hover:bg-red-50 hover:text-red-600"
                                                            title="Eliminar">
                                                            <i class="fas fa-trash-alt text-xs"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="py-16 text-center">
                                                        <i
                                                            class="fas fa-box-open text-slate-200 text-3xl mb-3 block"></i>
                                                        <p
                                                            class="text-slate-400 text-xs font-bold uppercase tracking-widest">
                                                            No hay productos agregados a este combo todavía</p>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        <div class="col-span-2">
                            <label class="modal-label">Nombre del Producto</label>
                            <div class="modal-input-group">
                                <i class="fas fa-box-open modal-input-icon"></i>
                                <input type="text" wire:model="name" class="modal-input"
                                    placeholder="Ej: Pomada Mate Reuzel 4oz">
                            </div>
                            @error('name')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="modal-label">Código SKU / de Barras</label>
                            <div class="modal-input-group">
                                <i class="fas fa-barcode modal-input-icon"></i>
                                <input type="text" wire:model="sku" class="modal-input"
                                    placeholder="Ej: REU-MAT-04">
                            </div>
                            @error('sku')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="modal-label">Categoría del Producto</label>
                            <div class="modal-input-group">
                                <i class="fas fa-tags modal-input-icon"></i>
                                <select wire:model="category_id" class="modal-input">
                                    <option value="">Seleccione una categoría...</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('category_id')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="modal-label">Marca del Producto</label>
                            <div class="modal-input-group">
                                <i class="fas fa-tags modal-input-icon"></i>
                                <select wire:model="brand_id" class="modal-input">
                                    <option value="">Seleccione una marca...</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('brand_id')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="modal-label">Costo Compra (Precio Costo)</label>
                            <div class="modal-input-group">
                                <i class="fas fa-dollar-sign modal-input-icon"></i>
                                <input type="number" step="0.01" wire:model="cost_price" class="modal-input"
                                    placeholder="0.00">
                            </div>
                            @error('cost_price')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="modal-label">Precio Venta al Público</label>
                            <div class="modal-input-group">
                                <i class="fas fa-coins modal-input-icon"></i>
                                <input type="number" step="0.01" wire:model="price" class="modal-input"
                                    placeholder="0.00">
                            </div>
                            @error('price')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-span-2">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

                                <div>
                                    <label class="modal-label">Es inventariable</label>
                                    <div class="modal-input-group">
                                        <i class="fas fa-tags modal-input-icon"></i>
                                        <select wire:model.live="is_inventoriable" class="modal-input">
                                            <option value="">Seleccione...</option>
                                            <option value="1">SI</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>
                                    @error('is_inventoriable')
                                        <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                                    @enderror
                                </div>

                                @if ($is_inventoriable == 1)
                                    <div>
                                        <label class="modal-label">Stock</label>
                                        <div class="modal-input-group">
                                            <i class="fas fa-layer-group modal-input-icon"></i>
                                            <input type="number" wire:model="stock" class="modal-input"
                                                placeholder="0" {{ $product_id ? 'disabled' : '' }}>
                                        </div>
                                        @error('stock')
                                            <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="modal-label">Stock Mín.</label>
                                        <div class="modal-input-group">
                                            <i class="fas fa-exclamation-triangle modal-input-icon"></i>
                                            <input type="number" wire:model="stock_min" class="modal-input"
                                                placeholder="Ej: 3">
                                        </div>
                                        @error('stock_min')
                                            <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @endif

                                <div>
                                    <label class="modal-label">Impuesto</label>
                                    <div class="modal-input-group">
                                        <i class="fas fa-tags modal-input-icon"></i>
                                        <select wire:model="tax" class="modal-input">
                                            <option value="">Seleccione...</option>
                                            <option value="18">Gravado 18%</option>
                                            <option value="16">Gravado 16%</option>
                                            <option value="0">Gravado 0%</option>
                                            <option value="ex">Exento</option>
                                        </select>
                                    </div>
                                    @error('tax')
                                        <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                                    @enderror
                                </div>

                            </div>
                        </div>

                        <div class="col-span-2">
                            <label class="modal-label">Descripción</label>
                            <textarea wire:model="description" class="modal-input min-h-[70px]"
                                placeholder="Breve detalle técnico, volumen, marca..."></textarea>
                            @error('description')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-span-2">
                            <label class="modal-label">Imagen Ilustrativa del Producto</label>
                            <div
                                class="mt-1 flex flex-col items-center justify-center border-2 border-dashed border-slate-200 rounded-xl p-4 bg-slate-50/50 hover:bg-slate-50 transition-colors relative">
                                <input type="file" wire:model="image"
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                <div class="text-center">
                                    <i class="fas fa-upload text-xl text-slate-400 mb-2"></i>
                                    <p class="text-xs font-bold text-slate-600">Cargar foto del producto</p>
                                </div>
                            </div>
                            @error('image')
                                <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        @if ($existing_image || $image)
                            <div
                                class="col-span-2 flex gap-4 justify-center items-center mt-1 bg-slate-50 p-2 rounded-xl border border-slate-100">
                                @if ($existing_image && !$image)
                                    <div class="text-center">
                                        <span class="text-[9px] font-black text-slate-400 uppercase block mb-1">Imagen
                                            Actual</span>
                                        <img src="{{ $existing_image }}"
                                            class="w-20 h-20 object-cover rounded-lg border border-slate-200">
                                    </div>
                                @endif
                                @if ($image)
                                    <div class="text-center">
                                        <span class="text-[9px] font-black text-indigo-500 uppercase block mb-1">Nueva
                                            Vista</span>
                                        <img src="{{ $image->temporaryUrl() }}"
                                            class="w-20 h-20 object-cover rounded-lg border-2 border-indigo-500">
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div class="col-span-2 py-1">
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <div class="relative">
                                    <input type="checkbox" wire:model="is_active" class="sr-only peer">
                                    <div
                                        class="w-10 h-5 bg-slate-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600">
                                    </div>
                                </div>
                                <span
                                    class="text-xs font-bold text-slate-600 uppercase group-hover:text-indigo-600 transition-colors">¿Producto
                                    habilitado para la venta?</span>
                            </label>
                            @error('is_active')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button wire:click="closeModal" class="btn-modal-secondary">Cancelar</button>
                    <button wire:click="store" class="btn-modal-primary" wire:loading.attr="disabled">
                        <span class="relative z-10" wire:loading.remove wire:target="store">
                            {{ $product_id ? 'Actualizar' : 'Guardar' }}
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

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('confirm-delete', data => {
                Swal.fire({
                    title: '¿Eliminar Producto?',
                    text: "El producto se marcará como removido del sistema.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#4F46E5',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch('delete-confirmed', {
                            id: data.id
                        });
                    }
                });
            });
        });
    </script>
</div>
