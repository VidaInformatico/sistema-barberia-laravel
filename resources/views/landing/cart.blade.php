<x-app-layout>
    <div class="pt-20 bg-white min-h-screen">
        
        <!-- Hero Section con temática de Barbería e Iconos de Ruta -->
        <section class="relative w-full h-[45vh] flex items-center justify-center overflow-hidden bg-stone-900 border-b border-stone-800">
            <div class="absolute inset-0 z-0">
                <!-- Overlay oscuro para resaltar el texto y darle estilo premium de barbería -->
                <div class="absolute inset-0 bg-stone-950/80 z-10"></div>
                <div 
                    class="absolute inset-0 bg-cover bg-center bg-fixed"
                    style="background-image: url('https://images.unsplash.com/photo-1503951914875-452162b0f3f1?q=80&w=2070&auto=format&fit=crop');"
                ></div>
            </div>

            <div class="relative z-20 text-center px-6">
                <nav class="flex items-center justify-center space-x-2 text-xs md:text-sm tracking-widest text-amber-500/90 uppercase mb-4 font-medium">
                    <a href="{{ route('welcome') }}" class="flex items-center hover:text-amber-400 transition-colors">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Inicio
                    </a>
                    <span class="text-stone-600">/</span>
                    <span class="flex items-center text-stone-300">
                        <svg class="w-4 h-4 mr-1.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        Carrito
                    </span>
                </nav>

                <h1 class="text-4xl md:text-6xl font-black tracking-[0.2em] uppercase text-white mb-4">
                    Tu Carrito
                </h1>
                <div class="w-16 h-1 bg-amber-600 mx-auto"></div>
            </div>
        </section>

        <main class="relative z-30 -mt-12 pb-20 px-6">
            <div class="max-w-5xl mx-auto">
                <div class="bg-white border border-stone-200 p-6 md:p-10 shadow-xl rounded-sm">
                    <livewire:front.cart />
                </div>
            </div>
        </main>
    </div>
</x-app-layout>