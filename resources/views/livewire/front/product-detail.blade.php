<div class="max-w-7xl mx-auto px-6 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-start">

        <div class="space-y-4">
            <div class="overflow-hidden rounded-2xl bg-stone-900 border border-stone-800">
                <img src="{{ asset('storage/' . $this->product->image) }}" alt="{{ $this->product->name }}"
                    class="w-full h-auto object-cover hover:scale-105 transition-transform duration-500">
            </div>
        </div>

        <div class="flex flex-col h-full">
            <div class="mb-6">
                <span
                    class="inline-block px-3 py-1 bg-amber-500/10 text-amber-500 text-xs font-bold uppercase tracking-widest rounded-full mb-4">
                    {{ $this->product->category->name ?? 'Sin categoría' }}
                </span>
                <h1 class="text-3xl sm:text-4xl font-black uppercase tracking-tighter text-white mb-2">
                    {{ $this->product->name }}</h1>

                <div class="flex flex-wrap items-center gap-3 text-xs text-stone-400">
                    <span>SKU: {{ $this->product->sku }}</span>
                    <span class="text-stone-700">•</span>
                    <span class="flex items-center gap-1 text-amber-500 font-medium">
                        <i class="fa-solid fa-location-dot"></i>
                        {{ $this->product->branch->name ?? 'Sucursal general' }}
                    </span>
                </div>
            </div>

            <div class="text-3xl font-bold text-white mb-6">
                {{ $company->currency_code }}{{ number_format($this->product->price, 2) }}
            </div>

            <div class="prose prose-invert prose-stone mb-8">
                <p>{{ $this->product->description }}</p>
            </div>

            @if ($this->product->type === 'combo' && $this->product->comboItems && $this->product->comboItems->count() > 0)
                <div class="mb-6 p-4 rounded-xl bg-stone-900/60 border border-stone-800">
                    <span class="text-xs font-bold uppercase tracking-wider text-amber-500 block mb-2">Este combo
                        incluye:</span>
                    <ul class="space-y-1.5 text-xs text-stone-300">
                        @foreach ($this->product->comboItems as $component)
                            <li class="flex justify-between items-center">
                                <span>- {{ $component->name }}</span>
                                <span class="font-bold text-amber-400">x{{ (int) $component->pivot->quantity }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 mt-auto">
                <div class="flex items-center justify-center border border-stone-700 rounded-lg bg-stone-900 shrink-0">
                    <button wire:click="decrement" class="px-4 py-3 text-stone-400 hover:text-white transition-colors">
                        <i class="fa-solid fa-minus text-xs"></i>
                    </button>
                    <span class="px-4 py-3 font-bold text-white text-sm">{{ $this->quantity }}</span>
                    <button wire:click="increment" class="px-4 py-3 text-stone-400 hover:text-white transition-colors">
                        <i class="fa-solid fa-plus text-xs"></i>
                    </button>
                </div>

                <button wire:click="addToCart"
                    class="flex-1 bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs uppercase tracking-widest py-3 px-6 rounded-lg flex items-center justify-center gap-2 transition-all shadow-lg shadow-amber-600/20">
                    <i class="fa-solid fa-bag-shopping text-sm"></i>
                    <span>Añadir al carrito</span>
                </button>
            </div>

            @if ($this->product->stock > 0)
                <p class="text-emerald-500 text-xs mt-4 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> En stock ({{ $this->product->stock }}
                    disponibles)
                </p>
            @else
                <p class="text-red-500 text-xs mt-4 flex items-center gap-2">
                    <i class="fa-solid fa-circle-exclamation"></i> Producto agotado
                </p>
            @endif
        </div>
    </div>
</div>
