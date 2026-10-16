<x-app-layout>
    <div class="min-h-screen bg-stone-950 text-white font-sans antialiased mt-20">

        <header class="relative w-full py-20 overflow-hidden bg-stone-900 border-b border-stone-800">
            <div class="absolute inset-0 bg-cover bg-center bg-no-repeat bg-fixed opacity-20 pointer-events-none"
                style="background-image: url('https://images.unsplash.com/photo-1621605815971-fbc98d665033?auto=format&fit=crop&q=80&w=1800');">
            </div>
            <div class="absolute inset-0 bg-gradient-to-b from-stone-950/20 via-transparent to-stone-950"></div>

            <div class="relative mx-auto px-6 text-center z-10">
                <span class="text-amber-500 font-bold uppercase tracking-widest text-xs md:text-sm block mb-3">
                    {{ $product->category ? $product->category->name : 'Apotecario & Estilo' }}
                </span>

                <h1 class="text-2xl md:text-4xl font-black tracking-tighter uppercase leading-none mb-4">
                    {{ $product->name }}
                </h1>

                <p class="text-stone-400 text-sm md:text-base max-w-xl mx-auto leading-relaxed">
                    Detalle exclusivo de nuestro catálogo. Calidad para tu cuidado personal.
                </p>
            </div>
        </header>

        <div class="mx-auto px-6 py-12">
            @livewire('front.product-detail', ['slug' => $product->slug])
        </div>

        <div class="border-t border-stone-800">
            @livewire('front.product-ratings', ['productId' => $product->id])
        </div>

    </div>
</x-app-layout>
