<main class="max-w-7xl mx-auto px-6 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-10 items-start">

        <aside class="space-y-8 bg-stone-900/40 border border-stone-800 p-6 rounded-md">
    
            <!-- Categorías con estilo Toggle Switch -->
            <div>
                <h3 class="text-sm font-bold uppercase text-amber-500 mb-4 border-b border-stone-800 pb-2">Categorías</h3>
                <div class="space-y-3">
                    <!-- Opción: Todos los Productos -->
                    <label class="flex items-center justify-between cursor-pointer group">
                        <span class="text-sm text-stone-400 group-hover:text-white transition-colors">Todos los Productos</span>
                        <div class="relative">
                            <input type="radio" name="category_toggle" wire:click="$set('selectedCategory', null)" 
                                   class="sr-only peer" {{ is_null($selectedCategory) ? 'checked' : '' }}>
                            <div class="w-9 h-5 bg-stone-950 border border-stone-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-stone-500 after:border-stone-300 after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-amber-500 peer-checked:after:bg-white"></div>
                        </div>
                    </label>
        
                    <!-- Categorías dinámicas -->
                    @foreach ($categories as $category)
                        <label class="flex items-center justify-between cursor-pointer group">
                            <span class="text-sm text-stone-400 group-hover:text-white transition-colors flex items-center gap-2">
                                {{ $category->name }}
                                <span class="text-xs font-mono bg-stone-800 px-1.5 py-0.5 rounded text-stone-500">{{ $category->products_count }}</span>
                            </span>
                            <div class="relative">
                                <input type="radio" name="category_toggle" wire:click="$set('selectedCategory', {{ $category->id }})" 
                                       class="sr-only peer" {{ $selectedCategory == $category->id ? 'checked' : '' }}>
                                <div class="w-9 h-5 bg-stone-950 border border-stone-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-stone-500 after:border-stone-300 after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-amber-500 peer-checked:after:bg-white"></div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
        
            <!-- Marcas con estilo Toggle Switch -->
            <div>
                <h3 class="text-sm font-bold uppercase text-amber-500 mb-4 border-b border-stone-800 pb-2">Marcas</h3>
                <div class="space-y-3">
                    @foreach ($brands as $brand)
                        <label class="flex items-center justify-between cursor-pointer group">
                            <span class="text-sm text-stone-400 group-hover:text-white transition-colors">{{ $brand->name }}</span>
                            <div class="relative">
                                <input type="checkbox" wire:model.live="selectedBrands" value="{{ $brand->id }}" 
                                       class="sr-only peer">
                                <div class="w-9 h-5 bg-stone-950 border border-stone-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-stone-500 after:border-stone-300 after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-amber-500 peer-checked:after:bg-white"></div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
        </aside>

        <div class="lg:col-span-3">
            <div
                class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-8 bg-stone-900/20 border border-stone-800 p-4 rounded-md">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar productos..."
                    class="bg-stone-950 border border-stone-800 text-sm px-4 py-2 rounded-sm w-full sm:w-64 focus:border-amber-500 outline-none text-white">

                <select wire:model.live="sort"
                    class="bg-stone-950 border border-stone-800 text-stone-300 text-sm px-4 py-2 rounded-sm outline-none">
                    <option value="created_at_desc">Más Nuevos</option>
                    <option value="price_asc">Precio: Menor a Mayor</option>
                    <option value="price_desc">Precio: Mayor a Menor</option>
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                @forelse ($products as $product)
                    <div
                        class="group relative bg-stone-900/60 border border-stone-800/80 rounded-sm overflow-hidden transition-all duration-500 hover:border-amber-500/40 flex flex-col justify-between h-[420px]">
                        <div
                            class="relative w-full h-56 bg-stone-950 flex items-center justify-center p-6 overflow-hidden">
                            @if ($product->hasLowStock())
                                <span
                                    class="absolute top-3 left-3 bg-amber-500 text-stone-950 font-mono text-[10px] font-bold uppercase px-2 py-0.5 tracking-wider rounded-sm z-10">
                                    Pocas unidades
                                </span>
                            @endif

                            <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}"
                                class="max-h-full object-contain transition-transform duration-500 group-hover:scale-105">

                            <div
                                class="absolute inset-0 bg-stone-950/80 backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center p-4">
                                <button wire:click="addToCart({{ $product->id }})" wire:loading.attr="disabled"
                                    class="bg-amber-500 hover:bg-amber-400 text-stone-950 font-bold uppercase text-xs tracking-widest px-6 py-3 rounded-sm transition-all shadow-lg flex items-center gap-2 disabled:opacity-50">
                                    <i class="fa-solid fa-basket-shopping"></i>
                                    <span wire:loading.remove wire:target="addToCart({{ $product->id }})">Añadir al
                                        Carrito</span>
                                    <span wire:loading
                                        wire:target="addToCart({{ $product->id }})">Procesando...</span>
                                </button>
                            </div>
                        </div>

                        <a href="{{ route('shop.show', $product->slug) }}"
                            class="p-5 flex-1 flex flex-col justify-between border-t border-stone-800/60">
                            <div>
                                <span class="text-[10px] font-mono text-stone-500 uppercase tracking-widest block mb-1">
                                    {{ $product->category->name }}
                                </span>
                                <h3
                                    class="text-base font-bold text-stone-100 tracking-tight line-clamp-1 group-hover:text-amber-400 transition-colors">
                                    {{ $product->name }}
                                </h3>
                                <p class="text-xs text-stone-400 mt-2 line-clamp-2 leading-relaxed">
                                    {{ $product->description }}
                                </p>
                            </div>

                            <div class="flex items-center justify-between mt-4">
                                <span
                                    class="text-lg font-black text-amber-500 font-mono">{{ $product->formatted_price }}</span>

                                @if ($product->isOutOfStock())
                                    <span class="text-[10px] font-bold text-red-500">Agotado</span>
                                @else
                                    <span class="text-[10px] font-bold text-emerald-500 flex items-center gap-1">
                                        <span
                                            class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block {{ $product->hasLowStock() ? 'animate-pulse' : '' }}"></span>
                                        En Stock
                                    </span>
                                @endif
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-20 text-stone-500">
                        No se encontraron productos con los filtros seleccionados.
                    </div>
                @endforelse
            </div>

            <div class="mt-12">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</main>
