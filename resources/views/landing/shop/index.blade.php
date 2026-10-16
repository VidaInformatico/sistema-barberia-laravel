<x-app-layout>
    <div class="min-h-screen bg-stone-950 text-white font-sans antialiased mt-20">

        <header class="relative w-full py-20 overflow-hidden bg-stone-900 border-b border-stone-800">
            <div class="absolute inset-0 bg-cover bg-center bg-no-repeat bg-fixed opacity-20 pointer-events-none"
                style="background-image: url('https://images.unsplash.com/photo-1621605815971-fbc98d665033?auto=format&fit=crop&q=80&w=1800');">
            </div>
            <div class="absolute inset-0 bg-gradient-to-b from-stone-950/20 via-transparent to-stone-950"></div>

            <div class="relative max-w-7xl mx-auto px-6 text-center z-10">
                <span class="text-amber-500 font-bold uppercase tracking-widest text-xs md:text-sm block mb-3">
                    Apotecario & Estilo
                </span>
                <h1 class="text-2xl md:text-4xl font-black tracking-tighter uppercase leading-none mb-4">
                    Nuestros <span
                        class="text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-amber-600">Productos.</span>
                </h1>
                <p class="text-stone-400 text-sm md:text-base max-w-xl mx-auto leading-relaxed">
                    La misma línea de productos premium que usamos en tu servicio, ahora disponibles para mantener tu
                    identidad impecable en casa.
                </p>
            </div>
        </header>

        @livewire('front.store')
    </div>
</x-app-layout>
