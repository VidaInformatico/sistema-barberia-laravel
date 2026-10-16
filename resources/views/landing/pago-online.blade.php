<x-app-layout>
    <div class="min-h-screen bg-gray-950 text-white pt-28 pb-24">
        <div class="max-w-5xl mx-auto px-6">

            <div class="flex items-center justify-center gap-4 mb-12 text-[10px] font-bold uppercase tracking-widest text-gray-500">
                <a href="{{ route('reservation', $booking->services->first()->service->slug ?? '') }}" class="text-gray-400 hover:text-amber-500 transition-colors">
                    01. Agenda
                </a>
                
                <i class="fa-solid fa-chevron-right text-[8px]"></i>
                
                <a href="{{ route('confirmation', ['booking_number' => $booking->booking_number]) }}" class="text-gray-400 hover:text-amber-500 transition-colors">
                    02. Confirmar Cita
                </a>
                
                <i class="fa-solid fa-chevron-right text-[8px]"></i>
                
                <span class="text-amber-500 border-b border-amber-500 pb-1">03. Pago online</span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                <div class="lg:col-span-7 space-y-6">
                    <div
                        class="bg-gray-900 border border-white/10 p-6 sm:p-8 rounded-2xl shadow-2xl space-y-6 backdrop-blur-xl">
                        <h2
                            class="text-xs font-bold uppercase tracking-widest text-amber-500 mb-6 flex items-center gap-2">
                            <i class="fa-solid fa-credit-card text-xs"></i> Pago online
                        </h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">

                            @if (!empty($company->extra_config['mercadopago']['key']) && !empty($preferenceId))
                                <label class="group relative cursor-pointer">
                                    <input type="radio" name="pay" value="mercadopago" class="peer hidden"
                                        checked>
                                    <div
                                        class="h-full border border-white/10 bg-gray-950/60 rounded-2xl p-5 transition-all duration-300 peer-checked:border-amber-500 peer-checked:bg-amber-500/5 peer-checked:shadow-[0_0_25px_rgba(245,158,11,0.15)] hover:border-white/20 hover:bg-gray-950">
                                        <div class="flex flex-col h-full justify-between gap-5">
                                            <div class="flex justify-between items-start">
                                                <div
                                                    class="p-3 bg-amber-500/10 rounded-xl text-amber-400 group-hover:scale-110 transition-transform">
                                                    <i class="fa-solid fa-handshake text-xl"></i>
                                                </div>
                                                <div
                                                    class="w-5 h-5 rounded-full border-2 border-white/20 peer-checked:border-amber-500 flex items-center justify-center peer-checked:bg-amber-500 transition-all">
                                                    <div
                                                        class="w-2 h-2 rounded-full bg-gray-950 opacity-0 peer-checked:opacity-100 transition-opacity">
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <p class="font-bold text-white text-base leading-none mb-1">Mercado Pago
                                                </p>
                                                <p class="text-gray-400 text-xs font-medium">Hasta 12 cuotas sin interés
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            @endif

                            @if (!empty($company->extra_config['paypal']['client_id']))
                                <label class="group relative cursor-pointer">
                                    <input type="radio" name="pay" value="paypal" class="peer hidden"
                                        {{ empty($company->extra_config['mercadopago']['key']) || empty($preferenceId) ? 'checked' : '' }}>
                                    <div
                                        class="h-full border border-white/10 bg-gray-950/60 rounded-2xl p-5 transition-all duration-300 peer-checked:border-blue-500 peer-checked:bg-blue-500/5 peer-checked:shadow-[0_0_25px_rgba(59,130,246,0.15)] hover:border-white/20 hover:bg-gray-950">
                                        <div class="flex flex-col h-full justify-between gap-5">
                                            <div class="flex justify-between items-start">
                                                <div
                                                    class="p-3 bg-blue-500/10 rounded-xl text-blue-400 group-hover:scale-110 transition-transform">
                                                    <i class="fa-brands fa-paypal text-xl"></i>
                                                </div>
                                                <div
                                                    class="w-5 h-5 rounded-full border-2 border-white/20 peer-checked:border-blue-500 flex items-center justify-center peer-checked:bg-blue-500 transition-all">
                                                    <div
                                                        class="w-2 h-2 rounded-full bg-gray-950 opacity-0 peer-checked:opacity-100 transition-opacity">
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <p class="font-bold text-white text-base leading-none mb-1">PayPal</p>
                                                <p class="text-gray-400 text-xs font-medium">Global y ultra seguro</p>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            @endif

                        </div>

                        <div id="paypal-button-container" class="mt-6 hidden animate-fade-up"></div>

                        {{-- Botón de Procesar Pago: Se muestra solo si existe Mercado Pago O PayPal configurado --}}
                        @if (
                            (!empty($company->extra_config['mercadopago']['key']) && !empty($preferenceId)) ||
                                !empty($company->extra_config['paypal']['client_id']))
                            <button id="btn-pay"
                                class="w-full bg-gradient-to-r from-amber-600 to-amber-500 text-white px-8 py-4 rounded-xl font-bold uppercase tracking-wider transition-all duration-300 hover:from-amber-500 hover:to-amber-400 hover:shadow-lg hover:shadow-amber-500/25 active:scale-[0.98] flex items-center justify-center gap-3">
                                <span>PROCESAR PAGO</span>
                                <i class="fa-solid fa-bolt-lightning animate-pulse"></i>
                            </button>
                        @else
                            <div class="bg-amber-500/10 border border-amber-500/20 rounded-xl p-4 text-center">
                                <p class="text-xs text-amber-400 font-medium">
                                    No hay pasarelas de pago online configuradas en este momento. Por favor, selecciona
                                    el pago presencial.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="lg:col-span-5 space-y-4 lg:sticky lg:top-24">
                    <div
                        class="bg-gray-900 border border-amber-500/20 p-6 sm:p-8 rounded-2xl shadow-2xl backdrop-blur-xl">
                        <h2
                            class="text-xs font-bold uppercase tracking-widest text-amber-500 mb-6 border-b border-white/5 pb-3">
                            Resumen del Ticket
                        </h2>

                        <div class="space-y-4">
                            <div class="bg-gray-950/60 border border-white/5 p-4 rounded-xl flex items-start gap-4">
                                <div
                                    class="w-12 h-12 bg-amber-500/10 text-amber-500 flex items-center justify-center text-xl border border-amber-500/20 rounded-xl shrink-0">
                                    <i class="fa-solid fa-chair"></i>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">
                                        Servicio elegido
                                    </span>
                                    @foreach ($booking->services as $service)
                                        <span
                                            class="text-sm font-black text-white uppercase">{{ $service->service_name }}</span>
                                        <span class="text-[11px] text-gray-500 font-medium mt-0.5">
                                            Con: {{ $booking->barber->user->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <div class="col-span-2 bg-gray-950/40 border border-white/5 p-3.5 rounded-xl">
                                    <span
                                        class="block text-[9px] font-bold text-gray-500 uppercase tracking-widest mb-0.5">Fecha</span>
                                    <span class="text-xs font-bold text-white uppercase tracking-wider">
                                        {{ \Carbon\Carbon::parse($booking->booking_date)->locale('es')->isoFormat('dddd, D [de] MMMM [del] YYYY') }}
                                    </span>
                                </div>

                                <div class="bg-gray-950/40 border border-white/5 p-3.5 rounded-xl text-center">
                                    <span
                                        class="block text-[9px] font-bold text-gray-500 uppercase tracking-widest mb-0.5">Hora
                                        Inicio</span>
                                    <span class="text-xs font-bold text-amber-500 tracking-wider">
                                        {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}
                                    </span>
                                </div>

                                <div class="bg-gray-950/40 border border-white/5 p-3.5 rounded-xl text-center">
                                    <span
                                        class="block text-[9px] font-bold text-gray-500 uppercase tracking-widest mb-0.5">Hora
                                        Fin</span>
                                    <span class="text-xs font-bold text-amber-500 tracking-wider">
                                        {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                    </span>
                                </div>
                            </div>

                            <div class="border-t border-white/5 pt-4 space-y-2.5 text-xs font-medium">
                                <div class="flex justify-between text-gray-400">
                                    <span>Total Servicio</span>
                                    <span
                                        class="text-white font-bold">{{ $company->currency_code }}{{ $booking->total_price }}</span>
                                </div>
                                <div class="flex justify-between text-gray-400">
                                    <span>Cargos por gestión online</span>
                                    <span
                                        class="text-emerald-500 uppercase font-bold text-[10px] tracking-wider">Gratis</span>
                                </div>
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-center gap-2 text-[10px] text-gray-500 font-bold uppercase tracking-wider mt-6 pt-4 border-t border-white/5">
                            <i class="fa-solid fa-shield-halved text-amber-500/60"></i> Entorno de cifrado SSL seguro
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('js')
        @if (!empty($preferenceId) && !empty($company->extra_config['mercadopago']['key']))
            <script src="https://sdk.mercadopago.com/js/v2"></script>
        @endif

        @if (!empty($company->extra_config['paypal']['client_id']))
            <script
                src="https://www.paypal.com/sdk/js?client-id={{ $company->extra_config['paypal']['client_id'] }}&currency={{ $company->extra_config['paypal']['currency'] ?? 'USD' }}">
            </script>
        @endif

        <script>
            function initPaymentGateway() {
                const paypalContainer = document.getElementById('paypal-button-container');
                let btnPay = document.querySelector('#btn-pay');
                const radioButtons = document.querySelectorAll('input[name="pay"]');

                if (!radioButtons.length) return;

                function updatePaymentVisibility() {
                    const checkedRadio = document.querySelector('input[name="pay"]:checked');
                    if (!checkedRadio) return;

                    const currentBtnPay = document.querySelector('#btn-pay');

                    if (checkedRadio.value === 'paypal') {
                        if (currentBtnPay) currentBtnPay.style.display = 'none';
                        if (paypalContainer) {
                            paypalContainer.classList.remove('hidden');
                            initPayPalButtons();
                        }
                    } else {
                        if (currentBtnPay) currentBtnPay.style.display = 'flex';
                        if (paypalContainer) paypalContainer.classList.add('hidden');
                    }
                }

                radioButtons.forEach(radio => {
                    const label = radio.closest('label');
                    if (label) {
                        label.addEventListener('click', () => {
                            setTimeout(updatePaymentVisibility, 10);
                        });
                    }
                    radio.addEventListener('change', updatePaymentVisibility);
                });

                updatePaymentVisibility();

                // Inicializar Mercado Pago solo si existe la llave
                @if (!empty($company->extra_config['mercadopago']['key']) && !empty($preferenceId))
                    const mpKey = "{{ $company->extra_config['mercadopago']['key'] }}";
                    const mpLocale = "{{ $company->extra_config['mercadopago']['locale'] ?? 'es-PE' }}";

                    if (mpKey && !window.mpInstance) {
                        window.mpInstance = new MercadoPago(mpKey, {
                            locale: mpLocale
                        });
                    }

                    if (btnPay) {
                        const newBtnPay = btnPay.cloneNode(true);
                        btnPay.parentNode.replaceChild(newBtnPay, btnPay);

                        newBtnPay.addEventListener('click', function() {
                            const checkedRadio = document.querySelector('input[name="pay"]:checked');
                            if (!checkedRadio) return;

                            const method = checkedRadio.value;

                            if (method === 'mercadopago') {
                                if (window.mpInstance) {
                                    window.mpInstance.checkout({
                                        preference: {
                                            id: '{{ $preferenceId }}'
                                        },
                                        autoOpen: true
                                    });
                                }
                            }
                        });
                    }
                @endif
            }

            function initPayPalButtons() {
                const paypalContainer = document.getElementById('paypal-button-container');
                if (!paypalContainer || paypalContainer.innerHTML !== '') return;
                if (typeof paypal === 'undefined') return;

                paypal.Buttons({
                    createOrder: function(data, actions) {
                        return fetch("{{ route('paypal.create') }}", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                },
                                body: JSON.stringify({
                                    booking_id: "{{ $booking->id }}"
                                })
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Error al generar la orden de pago.');
                                }
                                return response.json();
                            })
                            .then(order => {
                                if (order.error) {
                                    alert(order.error);
                                    throw new Error(order.error);
                                }
                                return order.id;
                            })
                            .catch(err => {
                                console.error('Error en createOrder:', err);
                            });
                    },

                    onApprove: function(data, actions) {
                        return fetch("{{ route('paypal.capture') }}", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                },
                                body: JSON.stringify({
                                    booking_id: "{{ $booking->id }}",
                                    orderID: data.orderID
                                })
                            })
                            .then(response => {
                                if (!response.ok) {
                                    return response.json().then(errData => {
                                        throw new Error(errData.error ||
                                            'Ocurrió un error al procesar el pago.');
                                    });
                                }
                                return response.json();
                            })
                            .then(details => {
                                if (details.error || details.name === 'INVALID_REQUEST') {
                                    alert('Error en la transacción: ' + (details.error || details.message));
                                    return;
                                }
                                window.location.href = "{{ route('payment.success') }}";
                            })
                            .catch(error => {
                                console.error('Error en capture:', error);
                                alert(error.message || 'No se pudo completar el pago.');
                            });
                    },

                    onCancel: function(data) {
                        alert("Pago cancelado por el usuario.");
                    },

                    onError: function(err) {
                        console.error('Error en el SDK de PayPal:', err);
                        alert("Ocurrió un problema con la pasarela de PayPal.");
                    }
                }).render('#paypal-button-container');
            }

            document.addEventListener("DOMContentLoaded", initPaymentGateway);
        </script>
    @endpush

</x-app-layout>
