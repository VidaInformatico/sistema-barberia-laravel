<div class="min-h-screen bg-slate-50 text-slate-900">
    <div class="mx-auto">

        @if (session()->has('success'))
            <div
                class="mb-6 p-4 bg-emerald-50 border border-emerald-500 text-emerald-800 rounded-sm text-sm font-bold uppercase tracking-wider">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

            <div class="lg:col-span-2 space-y-6">

                <div class="bg-white border border-slate-200 p-6 rounded-sm shadow-sm">
                    <h2 class="text-xs font-bold uppercase tracking-widest text-amber-600 mb-4 flex items-center gap-2">
                        <span class="w-5 h-5 bg-amber-50 text-amber-600 flex items-center justify-center text-[10px] font-mono border border-amber-200">01</span>
                        Selecciona los Servicios Solicitados
                    </h2>
                
                    @if ($categories->count())
                        <div class="flex items-center gap-2 overflow-x-auto pb-3 mb-5 border-b border-slate-100 scrollbar-none">
                            <button type="button" wire:click="$set('selectedCategory', '')"
                                class="px-4 py-2 text-xs font-bold uppercase tracking-wider rounded-sm border transition-all whitespace-nowrap {{ $selectedCategory == '' ? 'bg-amber-600 border-amber-600 text-white' : 'bg-slate-50 border-slate-200 text-slate-600 hover:border-slate-300' }}">
                                Todos
                            </button>
                
                            @foreach ($categories as $category)
                                <button type="button" wire:click="$set('selectedCategory', {{ $category->id }})"
                                    class="px-4 py-2 text-xs font-bold uppercase tracking-wider rounded-sm border transition-all whitespace-nowrap {{ $selectedCategory == $category->id ? 'bg-amber-600 border-amber-600 text-white' : 'bg-slate-50 border-slate-200 text-slate-600 hover:border-slate-300' }}">
                                    @if ($category->icon)
                                        <i class="{{ $category->icon }} mr-1.5 text-xs"></i>
                                    @endif
                                    {{ $category->name }}
                                </button>
                            @endforeach
                        </div>
                
                        <div class="space-y-4">
                            <div>
                                <input type="text" 
                                    wire:model.live.debounce.300ms="searchService" 
                                    placeholder="Buscar servicio por nombre..." 
                                    class="w-full text-sm border-slate-200 rounded-sm focus:border-amber-500 focus:ring-amber-500 placeholder:text-slate-400 bg-slate-50/50 px-3 py-2">
                            </div>
                        
                            <div class="max-h-[450px] overflow-y-auto space-y-2 pr-1 custom-scrollbar">
                                @forelse($availableServices as $service)
                                    @php
                                        $isChecked = $selectedServiceId == $service->id;
                                    @endphp
                
                                    <label class="group relative border p-3 rounded-sm flex items-center justify-between gap-4 cursor-pointer hover:border-amber-500/50 transition-all {{ $isChecked ? 'border-amber-600 bg-amber-50/60' : 'border-slate-200 bg-slate-50/50' }}">
                                        
                                        <div class="flex items-center gap-3 flex-1 min-w-0">
                                            <input type="radio" wire:model.live="selectedServiceId"
                                                value="{{ $service->id }}"
                                                class="rounded-full border-slate-300 text-amber-600 focus:ring-amber-500 shrink-0">
                
                                            <div class="flex flex-col min-w-0">
                                                <span class="text-sm font-bold uppercase tracking-tight text-slate-800 group-hover:text-amber-700 transition-colors truncate">
                                                    {{ $service->name }}
                                                </span>
                                                <div class="flex items-center gap-3 mt-0.5">
                                                    <span class="text-[10px] text-amber-700 font-bold uppercase tracking-wider">
                                                        ⏱️ {{ $service->duration_minutes }} min
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                
                                        <div class="text-right whitespace-nowrap shrink-0">
                                            <span class="font-black text-sm text-slate-900 block">
                                                {{ number_format($service->price, 2) }}
                                            </span>
                                        </div>
                                    </label>
                                @empty
                                    <div class="text-center py-8 text-slate-400 text-xs uppercase font-medium tracking-wider bg-slate-50/50 border border-dashed border-slate-200 rounded-sm">
                                        No se encontraron servicios disponibles.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @endif
                
                    @error('selectedServiceId')
                        <span class="text-xs text-red-600 mt-2 block font-semibold uppercase">
                            {{ $message }}
                        </span>
                    @enderror
                </div>

                <div class="bg-white border border-slate-200 p-6 rounded-sm shadow-sm">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-4">
                        <h2 class="text-xs font-bold uppercase tracking-widest text-amber-600 flex items-center gap-2">
                            <span
                                class="w-5 h-5 bg-amber-50 text-amber-600 flex items-center justify-center text-[10px] font-mono border border-amber-200">02</span>
                            Selecciona Fecha & Hora
                        </h2>
                        <div
                            class="flex items-center gap-4 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            <div class="flex items-center gap-1.5"><span
                                    class="w-2.5 h-2.5 bg-amber-600 rounded-sm"></span> Seleccionado</div>
                            <div class="flex items-center gap-1.5"><span
                                    class="w-2.5 h-2.5 bg-red-50 border border-red-200 rounded-sm"></span>
                                Ocupado/Inactivo</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                                <span
                                    class="text-sm font-black uppercase tracking-wider text-slate-800">{{ $monthName }}</span>
                                <div class="flex gap-1">
                                    <button type="button" wire:click="changeMonth('prev')"
                                        class="p-1.5 border border-slate-200 text-xs hover:bg-slate-50 text-slate-700 rounded-sm transition-colors">←</button>
                                    <button type="button" wire:click="changeMonth('next')"
                                        class="p-1.5 border border-slate-200 text-xs hover:bg-slate-50 text-slate-700 rounded-sm transition-colors">→</button>
                                </div>
                            </div>

                            <div
                                class="grid grid-cols-7 gap-1 text-center text-[10px] font-black tracking-widest text-slate-400 uppercase mb-2">
                                <span>Lu</span><span>Ma</span><span>Mi</span><span>Ju</span><span>Vi</span><span>Sá</span><span>Do</span>
                            </div>

                            <div class="grid grid-cols-7 gap-1.5 text-center text-xs font-bold">
                                @foreach ($this->calendarDays as $dayData)
                                    @if ($dayData['blank'])
                                        <span class="p-2.5 text-transparent pointer-events-none"></span>
                                    @else
                                        @php
                                            $isSelected = $selectedDate === $dayData['dateString'];
                                            $isDisabled = $dayData['isPast'];
                                        @endphp

                                        <button type="button"
                                            @if (!$isDisabled && !$dayData['isBusy']) wire:click="selectDate({{ $dayData['day'] }})" @endif
                                            class="p-2.5 rounded-sm transition-colors font-bold text-center text-xs
                                                {{ $isDisabled ? 'text-slate-300 opacity-50 cursor-not-allowed' : '' }}
                                                {{ $dayData['isBusy'] && !$isDisabled ? 'bg-red-50 border border-red-200 text-red-600 cursor-not-allowed' : '' }}
                                                {{ !$isDisabled && !$dayData['isBusy'] && !$isSelected ? 'border border-slate-100 bg-slate-50/50 hover:border-amber-500 hover:text-amber-600 text-slate-800' : '' }}
                                                {{ $isSelected ? 'bg-amber-600 text-white font-black' : '' }}
                                            "
                                            @if ($isDisabled || $dayData['isBusy']) disabled @endif>
                                            {{ $dayData['day'] }}
                                        </button>
                                    @endif
                                @endforeach
                            </div>
                            @error('selectedDate')
                                <span
                                    class="text-xs text-red-600 mt-2 block font-semibold uppercase">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="flex flex-col justify-between">
                            <div>
                                <div class="border-b border-slate-100 pb-3 mb-4">
                                    <span class="text-sm font-black uppercase tracking-wider text-slate-400">
                                        Horas Disponibles
                                    </span>
                                </div>
                                <div class="grid grid-cols-2 gap-2 text-xs font-bold">
                                    @forelse($this->availableHours as $hour)
                                        @if ($hour['available'])
                                            <label
                                                class="border p-3 text-center rounded-sm cursor-pointer transition-all flex items-center justify-center 
                                                {{ $selectedTime === $hour['time']
                                                    ? 'bg-amber-600 text-white border-amber-600'
                                                    : 'bg-slate-50/50 border-slate-200 text-slate-800 hover:border-amber-500' }}">

                                                <input type="radio" wire:model.live="selectedTime"
                                                    value="{{ $hour['time'] }}" class="hidden">
                                                {{ $hour['formatted'] }}
                                            </label>
                                        @else
                                            <button type="button"
                                                class="border border-red-100 bg-red-50 text-red-400 p-3 text-center rounded-sm cursor-not-allowed line-through font-medium"
                                                disabled>
                                                {{ $hour['formatted'] }}
                                            </button>
                                        @endif
                                    @empty
                                        <div class="col-span-2 text-slate-400 text-center py-4">
                                            Selecciona un día habilitado y asegúrate de elegir al menos un servicio.
                                        </div>
                                    @endforelse
                                </div>
                                @error('selectedTime')
                                    <span
                                        class="text-xs text-red-600 mt-2 block font-semibold uppercase">{{ $message }}</span>
                                @enderror
                            </div>
                            <p class="text-[10px] text-slate-400 font-medium mt-4 lg:mt-0">
                                * La tolerancia de espera en la sucursal es de un máximo de 10 minutos.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:sticky lg:top-0 space-y-6">

                <div class="bg-white border border-slate-200 p-6 rounded-sm shadow-sm" x-data="{ open: false, selectedName: '' }">
                    <h2 class="text-xs font-bold uppercase tracking-widest text-amber-600 mb-4 flex items-center gap-2">
                        <span class="w-5 h-5 bg-amber-50 text-amber-600 flex items-center justify-center text-[10px] font-mono border border-amber-200">03</span>
                        Asignar Cliente
                    </h2>
                
                    <div class="relative w-full" @click.away="open = false">
                        
                        <input 
                            type="text" 
                            placeholder="Buscar cliente..."
                            @focus="open = true"
                            wire:model.live.debounce.300ms="searchClient"
                            x-model="selectedName"
                            class="w-full bg-slate-50 border-slate-200 text-sm focus:border-amber-500 focus:ring-0 rounded-sm py-2.5 px-3"
                        >
                
                        <div x-show="open" 
                             x-transition 
                             class="absolute z-50 w-full mt-1 bg-white border border-slate-200 rounded-sm shadow-xl max-h-48 overflow-y-auto">
                            
                            @forelse ($clients as $client)
                                <button type="button"
                                    @click="selectedName = '{{ $client->name }} {{ $client->last_name }}'; $wire.set('clientId', {{ $client->id }}); open = false"
                                    class="w-full text-left px-4 py-2 text-sm text-slate-700 hover:bg-amber-50 hover:text-amber-700 transition-colors border-b border-slate-50 last:border-0">
                                    <span class="block font-medium">{{ $client->name }} {{ $client->last_name }}</span>
                                    <span class="text-[10px] text-slate-400">{{ $client->phone ?? 'Sin teléfono' }}</span>
                                </button>
                            @empty
                                <div class="px-4 py-3 text-xs text-slate-400 italic">No se encontraron resultados</div>
                            @endforelse
                        </div>
                    </div>
                
                    @error('clientId')
                        <span class="text-xs text-red-600 mt-2 block font-semibold uppercase tracking-wider">{{ $message }}</span>
                    @enderror
                </div>

                <div class="bg-white border border-amber-200 p-6 rounded-sm shadow-sm">
                    <h2
                        class="text-xs font-bold uppercase tracking-widest text-amber-600 mb-6 border-b border-slate-100 pb-3">
                        Resumen de la Orden
                    </h2>

                    <div class="space-y-4 text-sm font-medium">
                        <div class="space-y-2 max-h-40 overflow-y-auto border-b border-slate-100 pb-4">
                            <div class="space-y-2 max-h-40 overflow-y-auto border-b border-slate-100 pb-4">
                                @php $service = $this->getSelectedServiceProperty(); @endphp
                                @if ($service)
                                    <div class="flex justify-between items-center text-xs">
                                        <div class="flex flex-col">
                                            <span
                                                class="text-slate-800 uppercase font-bold">{{ $service->name }}</span>
                                            <span class="text-[10px] text-slate-400">Duración:
                                                {{ $service->duration_minutes }} min</span>
                                        </div>
                                        <span class="font-black text-amber-600">
                                            {{ number_format($service->price, 2) }}</span>
                                    </div>
                                @else
                                    <span
                                        class="text-xs text-red-500 font-bold uppercase tracking-wide block text-center py-2">
                                        [ Elige un servicio ]
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="space-y-2 text-xs uppercase tracking-wider text-slate-500">
                            <div class="flex items-center justify-between">
                                <span>Tiempo Total Est.:</span>
                                <span class="text-slate-800 font-bold">{{ $this->totalDurationFormatted }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>Fecha:</span>
                                <span class="text-slate-800 font-bold">
                                    {{ $selectedDate ? \Carbon\Carbon::parse($selectedDate)->translatedFormat('d \d\e F, Y') : '-' }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>Horario:</span>
                                <span class="text-amber-600 font-bold">
                                    {{ $selectedTime ? \Carbon\Carbon::parse($selectedTime)->format('h:i A') : '-' }}
                                </span>
                            </div>
                        </div>

                        <div class="border-t border-slate-200 pt-4 flex justify-between items-baseline mb-6">
                            <span class="text-xs uppercase tracking-widest font-bold text-slate-600">Total Neto:</span>
                            <span class="text-2xl font-black text-amber-600 tracking-tight">
                                {{ number_format($this->totalPriceSum, 2) }}
                            </span>
                        </div>

                        <button type="button" wire:click="storeBooking" wire:loading.attr="disabled"
                            class="w-full bg-amber-600 hover:bg-amber-700 disabled:bg-slate-300 text-white font-black uppercase tracking-widest text-xs py-4 rounded-sm transition-all duration-300 shadow-md active:scale-[0.98] flex items-center justify-center gap-2">
                            <span wire:loading.remove>Confirmar Reserva</span>
                            <span wire:loading>Procesando...</span>
                        </button>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
