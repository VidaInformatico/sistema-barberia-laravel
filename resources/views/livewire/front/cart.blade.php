<div class="w-full">
    <!-- Encabezados -->
    <div class="grid grid-cols-12 gap-4 pb-4 text-[10px] uppercase tracking-[0.2em] text-stone-400 border-b border-stone-200 mb-2">
        <div class="col-span-6">Producto</div>
        <div class="col-span-2 text-center">Cantidad</div>
        <div class="col-span-2 text-right">Precio</div>
        <div class="col-span-2"></div>
    </div>

    <!-- Lista -->
    <div class="space-y-0">
        @forelse($cart as $id => $item)
            <div wire:key="cart-item-{{ $id }}" class="grid grid-cols-12 gap-4 items-center py-6 border-b border-stone-100">
                <div class="col-span-6 flex items-start gap-4">
                    <img src="{{ Storage::url($item['image']) }}" alt="{{ $item['name'] }}"
                         class="w-16 h-16 object-cover rounded-md bg-stone-100 mt-1">
                    <div>
                        <h4 class="text-sm font-medium text-stone-900">{{ $item['name'] }}</h4>
                        <p class="text-[10px] text-stone-400 uppercase tracking-widest mt-0.5">{{ $item['category'] ?? 'Producto' }}</p>

                        @if (!empty($item['type']) && $item['type'] === 'combo' && !empty($item['combo_components']))
                            <div class="mt-2 text-xs text-stone-500 bg-stone-50 p-2 rounded border border-stone-200">
                                <span class="font-bold text-stone-700 block mb-1">Incluye:</span>
                                <ul class="space-y-0.5 text-[11px]">
                                    @foreach ($item['combo_components'] as $component)
                                        <li class="flex justify-between gap-4">
                                            <span>- {{ $component['name'] }}</span>
                                            <span class="font-semibold text-stone-800">x{{ (int) $component['quantity'] }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-span-2 flex justify-center">
                    <div class="flex items-center border border-stone-200 rounded-full px-2 py-1">
                        <button wire:click="updateQuantity('{{ $id }}', 'dec')" class="px-2 text-stone-400 hover:text-black transition">
                            <i class="fa-solid fa-minus text-[10px]"></i>
                        </button>
                        <span class="w-6 text-center text-xs font-bold text-stone-900">{{ $item['quantity'] }}</span>
                        <button wire:click="updateQuantity('{{ $id }}', 'inc')" class="px-2 text-stone-400 hover:text-black transition">
                            <i class="fa-solid fa-plus text-[10px]"></i>
                        </button>
                    </div>
                </div>

                <div class="col-span-2 text-right">
                    <span class="text-sm font-semibold text-stone-900">{{ $company->currency_code }}{{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                </div>

                <div class="col-span-2 text-right">
                    <button wire:click="removeItem('{{ $id }}')" class="text-stone-300 hover:text-red-500 transition-colors">
                        <i class="fa-solid fa-trash-can text-sm"></i>
                    </button>
                </div>
            </div>
        @empty
            <div class="text-center py-20 text-stone-400 font-light">Tu carrito está vacío</div>
        @endforelse
    </div>

    <!-- Total y Botones -->
    @if (!empty($cart))
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center pt-8 gap-6">
            <button wire:click="clearCart" class="text-xs uppercase tracking-widest text-stone-400 hover:text-red-600 transition underline underline-offset-4">
                Limpiar Carrito
            </button>
            <div class="flex flex-col items-end gap-4 w-full md:w-auto">
                <div class="text-right">
                    <span class="text-[10px] uppercase tracking-[0.2em] text-stone-400 mb-1 block">Total del pedido</span>
                    <span class="text-3xl font-light text-stone-900">
                        {{ $company->currency_code }}{{ number_format(collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']), 2) }}
                    </span>
                </div>
                <button wire:click="checkout" class="w-full md:w-auto px-8 py-3 bg-stone-900 text-white text-xs uppercase tracking-widest hover:bg-amber-600 transition-colors shadow-md">
                    Procesar Pedido
                </button>
            </div>
        </div>
    @endif
</div>