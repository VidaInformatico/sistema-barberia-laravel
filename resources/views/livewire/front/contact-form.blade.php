<section class="scroll-mt-20 bg-stone-900 py-16 px-4 sm:px-6 lg:px-8 border-t border-stone-800" id="section-contact">
    <div class="mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-12 items-start">
            <div class="lg:col-span-2 space-y-8">
                <div>
                    <h2 class="text-base font-semibold text-amber-500 uppercase tracking-wide">¿Listo para un
                        cambio?</h2>
                    <p class="mt-2 text-3xl font-extrabold text-white sm:text-4xl">Contactenos</p>
                    <p class="mt-4 text-stone-400 leading-relaxed">
                        Asegura tu espacio con nuestros maestros barberos. También puedes dejarnos tus dudas y te
                        responderemos en minutos.
                    </p>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center gap-4 text-stone-300">
                        <div class="p-3 bg-stone-950 rounded-lg text-amber-500 border border-stone-800">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-stone-500 uppercase font-semibold">Ubicación</p>
                            <p class="text-sm font-medium">{{ $company->address }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 text-stone-300">
                        <div class="p-3 bg-stone-950 rounded-lg text-amber-500 border border-stone-800">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-stone-500 uppercase font-semibold">Horario de Atención</p>
                            <p class="text-sm font-medium">{{ $company->hours }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-3 bg-stone-950 p-8 border border-stone-800 shadow-2xl">
                @if (session()->has('message'))
                    <div class="mb-6 p-4 bg-green-900/20 text-green-500 border border-green-900 rounded-lg">
                        {{ session('message') }}
                    </div>
                @endif

                <form wire:submit="send" class="space-y-6">
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-stone-400 mb-2">Nombre Completo</label>
                            <div class="relative rounded-lg shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user text-stone-500 text-sm"></i>
                                </div>
                                <input type="text" wire:model="name" placeholder="Tu nombre completo"
                                    class="w-full bg-stone-900 border border-stone-800 rounded-lg pl-10 pr-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-sm">
                            </div>
                            @error('name')
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-stone-400 mb-2">Teléfono</label>
                            <div class="relative rounded-lg shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-phone text-stone-500 text-sm"></i>
                                </div>
                                <input type="tel" wire:model="phone" placeholder="Tu número"
                                    class="w-full bg-stone-900 border border-stone-800 rounded-lg pl-10 pr-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-sm">
                            </div>
                            @error('phone')
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-stone-400 mb-2">Asunto</label>
                            <div class="relative rounded-lg shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-bookmark text-stone-500 text-sm"></i>
                                </div>
                                <input type="text" wire:model="subject" placeholder="¿En qué podemos ayudarte?"
                                    class="w-full bg-stone-900 border border-stone-800 rounded-lg pl-10 pr-4 py-2.5 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-sm">
                            </div>
                            @error('subject')
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-stone-400 mb-2">Notas</label>
                        <div class="relative rounded-lg shadow-sm">
                            <div class="absolute top-3 left-0 pl-3 flex items-start pointer-events-none">
                                <i class="fas fa-comment-alt text-stone-500 text-sm"></i>
                            </div>
                            <textarea wire:model="message" rows="4" placeholder="Detalles adicionales..."
                                class="w-full bg-stone-900 border border-stone-800 rounded-lg pl-10 pr-4 py-3 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-sm"></textarea>
                        </div>
                        @error('message')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" wire:loading.attr="disabled"
                        class="w-full bg-amber-500 text-stone-950 font-bold uppercase tracking-wider py-3 px-6 rounded-lg hover:bg-amber-400 disabled:opacity-50 transition text-sm">
                        <span wire:loading.remove wire:target="send">Enviar</span>
                        <span wire:loading wire:target="send">Enviando...</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
