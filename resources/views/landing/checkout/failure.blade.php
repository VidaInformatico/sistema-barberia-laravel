<x-app-layout>

    <div class="mt-20"></div>

    <div class="min-h-screen bg-slate-50 font-sans antialiased text-slate-800">
        
        <div class="relative bg-gradient-to-b from-rose-950 via-rose-900 to-rose-950 text-white pt-12 pb-24 px-4 sm:px-6 lg:px-8 border-b border-rose-800/40 overflow-hidden">

            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-rose-500/10 via-transparent to-transparent pointer-events-none"></div>

            <div class="relative max-w-2xl mx-auto text-center space-y-3">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-rose-500/10 border border-rose-400/20 text-rose-400 shadow-inner">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>

                <p class="text-[11px] font-semibold tracking-[0.25em] text-rose-400 uppercase">
                    Error en el Pago
                </p>

                <h1 class="text-2xl sm:text-3xl font-light tracking-tight text-rose-50">
                    No pudimos procesar la compra
                </h1>

                <p class="text-xs text-rose-200/70 font-normal max-w-md mx-auto leading-relaxed">
                    Tu tarjeta o método de pago no pudo ser procesado. Por favor, verifica los datos e inténtalo de nuevo.
                </p>
            </div>
        </div>

        <div class="max-w-md mx-auto px-4 -mt-10 pb-16 relative z-10">
            <div class="bg-white rounded-2xl shadow-[0_10px_30px_rgba(0,0,0,0.04)] border border-slate-200/70 p-6 space-y-6">
                
                <div class="space-y-3 text-xs">
                    <div class="flex items-center justify-between py-1">
                        <span class="text-slate-500 font-medium">Estado del Pago</span>
                        <span class="inline-flex items-center gap-1.5 font-medium text-rose-700 bg-rose-50 px-2.5 py-0.5 rounded-full border border-rose-200/60 text-[11px]">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                            Rechazado
                        </span>
                    </div>

                    <div class="border-t border-slate-100"></div>

                    <div class="flex items-center justify-between py-1 text-slate-500">
                        <span>Método</span>
                        <span class="font-medium text-slate-700 uppercase tracking-wider text-[11px]">Pasarela de Pago</span>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-2.5 pt-2">
                    <a href="/checkout" 
                       class="flex-1 inline-flex justify-center items-center px-4 py-2.5 bg-rose-700 hover:bg-rose-800 text-white text-xs font-medium rounded-xl transition-colors duration-150 shadow-sm shadow-rose-700/20 active:scale-[0.99]">
                        Reintentar pago
                    </a>
                    
                    <a href="{{ route('welcome') }}" 
                       class="flex-1 inline-flex justify-center items-center px-4 py-2.5 bg-slate-50 hover:bg-slate-100 text-slate-600 border border-slate-200/80 text-xs font-medium rounded-xl transition-colors duration-150 active:scale-[0.99]">
                        Cancelar y volver
                    </a>
                </div>

            </div>
        </div>

    </div>
</x-app-layout>