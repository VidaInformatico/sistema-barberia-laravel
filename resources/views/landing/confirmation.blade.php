<x-app-layout>
    <div class="min-h-screen bg-gray-950 text-white pt-28 pb-24">
        <div class="max-w-5xl mx-auto px-6">

            <div
                class="flex items-center justify-center gap-4 mb-12 text-[10px] font-bold uppercase tracking-widest text-gray-500">
                <a href="{{ route('reservation', $booking->services->first()->service->slug ?? '') }}" class="text-gray-400 hover:text-amber-500 transition-colors">
                    01. Agenda
                </a>
                <i class="fa-solid fa-chevron-right text-[8px]"></i>
                <span class="text-amber-500 border-b border-amber-500 pb-1">02. Procesar Cita</span>
                <i class="fa-solid fa-chevron-right text-[8px]"></i>
                <span>03. Pago online</span>
            </div>

            <livewire:front.confirmation :booking="$booking"/>

        </div>
    </div>
</x-app-layout>
