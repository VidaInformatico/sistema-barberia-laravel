<footer class="relative w-full bg-gray-950 text-white pt-24 pb-12 border-t border-white/10">
    <div class="max-w-7xl mx-auto px-6">

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 pb-16 border-b border-white/10">
            <!-- Columna 1: Logo y descripción -->
            <div class="flex flex-col space-y-4">
                <span class="text-2xl font-black text-white tracking-tighter">
                    {{ $company->name }}
                </span>
                <p class="text-gray-400 text-xs font-medium leading-relaxed mb-4">
                    {{ $company->extra_config['message'] ?? 'Inscríbete para recibir invitaciones a eventos privados.' }}
                </p>
                <div class="flex items-center gap-4 text-gray-400 pt-2">
                    @if (!empty($company->social_links) && is_array($company->social_links))
                        @foreach ($company->social_links as $social)
                            @php
                                $url = is_array($social) ? ($social['url'] ?? '') : $social;
                                $name = is_array($social) ? ($social['name'] ?? '') : '';
                                $icon = is_array($social) ? ($social['icon'] ?? '') : '';
                                $color1 = is_array($social) ? ($social['color1'] ?? '') : '';
                            @endphp
                            @if (!empty($url))
                                <a href="{{ $url }}" target="_blank"
                                    class="transition-colors" style="color: {{ $color1 }};" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                                    <i class="{{ !empty($icon) ? $icon : 'fa-brands fa-globe' }} text-sm"></i>
                                </a>
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Columna 2: Horarios -->
            <div>
                <h4 class="text-xs font-bold uppercase tracking-widest text-amber-500 mb-6">Horarios de Atención</h4>
                <ul class="space-y-3 text-xs font-medium text-gray-400">
                    <li class="flex justify-between border-b border-white/5 pb-2">
                        <span>{{ $company->hours }}</span>
                    </li>
                </ul>
            </div>

            <!-- Columna 3: Ubicación Directa -->
            <div>
                <h4 class="text-xs font-bold uppercase tracking-widest text-amber-500 mb-6">Ubicación Central</h4>
                <p class="text-gray-400 text-xs font-medium leading-relaxed mb-4">
                    {{ $company->address }}
                </p>
                <a href="{{ $company->extra_config['map_url'] ?? 'https://maps.google.com' }}" target="_blank"
                    class="inline-flex items-center gap-2 text-[10px] font-bold uppercase tracking-widest text-amber-400 hover:text-white transition-colors">
                    <i class="fa-solid fa-location-dot"></i> Ver en Google Maps
                </a>
            </div>

            <!-- Columna 4: Redes Sociales (Reemplazo de Suscribirse) -->
            <div>
                <h4 class="text-xs font-bold uppercase tracking-widest text-amber-500 mb-6">Síguenos</h4>
                <p class="text-gray-400 text-xs font-medium leading-relaxed mb-4">
                    Mantente al tanto de nuestras últimas tendencias, eventos exclusivos y promociones en redes sociales.
                </p>
                <div class="flex flex-col space-y-3 text-xs font-medium">
                    @if (!empty($company->social_links) && is_array($company->social_links))
                        @foreach ($company->social_links as $social)
                            @php
                                $url = is_array($social) ? ($social['url'] ?? '') : $social;
                                $name = is_array($social) ? ($social['name'] ?? '') : '';
                                $icon = is_array($social) ? ($social['icon'] ?? '') : '';
                                $color1 = is_array($social) ? ($social['color1'] ?? '') : '';
                            @endphp
                            @if (!empty($url))
                                <a href="{{ $url }}" target="_blank"
                                    class="flex items-center gap-3 text-gray-400 transition-colors" onmouseover="this.style.color='{{ $color1 }}'" onmouseout="this.style.color=''">
                                    <i class="{{ !empty($icon) ? $icon : 'fa-brands fa-globe' }} text-base w-5" style="color: {{ $color1 }};"></i> {{ $name }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <!-- Copyright y enlaces legales -->
        <div
            class="pt-8 flex flex-col md:flex-row items-center justify-between text-[10px] font-bold uppercase tracking-widest text-gray-500 gap-4">
            <span>© {{ date('Y') }} {{ $company->name }}. Todos los derechos reservados.</span>
            <div class="flex gap-6">
                <a href="#" class="hover:text-white transition-colors">Privacidad</a>
                <a href="#" class="hover:text-white transition-colors">Términos</a>
                <a href="{{ route('policies') }}" class="hover:text-white transition-colors">Políticas de Reserva</a>
            </div>
        </div>

    </div>
</footer>