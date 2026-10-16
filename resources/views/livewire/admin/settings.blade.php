<div class="page-container">
    <header class="page-header mb-6">
        <div class="flex items-center gap-3">
            <div class="header-icon bg-amber-500 text-stone-950 p-3 rounded-lg">
                <i class="fas fa-cog"></i>
            </div>
            <div>
                <h2 class="text-2xl font-black text-slate-800">Ajustes Globales</h2>
            </div>
        </div>
    </header>

    <div class="space-y-6">
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
            <h3 class="text-sm font-black text-slate-700 uppercase mb-6 flex items-center gap-2">
                <i class="fas fa-building text-amber-500"></i> Información de Empresa
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-2">Logo de la Empresa</label>
                    <div class="relative flex items-center">
                        <i class="fas fa-image absolute left-3 text-slate-400 text-xs"></i>
                        <input type="file" wire:model="logo"
                            class="w-full bg-slate-50 border border-slate-200 rounded-lg py-2 pl-9 pr-3 text-sm text-slate-700 file:mr-4 file:py-1 file:px-2 file:rounded file:border-0 file:bg-slate-200 file:text-xs">
                    </div>
                    @error('logo')
                        <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                    @enderror
                </div>

                @foreach ([
                    'name' => ['Nombre de la Empresa', 'fa-building', 'Barbería Real'],
                    'email' => ['Email de Contacto', 'fa-envelope', 'contacto@ejemplo.com'],
                    'phone' => ['Teléfono', 'fa-phone', '+51 999 999 999'],
                    'address' => ['Dirección', 'fa-map-marker-alt', 'Av. Principal 123'],
                    'currency_code' => ['Moneda', 'fa-money-bill', 'USD'],
                    'timezone' => ['Timezone', 'fa-clock', 'America/Lima'],
                    'hours' => ['Horario', 'fa-calendar', 'Lun - Sáb: 9:00 - 20:00'],
                ] as $field => $data)
                    <div>
                        <label
                            class="block text-[10px] font-black text-slate-400 uppercase mb-2">{{ $data[0] }}</label>
                        <div class="relative flex items-center">
                            <i class="fas {{ $data[1] }} absolute left-3 text-slate-400 text-xs"></i>
                            <input wire:model="{{ $field }}" placeholder="{{ $data[2] }}"
                                class="w-full bg-slate-50 border {{ $errors->has($field) ? 'border-red-500' : 'border-slate-200' }} rounded-lg py-2.5 pl-9 pr-3 text-sm text-slate-700 focus:border-amber-500 outline-none transition">
                        </div>
                        @error($field)
                            <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                        @enderror
                    </div>
                @endforeach

                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-2">URL de Google Maps</label>
                    <div class="relative flex items-center">
                        <i class="fas fa-map-location-dot absolute left-3 text-slate-400 text-xs"></i>
                        <input wire:model="extra_config.map_url" placeholder="https://maps.google.com/..."
                            class="w-full bg-slate-50 border border-slate-200 rounded-lg py-2.5 pl-9 pr-3 text-sm text-slate-700 focus:border-amber-500 outline-none transition">
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-2">Mensaje Personalizado (Footer/Club)</label>
                    <div class="relative flex items-start">
                        <i class="fas fa-comment-dots absolute left-3 top-3 text-slate-400 text-xs"></i>
                        <textarea wire:model="extra_config.message" placeholder="Escribe un mensaje descriptivo..." rows="3"
                            class="w-full bg-slate-50 border border-slate-200 rounded-lg py-2.5 pl-9 pr-3 text-sm text-slate-700 focus:border-amber-500 outline-none transition"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
            <h3 class="text-sm font-black text-slate-700 uppercase mb-6 flex items-center gap-2">
                <i class="fas fa-wallet text-amber-500"></i> Pasarelas de Pago
            </h3>

            <div class="space-y-6">
                <div class="border-b border-slate-100 pb-6">
                    <h4 class="text-xs font-black text-slate-600 uppercase mb-4 flex items-center gap-2">
                        <i class="fas fa-hand-holding-dollar text-blue-500"></i> Mercado Pago
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase mb-2">Mercadopago
                                Key</label>
                            <input wire:model="extra_config.mercadopago.key" placeholder="APP_USR-..."
                                class="w-full bg-slate-50 border border-slate-200 rounded-lg py-2.5 px-3 text-sm text-slate-700 focus:border-amber-500 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase mb-2">Mercadopago
                                Token</label>
                            <input wire:model="extra_config.mercadopago.token" placeholder="APP_USR-..."
                                class="w-full bg-slate-50 border border-slate-200 rounded-lg py-2.5 px-3 text-sm text-slate-700 focus:border-amber-500 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase mb-2">Mercadopago
                                Currency</label>
                            <input wire:model="extra_config.mercadopago.currency" placeholder="PEN"
                                class="w-full bg-slate-50 border border-slate-200 rounded-lg py-2.5 px-3 text-sm text-slate-700 focus:border-amber-500 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase mb-2">Mercadopago
                                Locale</label>
                            <input wire:model="extra_config.mercadopago.locale" placeholder="es-PE"
                                class="w-full bg-slate-50 border border-slate-200 rounded-lg py-2.5 px-3 text-sm text-slate-700 focus:border-amber-500 outline-none transition">
                        </div>
                    </div>
                </div>

                <div>
                    <h4 class="text-xs font-black text-slate-600 uppercase mb-4 flex items-center gap-2">
                        <i class="fab fa-paypal text-sky-600"></i> PayPal
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase mb-2">PayPal
                                Mode</label>
                            <select wire:model="extra_config.paypal.mode"
                                class="w-full bg-slate-50 border border-slate-200 rounded-lg py-2.5 px-3 text-sm text-slate-700 focus:border-amber-500 outline-none transition">
                                <option value="sandbox">Sandbox</option>
                                <option value="live">Live</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase mb-2">PayPal
                                Currency</label>
                            <input wire:model="extra_config.paypal.currency" placeholder="USD"
                                class="w-full bg-slate-50 border border-slate-200 rounded-lg py-2.5 px-3 text-sm text-slate-700 focus:border-amber-500 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase mb-2">PayPal Client
                                ID</label>
                            <input wire:model="extra_config.paypal.client_id" placeholder="AewsWxrC..."
                                class="w-full bg-slate-50 border border-slate-200 rounded-lg py-2.5 px-3 text-sm text-slate-700 focus:border-amber-500 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase mb-2">PayPal
                                Secret</label>
                            <input wire:model="extra_config.paypal.secret" placeholder="EAZOhTz..."
                                class="w-full bg-slate-50 border border-slate-200 rounded-lg py-2.5 px-3 text-sm text-slate-700 focus:border-amber-500 outline-none transition">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-sm font-black text-slate-700 uppercase flex items-center gap-2">
                    <i class="fas fa-share-nodes text-amber-500"></i> Redes Sociales
                </h3>
                <button type="button" wire:click="addSocial"
                    class="text-[10px] font-black bg-slate-800 text-white px-3 py-2 rounded-lg hover:bg-slate-900 uppercase">
                    <i class="fas fa-plus mr-1"></i> Añadir Red
                </button>
            </div>

            <div class="space-y-3">
                @foreach ($socials as $index => $social)
                    <div class="flex flex-col gap-2">
                        <div
                            class="flex flex-wrap gap-3 items-center bg-slate-50 p-3 rounded-lg border border-slate-100">
                            
                            <div class="flex items-center justify-center w-10 h-10 bg-white border border-slate-200 rounded-lg text-slate-700 text-base shadow-sm shrink-0">
                                <i class="{{ !empty($social['icon']) ? $social['icon'] : 'fas fa-globe' }}"></i>
                            </div>

                            <input wire:model="socials.{{ $index }}.name" placeholder="Nombre"
                                class="flex-1 min-w-[120px] bg-white border border-slate-200 rounded p-2 text-xs">
                            <input wire:model="socials.{{ $index }}.url" placeholder="URL"
                                class="flex-[2] min-w-[200px] bg-white border border-slate-200 rounded p-2 text-xs">
                            <input wire:model="socials.{{ $index }}.icon" placeholder="Clase FA (ej: fa-brands fa-instagram)"
                                class="w-44 bg-white border border-slate-200 rounded p-2 text-xs">

                            <div class="flex gap-2 items-center">
                                <input type="color" wire:model="socials.{{ $index }}.color1"
                                    class="w-8 h-8 rounded border border-slate-200 cursor-pointer">
                                <input type="color" wire:model="socials.{{ $index }}.color2"
                                    class="w-8 h-8 rounded border border-slate-200 cursor-pointer">
                            </div>

                            <button type="button" wire:click="removeSocial({{ $index }})"
                                class="text-slate-400 hover:text-red-600 px-2">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <button type="button" wire:click="save"
            class="w-full bg-slate-800 text-white font-black py-3 rounded-lg hover:bg-slate-900 uppercase tracking-widest text-sm flex items-center justify-center gap-2">
            <i class="fas fa-save"></i> Guardar Configuración Global
        </button>
    </div>
</div>