<div>
    @if ($open)
        <div class="modal-overlay">
            <div class="modal-backdrop" wire:click="closeModal"></div>

            <div class="modal-container max-w-2xl">
                <div class="modal-body p-4">
                    <div class="space-y-4">
                        <div>
                            <label class="modal-label">Búsqueda rápida</label>
                            <div class="modal-input-group">
                                <i class="fas fa-search modal-input-icon"></i>
                                <input type="text" wire:model.live.debounce.300ms="search"
                                    placeholder="Buscar por Nombre o SKU..." class="modal-input">
                            </div>
                        </div>

                        <div class="datatable-container overflow-x-auto">
                            <table class="datatable w-full">
                                <thead class="datatable-thead">
                                    <tr>
                                        <th class="datatable-th">Producto</th>
                                        <th class="datatable-th">SKU</th>
                                        <th class="datatable-th">Precio</th>
                                        <th class="datatable-th">Stock</th>
                                        <th class="datatable-th text-right">Acción</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50">
                                    @forelse($products as $product)
                                        <tr class="datatable-tr" wire:key="product-row-{{ $product->id }}">
                                            <td class="datatable-td">
                                                <span
                                                    class="text-sm font-bold text-slate-700">{{ $product->name }}</span>
                                            </td>
                                            <td class="datatable-td">
                                                <span class="text-xs text-slate-500">{{ $product->sku ?? '-' }}</span>
                                            </td>
                                            <td class="datatable-td">
                                                <span
                                                    class="text-xs font-medium text-slate-600">{{ number_format($product->price, 2) }}</span>
                                            </td>
                                            <td class="datatable-td">
                                                <span class="text-xs text-slate-600">{{ $product->stock }}</span>
                                            </td>
                                            <td class="datatable-td text-right">
                                                <button type="button" wire:click="selectProduct({{ $product->id }})"
                                                    class="btn-modal-primary group">
                                                    <i class="fas fa-check"></i> Seleccionar
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="py-16 text-center">
                                                <i class="fas fa-box-open text-slate-200 text-3xl mb-3 block"></i>
                                                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">No
                                                    se encontraron productos registrados</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $products->links() }}
                        </div>
                    </div>
                </div>

                <div class="modal-footer p-4 border-t border-slate-100 flex justify-end">
                    <button wire:click="closeModal" class="btn-modal-secondary">Cerrar</button>
                </div>
            </div>
        </div>
    @endif
</div>
