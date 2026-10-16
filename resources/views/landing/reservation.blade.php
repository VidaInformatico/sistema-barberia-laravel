<x-app-layout>
    <div class="min-h-screen bg-gray-950 text-white pt-28 pb-24">
        <div class="max-w-6xl mx-auto px-6">

            <div class="mb-12 border-b border-white/10 pb-6">
                <span class="text-amber-500 font-bold uppercase tracking-widest text-xs block mb-2">Plataforma de
                    Citas
                </span>
                <h1 class="text-xl md:text-3xl font-black tracking-tighter uppercase">Reserva tu <span
                        class="text-transparent bg-clip-text bg-gradient-to-r from-amber-400 to-amber-600">Cita.</span>
                </h1>
            </div>

            <div class="mb-10">

                @livewire('front.reservations', ['serviceId' => $service->id, 'barber' => $barber])

            </div>

            @livewire('front.service-ratings', ['serviceId' => $service->id])

        </div>
    </div>
</x-app-layout>
