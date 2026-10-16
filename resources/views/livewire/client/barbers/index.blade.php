<div>
    <div class="page-container">
        <header class="page-header">
            <div class="flex items-center gap-3">
                <div class="header-icon">
                    <i class="fas fa-id-card"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black tracking-tight text-slate-800 leading-none">Barberos</h2>
                    <p class="text-slate-400 text-[11px] font-bold uppercase tracking-wider mt-1">
                        Gestión de perfiles profesionales y especialidades de los barberos
                    </p>
                </div>
            </div>
        </header>

        <div class="toolbar-container">
            <div class="search-input-group relative w-full lg:flex-1">
                <input type="text" wire:model.live.debounce.500ms="search" class="search-input"
                    placeholder="Buscar por nombre o especialidad...">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                    <i class="fas fa-search text-sm"></i>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-between w-full lg:w-auto gap-3">
                <div class="per-page-select flex items-center bg-slate-50 px-3 py-2 rounded-lg border border-slate-100">
                    <label
                        class="text-[10px] font-black text-slate-400 uppercase border-r border-slate-200 pr-3 mr-3 leading-none">
                        Mostrar
                    </label>
                    <select wire:model.live="perPage"
                        class="bg-transparent border-none p-0 pr-8 text-sm font-bold text-slate-700 focus:ring-0 cursor-pointer">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Vista Móvil (Cards) -->
        <div class="block md:hidden space-y-3">
            @forelse ($barbers as $item)
                <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm space-y-3"
                    wire:key="barber-card-{{ $item->id }}">
                    <div class="flex items-start justify-between border-b border-slate-100 pb-2.5">
                        <div class="flex items-center gap-3">
                            <img src="{{ $item->user->avatar_url }}" alt="{{ $item->full_name }}"
                                class="w-10 h-10 rounded-full object-cover border border-slate-200 shrink-0">
                            <div class="flex flex-col min-w-0">
                                <span class="text-sm font-black text-slate-800 truncate">{{ $item->full_name }}</span>
                                <span
                                    class="text-[11px] text-slate-400 font-medium truncate">{{ $item->user->email }}</span>
                            </div>
                        </div>

                        <div class="shrink-0">
                            @if ($item->is_available)
                                <span
                                    class="px-2.5 py-0.5 text-[10px] font-black bg-emerald-100 text-emerald-600 rounded-full uppercase">
                                    Disponible
                                </span>
                            @else
                                <span
                                    class="px-2.5 py-0.5 text-[10px] font-black bg-rose-100 text-rose-600 rounded-full uppercase">
                                    No Disponible
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Badge de Sucursal -->
                    <div>
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-800 border border-slate-200/80">
                            <i class="fa-solid fa-shop mr-1.5 text-amber-500 text-[10px]"></i>
                            {{ $item->user->branch->name ?? 'Sin sucursal' }}
                        </span>
                    </div>

                    <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-100 space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-1.5 text-slate-600 font-medium">
                                <i class="fas fa-cut text-slate-400 text-[11px] shrink-0"></i>
                                <span>
                                    <strong class="text-slate-700">Especialidad:</strong>
                                    {{ $item->specialty ?? 'General' }}
                                </span>
                            </div>

                            <span
                                class="text-[11px] font-bold text-slate-500 bg-white px-2 py-0.5 rounded border border-slate-200 shrink-0">
                                {{ $item->experience_years }} {{ $item->experience_years == 1 ? 'año' : 'años' }} exp.
                            </span>
                        </div>

                        @if ($item->instagram)
                            <div class="flex items-center pt-1 border-t border-slate-200/60">
                                <a href="https://instagram.com/{{ ltrim($item->instagram, '@') }}" target="_blank"
                                    class="text-xs font-bold text-indigo-600 hover:text-indigo-700 flex items-center gap-1.5 transition-colors">
                                    <i class="fab fa-instagram text-sm"></i>
                                    <span>{{ $item->instagram }}</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white p-8 rounded-xl border border-slate-200 text-center">
                    <i class="fas fa-id-card text-slate-200 text-3xl mb-3 block"></i>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">
                        No hay perfiles de barberos configurados
                    </p>
                </div>
            @endforelse
        </div>

        <!-- Vista Escritorio (Tabla) -->
        <div class="datatable-container hidden md:block overflow-x-auto">
            <table class="datatable w-full">
                <thead class="datatable-thead">
                    <tr>
                        <th class="datatable-th">Barbero</th>
                        <th class="datatable-th">Sucursal</th>
                        <th class="datatable-th">Especialidad</th>
                        <th class="datatable-th">Experiencia</th>
                        <th class="datatable-th">Instagram</th>
                        <th class="datatable-th">Disponibilidad</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-50">
                    @forelse ($barbers as $item)
                        <tr class="datatable-tr" wire:key="barber-row-{{ $item->id }}">
                            <td class="datatable-td">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $item->user->avatar_url }}" alt="{{ $item->full_name }}"
                                        class="w-9 h-9 rounded-full object-cover border border-slate-200 shrink-0">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-slate-700">{{ $item->full_name }}</span>
                                        <span class="text-xs text-slate-400">{{ $item->user->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="datatable-td">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-800 border border-slate-200/80">
                                    <i class="fa-solid fa-shop mr-1.5 text-amber-500 text-[10px]"></i>
                                    {{ $item->user->branch->name ?? 'Sin sucursal' }}
                                </span>
                            </td>
                            <td class="datatable-td">
                                <span
                                    class="text-xs font-bold text-slate-600">{{ $item->specialty ?? 'General' }}</span>
                            </td>
                            <td class="datatable-td">
                                <span class="text-xs font-bold text-slate-600">{{ $item->experience_years }}
                                    años</span>
                            </td>
                            <td class="datatable-td">
                                @if ($item->instagram)
                                    <a href="https://instagram.com/{{ ltrim($item->instagram, '@') }}" target="_blank"
                                        class="text-xs font-bold text-indigo-600 hover:underline inline-flex items-center gap-1">
                                        <i class="fab fa-instagram"></i>{{ $item->instagram }}
                                    </a>
                                @else
                                    <span class="text-xs text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="datatable-td">
                                @if ($item->is_available)
                                    <span
                                        class="px-2.5 py-1 text-[10px] font-black bg-emerald-100 text-emerald-600 rounded-full uppercase">
                                        Disponible
                                    </span>
                                @else
                                    <span
                                        class="px-2.5 py-1 text-[10px] font-black bg-rose-100 text-rose-600 rounded-full uppercase">
                                        No Disponible
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-16 text-center">
                                <i class="fas fa-id-card text-slate-200 text-3xl mb-3 block"></i>
                                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">
                                    No hay perfiles de barberos configurados
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if (method_exists($barbers, 'links'))
            <div class="mt-4 px-2">
                {{ $barbers->links() }}
            </div>
        @endif
    </div>
</div>
