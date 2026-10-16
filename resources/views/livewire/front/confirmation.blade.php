<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

    <div class="lg:col-span-7 space-y-6">
        <div class="bg-gray-900 border border-white/10 p-6 rounded-sm">
            <h2 class="text-xs font-bold uppercase tracking-widest text-amber-500 mb-6 flex items-center gap-2">
                <i class="fa-solid fa-user-tie text-xs"></i> Información del Cliente
            </h2>

            <div class="space-y-4">
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-2">Nombre
                        Completo *</label>
                    <input type="text" placeholder="Ej. Alejandro Mendoza"
                        value="{{ $booking->client->name . ' ' . $booking->client->last_name }}"
                        class="w-full bg-gray-950 border border-white/10 text-white px-4 py-3 text-sm rounded-sm focus:outline-none focus:border-amber-500 transition-colors">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-2">Teléfono
                            Celular *</label>
                        <input type="tel" name="phone" required placeholder="+51 999 999 999"
                            value="{{ $booking->client->phone }}"
                            class="w-full bg-gray-950 border border-white/10 text-white px-4 py-3 text-sm rounded-sm focus:outline-none focus:border-amber-500 transition-colors">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-2">Correo
                            Electrónico *</label>
                        <input type="email" name="email" required placeholder="alejandro@empresa.com"
                            value="{{ $booking->client->email }}"
                            class="w-full bg-gray-950 border border-white/10 text-white px-4 py-3 text-sm rounded-sm focus:outline-none focus:border-amber-500 transition-colors">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-2">Notas
                        Especiales u Observaciones (Opcional)</label>
                    <textarea wire:model="notes" rows="2"
                        placeholder="Alguna alergia a productos, preferencia de música o detalle técnico..."
                        class="w-full bg-gray-950 border border-white/10 text-white p-4 text-sm rounded-sm focus:outline-none focus:border-amber-500 transition-colors resize-none"></textarea>
                </div>
            </div>
        </div>

        <div class="bg-gray-900 border border-white/10 p-6 rounded-sm space-y-6">
            <h2 class="text-xs font-bold uppercase tracking-widest text-amber-500 mb-6 flex items-center gap-2">
                <i class="fa-solid fa-credit-card text-xs"></i> Garantía y Pago
            </h2>

            <div class="space-y-3">
                <label
                    class="group relative border border-white/10 p-4 rounded-sm flex items-start gap-4 cursor-pointer hover:border-amber-500/50 transition-all bg-gray-950/40 has-[:checked]:border-amber-500 has-[:checked]:bg-amber-500/5">
                    <input type="radio" wire:model.live="payment_method" value="local" class="mt-1 accent-amber-500">
                    <div class="flex flex-col">
                        <span
                            class="text-sm font-bold uppercase tracking-tight text-white group-hover:text-amber-400 transition-colors">
                            Pago Presencial en Sucursal
                        </span>
                        <span class="text-xs text-gray-400 font-medium mt-0.5">
                            Liquida el total en efectivo, tarjeta o transferencia al finalizar tu servicio en la barbería.
                        </span>
                    </div>
                </label>
            
                @if (
                    (!empty($company->extra_config['mercadopago']['key']) && !empty($preferenceId)) || 
                    !empty($company->extra_config['paypal']['client_id'])
                )
                    <a href="{{ route('payment.index', $booking->booking_number) }}"
                        class="group relative border border-white/10 p-4 rounded-sm flex items-start gap-4 cursor-pointer hover:border-amber-500/50 transition-all bg-gray-950/40 block text-decoration-none">
                        <div
                            class="mt-1 flex items-center justify-center w-4 h-4 rounded-full border border-white/25 group-hover:border-amber-500">
                        </div>
                        <div class="flex flex-col w-full">
                            <div class="flex justify-between items-center w-full">
                                <span
                                    class="text-sm font-bold uppercase tracking-tight text-white group-hover:text-amber-400 transition-colors">
                                    Pago en Línea Anticipado
                                </span>
                                <div class="flex gap-1 text-[16px] text-gray-400">
                                    <i class="fa-brands fa-cc-visa"></i>
                                    <i class="fa-brands fa-cc-mastercard"></i>
                                    <i class="fa-brands fa-cc-amex"></i>
                                </div>
                            </div>
                            <span class="text-xs text-gray-400 font-medium mt-0.5">
                                Asegura tu slot congelando la tarifa mediante nuestra pasarela encriptada SSL.
                            </span>
                        </div>
                    </a>
                @endif
            </div>

        </div>

        <button type="submit"
            class="w-full lg:hidden bg-amber-500 hover:bg-amber-600 text-gray-950 font-black uppercase tracking-widest text-xs py-4 rounded-sm transition-all duration-300 shadow-lg mt-4">
            Finalizar y Confirmar Reserva
        </button>
    </div>

    <div class="lg:col-span-5 space-y-4 lg:sticky lg:top-24">
        <div class="bg-gray-900 border border-amber-500/20 p-6 rounded-sm shadow-xl">
            <h2 class="text-xs font-bold uppercase tracking-widest text-amber-500 mb-6 border-b border-white/5 pb-3">
                Resumen del Ticket
            </h2>

            <div class="space-y-4">
                <div class="bg-gray-950/60 border border-white/5 p-4 rounded-sm flex items-start gap-4">
                    <div
                        class="w-12 h-12 bg-amber-500/10 text-amber-500 flex items-center justify-center text-xl border border-amber-500/20 rounded-sm">
                        <i class="fa-solid fa-chair"></i>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">
                            Servicio elegido
                        </span>
                        @foreach ($booking->services as $service)
                            <span class="text-sm font-black text-white uppercase">{{ $service->service_name }}</span>
                            <span class="text-[11px] text-gray-500 font-medium mt-0.5">
                                Con: {{ $booking->barber->user->name }}
                            </span>
                        @endforeach

                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div class="col-span-2 bg-gray-950/40 border border-white/5 p-3 rounded-sm">
                        <span
                            class="block text-[9px] font-bold text-gray-500 uppercase tracking-widest mb-0.5">Fecha</span>
                        <span class="text-xs font-bold text-white uppercase tracking-wider">
                            {{ \Carbon\Carbon::parse($booking->booking_date)->locale('es')->isoFormat('dddd, D [de] MMMM [del] YYYY') }}
                        </span>
                    </div>

                    <div class="bg-gray-950/40 border border-white/5 p-3 rounded-sm text-center">
                        <span class="block text-[9px] font-bold text-gray-500 uppercase tracking-widest mb-0.5">Hora
                            Inicio</span>
                        <span class="text-xs font-bold text-amber-500 tracking-wider">
                            {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}
                        </span>
                    </div>

                    <div class="bg-gray-950/40 border border-white/5 p-3 rounded-sm text-center">
                        <span class="block text-[9px] font-bold text-gray-500 uppercase tracking-widest mb-0.5">Hora
                            Fin</span>
                        <span class="text-xs font-bold text-amber-500 tracking-wider">
                            {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                        </span>
                    </div>
                </div>

                <div class="border-t border-white/5 pt-4 space-y-2.5 text-xs font-medium">
                    <div class="flex justify-between text-gray-400">
                        <span>Total Servicio</span>
                        <span class="text-white">{{ $company->currency_code }}{{ $booking->total_price }}</span>
                    </div>
                    <div class="flex justify-between text-gray-400">
                        <span>Cargos por gestión online</span>
                        <span class="text-emerald-500 uppercase font-bold text-[10px] tracking-wider">Gratis</span>
                    </div>
                </div>
            </div>

            <button type="button" wire:click="processBooking"
                class="hidden lg:flex w-full bg-amber-500 hover:bg-amber-600 text-gray-950 font-black uppercase tracking-widest text-xs py-4 rounded-sm transition-all duration-300 shadow-lg active:scale-[0.99] items-center justify-center gap-2 mt-6">
                Finalizar y Reservar <i class="fa-solid fa-lock text-xs"></i>
            </button>

            <div
                class="flex items-center justify-center gap-2 text-[10px] text-gray-500 font-bold uppercase tracking-wider mt-4">
                <i class="fa-solid fa-shield-halved text-amber-500/60"></i> Entorno de cifrado SSL seguro
            </div>
        </div>
    </div>

</div>
