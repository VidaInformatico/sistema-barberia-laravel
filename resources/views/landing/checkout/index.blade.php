<x-app-layout>
    <style>
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-up {
            animation: fadeUp 0.6s ease-out forwards;
        }

        .parallax-bg {
            background-attachment: fixed;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #cbd5e1;
        }
    </style>

    <div class="relative h-[300px] w-full overflow-hidden flex items-center justify-center parallax-bg bg-stone-900"
        style="background-image: url('https://images.unsplash.com/photo-1517832606293-7ae2fc73c79f?q=80&w=2070&auto=format&fit=crop');">

        <!-- Overlay oscuro para mantener la legibilidad y el estilo de barbería -->
        <div class="absolute inset-0 bg-stone-950/80"></div>

        <div class="relative z-10 text-center px-6 animate-fade-up">
            <h1 class="text-4xl md:text-5xl font-black text-white tracking-widest uppercase mb-4">
                Checkout
            </h1>

            <nav
                class="flex items-center justify-center space-x-2 text-xs md:text-sm tracking-widest text-amber-500/90 uppercase font-medium">
                <a href="{{ route('cart.index') }}" class="flex items-center hover:text-amber-400 transition-colors">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    Carrito
                </a>

                <span class="text-stone-600">/</span>

                <span class="flex items-center text-stone-300">
                    <svg class="w-4 h-4 mr-1.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                        </path>
                    </svg>
                    Pago Seguro
                </span>

                <span class="text-stone-600">/</span>

                <span class="flex items-center opacity-40 text-stone-400">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 13l4 4L19 7">
                        </path>
                    </svg>
                    Confirmación
                </span>
            </nav>
        </div>
    </div>

    <div class="mx-auto px-4 sm:px-6 lg:px-12 -mt-16 relative z-20 mb-20 font-sans">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

            <div class="lg:col-span-2 space-y-6">
                <div
                    class="bg-white/80 backdrop-blur-md rounded-[1.5rem] p-10 border border-white shadow-2xl shadow-slate-200/50 animate-fade-up">
                    <div class="flex items-center gap-4 mb-10">
                        <div
                            class="h-12 w-12 bg-amber-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-200 rotate-3 group-hover:rotate-0 transition-transform">
                            <i class="fa-solid fa-credit-card text-white text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-3xl font-black text-slate-900 tracking-tight">Método de pago</h2>
                            <p class="text-slate-500 font-medium text-sm text-balance">Todas las transacciones están
                                encriptadas y son seguras.
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                        @if (!empty($company->extra_config['mercadopago']['key']) && !empty($preferenceId))
                            <label class="group relative cursor-pointer">
                                <input type="radio" name="pay" value="mercadopago" class="peer hidden" checked>
                                <div
                                    class="h-full border-2 border-slate-100 rounded-3xl p-6 transition-all duration-300 peer-checked:border-blue-500 peer-checked:bg-blue-50/50 peer-checked:shadow-xl hover:shadow-lg group-active:scale-95">
                                    <div class="flex flex-col h-full justify-between gap-4">
                                        <div class="flex justify-between items-start">
                                            <div
                                                class="p-3 bg-blue-100 rounded-xl text-blue-600 group-hover:rotate-12 transition-transform">
                                                <i class="fa-solid fa-handshake text-2xl"></i>
                                            </div>
                                            <div
                                                class="w-4 h-4 rounded-full border-2 border-slate-200 peer-checked:border-blue-500 flex items-center justify-center group-peer-checked:bg-blue-500">
                                                <div
                                                    class="w-1.5 h-1.5 rounded-full bg-white opacity-0 group-peer-checked:opacity-100">
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <p class="font-black text-slate-800 text-lg leading-none mb-1">Mercado Pago
                                            </p>
                                            <p class="text-slate-500 text-xs font-medium">Hasta 12 cuotas sin interés
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
                                    class="h-full border-2 border-slate-100 rounded-3xl p-6 transition-all duration-300 peer-checked:border-indigo-600 peer-checked:bg-indigo-50/50 peer-checked:shadow-xl hover:shadow-lg group-active:scale-95">
                                    <div class="flex flex-col h-full justify-between gap-4">
                                        <div class="flex justify-between items-start">
                                            <div
                                                class="p-3 bg-indigo-100 rounded-xl text-blue-500 group-hover:rotate-12 transition-transform">
                                                <i class="fa-brands fa-paypal text-2xl"></i>
                                            </div>
                                            <div
                                                class="w-4 h-4 rounded-full border-2 border-slate-200 peer-checked:border-indigo-600 flex items-center justify-center">
                                            </div>
                                        </div>
                                        <div>
                                            <p class="font-black text-slate-800 text-lg leading-none mb-1">PayPal</p>
                                            <p class="text-slate-500 text-xs font-medium">Global y ultra seguro</p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        @endif

                        <label class="group relative cursor-pointer">
                            <input type="radio" name="pay" value="cash" class="peer hidden"
                                {{ (empty($company->extra_config['mercadopago']['key']) || empty($preferenceId)) && empty($company->extra_config['paypal']['client_id']) ? 'checked' : '' }}>
                            <div
                                class="h-full border-2 border-slate-100 rounded-3xl p-6 transition-all duration-300 peer-checked:border-emerald-500 peer-checked:bg-emerald-50/50 peer-checked:shadow-xl hover:shadow-lg group-active:scale-95">
                                <div class="flex flex-col h-full justify-between gap-4">
                                    <div class="flex justify-between items-start">
                                        <div
                                            class="p-3 bg-emerald-100 rounded-xl text-emerald-600 group-hover:rotate-12 transition-transform">
                                            <i class="fa-solid fa-truck-fast text-2xl"></i>
                                        </div>
                                        <div
                                            class="w-4 h-4 rounded-full border-2 border-slate-200 peer-checked:border-emerald-500 flex items-center justify-center">
                                        </div>
                                    </div>
                                    <div>
                                        <p class="font-black text-slate-800 text-lg leading-none mb-1">Efectivo</p>
                                        <p class="text-slate-500 text-xs font-medium">Paga al recibir en casa</p>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>

                    <div
                        class="mt-12 flex flex-col sm:flex-row items-center justify-between gap-6 p-8 bg-white border border-slate-100 rounded-[2rem] shadow-xl shadow-slate-200/60">
                        <div class="text-center sm:text-left">
                            <p class="text-amber-600 font-bold text-xs uppercase tracking-widest mb-1">
                                Listo para finalizar
                            </p>
                            <h4 class="text-slate-900 text-xl font-bold">
                                Total a confirmar:
                                <span class="text-amber-600">
                                    {{ $company->currency_code }} {{ number_format($total ?? 0, 2) }}
                                </span>
                            </h4>
                        </div>

                        <div id="paypal-button-container" class="mt-6 hidden animate-fade-up"></div>

                        <button id="btn-pay"
                            class="w-full sm:w-auto bg-amber-600 text-white px-10 py-3 rounded-2xl font-black transition-all hover:scale-105 active:scale-95 shadow-lg shadow-indigo-200 flex items-center justify-center gap-3">
                            PROCESAR PAGO
                            <i class="fa-solid fa-bolt-lightning animate-pulse"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1 sticky top-8">
                <div class="bg-white/90 backdrop-blur-xl rounded-[1.5rem] p-8 border border-white shadow-2xl shadow-slate-200/60 animate-fade-up"
                    style="animation-delay: 0.2s">

                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h3 class="text-2xl font-black text-slate-900 tracking-tight">Tu Orden</h3>
                            <p class="text-slate-400 text-xs font-bold uppercase tracking-tighter">Resumen detallado
                            </p>
                        </div>
                        <span
                            class="bg-amber-600 text-white px-4 py-2 rounded-2xl text-xs font-black shadow-lg shadow-indigo-200">
                            {{ array_sum(array_column($cart, 'quantity')) }} ítems
                        </span>
                    </div>

                    <div class="space-y-5 max-h-[400px] overflow-y-auto pr-3 custom-scrollbar">
                        @foreach ($cart as $id => $item)
                            <div
                                class="flex items-start group bg-slate-50/50 p-3 rounded-3xl border border-transparent hover:border-slate-100 hover:bg-white transition-all">
                                <div class="relative shrink-0">
                                    <img src="{{ asset('storage/' . $item['image']) }}"
                                        class="w-10 h-10 object-cover rounded-2xl shadow-md group-hover:rotate-2 transition-transform duration-500">
                                    <div
                                        class="absolute top-0 left-0 bg-amber-600 text-white text-[8px] font-black w-4 h-4 flex items-center justify-center rounded-xl shadow-lg ring-4 ring-white">
                                        {{ $item['quantity'] }}
                                    </div>
                                </div>

                                <div class="ml-4 flex-1">
                                    <h4
                                        class="text-[10px] font-black text-slate-800 leading-tight group-hover:text-indigo-600 transition-colors">
                                        {{ $item['name'] }}
                                    </h4>

                                    @if (!empty($item['combo_components']) && is_array($item['combo_components']))
                                        <div class="mt-1.5 space-y-1 bg-slate-100/60 p-2 rounded-xl">
                                            <p class="text-[9px] font-bold text-slate-500 uppercase tracking-wider">
                                                Incluye:</p>
                                            <ul class="space-y-0.5">
                                                @foreach ($item['combo_components'] as $component)
                                                    <li class="text-[9px] text-slate-600 flex justify-between">
                                                        <span>• {{ $component['name'] }}</span>
                                                        <span
                                                            class="font-bold text-slate-500">x{{ intval($component['quantity']) }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <p class="text-xs text-slate-400 mt-1 font-bold italic">
                                        {{ $company->currency_code }}{{ number_format($item['price'], 2) }} c/u
                                    </p>
                                </div>

                                <div class="text-sm font-black text-slate-900 ml-2">
                                    {{ $company->currency_code }}{{ number_format($item['quantity'] * $item['price'], 2) }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-8 pt-8 border-t border-slate-100">

                        <div
                            class="relative overflow-hidden bg-gradient-to-br from-white to-slate-50 p-6 rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-200/50 group">
                            <div
                                class="absolute -right-4 -bottom-4 w-24 h-24 bg-indigo-50 rounded-full blur-2xl group-hover:bg-indigo-100 transition-all">
                            </div>

                            <div class="relative z-10 flex justify-between items-center">
                                <div>
                                    <span class="text-amber-600 font-bold text-[10px] uppercase tracking-[0.2em]">
                                        Total Final
                                    </span>
                                    <div class="text-3xl font-black text-slate-900 tracking-tighter mt-1">
                                        <span class="text-amber-600 text-xl font-medium mr-1">
                                            {{ $company->currency_code }}
                                        </span>
                                        {{ number_format($total ?? 0, 2) }}
                                    </div>
                                </div>

                                <div
                                    class="h-12 w-12 bg-indigo-50 backdrop-blur-md rounded-2xl flex items-center justify-center border border-indigo-100">
                                    <i class="fa-solid fa-lock text-amber-600 text-lg"></i>
                                </div>
                            </div>
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
            const paypalContainer = document.getElementById('paypal-button-container');
            const btnPay = document.getElementById('btn-pay');

            document.querySelectorAll('input[name="pay"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'paypal') {
                        if (btnPay) btnPay.classList.add('hidden');
                        if (paypalContainer) {
                            paypalContainer.classList.remove('hidden');
                            initPayPalButtons();
                        }
                    } else {
                        if (btnPay) btnPay.classList.remove('hidden');
                        if (paypalContainer) paypalContainer.classList.add('hidden');
                    }
                });
            });

            @if (!empty($company->extra_config['mercadopago']['key']) && !empty($preferenceId))
                const mp = new MercadoPago("{{ $company->extra_config['mercadopago']['key'] }}", {
                    locale: "{{ $company->extra_config['mercadopago']['locale'] ?? 'es-PE' }}"
                });

                if (btnPay) {
                    btnPay.addEventListener('click', function() {
                        const checkedRadio = document.querySelector('input[name="pay"]:checked');
                        if (!checkedRadio) return;

                        const method = checkedRadio.value;

                        if (method === 'mercadopago') {
                            mp.checkout({
                                preference: {
                                    id: '{{ $preferenceId }}'
                                },
                                autoOpen: true
                            });
                        } else if (method === 'cash') {
                            window.location.href = "{{ route('cash.process') }}";
                        }
                    });
                }
            @else
                if (btnPay) {
                    btnPay.addEventListener('click', function() {
                        const checkedRadio = document.querySelector('input[name="pay"]:checked');
                        if (checkedRadio && checkedRadio.value === 'cash') {
                            window.location.href = "{{ route('cash.process') }}";
                        }
                    });
                }
            @endif

            function initPayPalButtons() {
                if (!paypalContainer || paypalContainer.innerHTML !== '' || typeof paypal === 'undefined') return;

                paypal.Buttons({
                    createOrder: function(data, actions) {
                        return fetch("{{ route('paypal.create') }}", {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                }
                            })
                            .then(response => {
                                if (!response.ok) throw new Error('Error al generar la orden de pago.');
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
        </script>
    @endpush
</x-app-layout>
