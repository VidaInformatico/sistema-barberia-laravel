<x-app-layout>
    <div class="min-h-screen bg-stone-950 text-white font-sans antialiased mt-20">

        <header class="relative w-full py-20 overflow-hidden bg-stone-900 border-b border-stone-800">
            <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-20 pointer-events-none"
                style="background-image: url('https://images.unsplash.com/photo-1599351431202-180f0b484501?auto=format&fit=crop&q=80&w=1800');">
            </div>
            <div class="absolute inset-0 bg-gradient-to-b from-stone-950/20 via-transparent to-stone-950"></div>

            <div class="relative mx-auto px-6 text-center z-10">
                <span class="text-amber-500 font-bold uppercase tracking-widest text-xs md:text-sm block mb-3">
                    {{ $barber->specialty }}
                </span>

                <h1 class="text-2xl md:text-4xl font-black tracking-tighter uppercase leading-none mb-4">
                    {{ $barber->full_name }}
                </h1>

                <div class="flex justify-center gap-6 text-stone-400 text-sm md:text-base">
                    <span>{{ $barber->experience_years }} años de experiencia</span>
                    <span>|</span>
                    <span class="text-amber-500">{{ $barber->specialty }}</span>
                </div>
            </div>
        </header>

        <div class="max-w-5xl mx-auto px-6 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12">

                <div class="md:col-span-2 space-y-12">
                    <div>
                        <h3 class="text-xl font-bold uppercase tracking-widest text-white mb-4">Sobre mí</h3>
                        <p class="text-stone-400 leading-relaxed">{{ $barber->bio }}</p>
                    </div>

                    @if ($barber->galleryPhotos->count() > 0)
                        <div>
                            <h3 class="text-xl font-bold uppercase tracking-widest text-white mb-6">Galería de trabajos
                            </h3>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                @foreach ($barber->galleryPhotos as $photo)
                                    <div
                                        class="aspect-square overflow-hidden rounded-xl bg-stone-900 border border-stone-800">
                                        <img src="{{ asset('storage/' . $photo->image) }}"
                                            alt="Trabajo de {{ $barber->full_name }}"
                                            class="w-full h-full object-cover hover:scale-110 transition-transform duration-500">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="bg-stone-900 p-6 rounded-2xl border border-stone-800">
                        <h3 class="text-xl font-bold uppercase tracking-widest text-white mb-6">Selecciona un servicio
                        </h3>

                        <div class="space-y-4">
                            @foreach ($barber->services as $service)
                                <label
                                    class="group flex items-center justify-between cursor-pointer border-b border-stone-800 pb-4 last:border-0 hover:bg-stone-800/50 p-2 rounded-lg transition-colors">
                                    <div class="flex items-center gap-3">
                                        <input type="radio" name="selectedServiceId" value="{{ $service->slug }}"
                                            class="w-4 h-4 text-amber-500 bg-stone-950 border-stone-700 focus:ring-amber-500 focus:ring-2">

                                        <span class="text-stone-300 group-hover:text-white transition-colors">
                                            {{ $service->name }}
                                        </span>
                                    </div>

                                    <span class="text-amber-500 font-bold">
                                        {{ $company->currency_code }}{{ $service->pivot->custom_price ?? $service->price }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-stone-900 p-6 rounded-2xl border border-stone-800 text-center sticky top-28">
                        <div
                            class="w-32 h-32 mx-auto bg-stone-800 rounded-full mb-6 overflow-hidden border-4 border-stone-700 flex items-center justify-center">
                            @if (!empty($barber->user->avatar))
                                <img src="{{ asset('storage/' . $barber->user->avatar) }}"
                                    alt="{{ $barber->full_name }}" class="w-full h-full object-cover">
                            @else
                                <div class="text-4xl font-black text-amber-500 uppercase">
                                    {{ substr($barber->user->name, 0, 1) }}{{ substr($barber->user->last_name ?? '', 0, 1) }}
                                </div>
                            @endif
                        </div>

                        <button type="button" onclick="redirectBooking()"
                            class="block w-full bg-amber-600 hover:bg-amber-500 text-white font-bold py-3 px-6 rounded-lg transition-all mb-4">
                            RESERVAR CITA
                        </button>

                        @if ($barber->instagram)
                            <a href="https://instagram.com/{{ $barber->instagram }}" target="_blank"
                                class="text-stone-400 hover:text-amber-500 text-sm transition-colors">
                                Ver Portafolio en Instagram
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="border-t border-stone-800">
            @livewire('front.barber-ratings', ['barberId' => $barber->id])
        </div>

    </div>

    @push('js')
        <script>
            function redirectBooking() {
                const selectedRadio = document.querySelector('input[name="selectedServiceId"]:checked');

                if (!selectedRadio) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Atención',
                        text: 'Por favor, selecciona un servicio primero.',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Entendido'
                    });
                    return;
                }

                const serviceSlug = selectedRadio.value;
                const barberSlug = "{{ $barber->user->slug }}";

                window.location.href = `/reservation/${serviceSlug}/${barberSlug}`;
            }
        </script>
    @endpush
</x-app-layout>
