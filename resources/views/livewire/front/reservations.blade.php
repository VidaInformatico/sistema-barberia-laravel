<div class="mx-auto">
    @if (session()->has('success'))
        <div
            class="mb-6 p-4 bg-emerald-900/30 border border-emerald-700 text-emerald-400 rounded-sm text-sm font-bold uppercase tracking-wider">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

        <div class="lg:col-span-2 space-y-6">

            <div class="bg-slate-800 border border-slate-700 p-6 rounded-sm shadow-sm">
                <h2 class="text-xs font-bold uppercase tracking-widest text-amber-500 mb-4 flex items-center gap-2">
                    <span
                        class="w-5 h-5 bg-amber-900/30 text-amber-500 flex items-center justify-center text-[10px] font-mono border border-amber-700">02</span>
                    Selecciona tu Barber
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    @forelse ($barbers as $barber)
                        <label
                            class="group relative border p-4 rounded-sm flex items-center gap-3 cursor-pointer hover:border-amber-500/50 transition-all {{ $barberId == $barber->id ? 'border-amber-600 bg-amber-900/20' : 'border-slate-700 bg-slate-900/50' }}">

                            <input type="radio" wire:model.live="barberId" value="{{ $barber->id }}" class="hidden">

                            @if ($barber->user->avatar)
                                <div class="w-10 h-10 bg-slate-700 rounded-sm bg-cover bg-center shrink-0"
                                    style="background-image:url('{{ Storage::url($barber->user->avatar) }}');">
                                </div>
                            @else
                                <div
                                    class="w-10 h-10 rounded-sm flex items-center justify-center font-bold text-sm uppercase shrink-0 transition-colors {{ $barberId == $barber->id ? 'bg-amber-700 text-white' : 'bg-slate-700 text-slate-300' }}">
                                    {{ collect(explode(' ', $barber->user->name))->map(fn($name) => Str::substr($name, 0, 1))->take(2)->implode('') }}
                                </div>
                            @endif

                            <div class="flex flex-col">
                                <span
                                    class="text-sm font-bold uppercase tracking-tight group-hover:text-amber-400 {{ $barberId == $barber->id ? 'text-amber-500' : 'text-slate-200' }}">
                                    {{ $barber->user->name }}
                                </span>

                                <span class="text-[10px] text-slate-500 font-medium">
                                    Especialista
                                </span>
                            </div>

                            <div
                                class="absolute top-2 right-2 w-2 h-2 rounded-full bg-amber-500 {{ $barberId == $barber->id ? 'opacity-100' : 'opacity-0' }}">
                            </div>
                        </label>
                    @empty
                        <div
                            class="col-span-1 sm:col-span-3 p-8 text-center border border-dashed border-slate-700 rounded-sm text-slate-500">
                            <p>No hay barberos disponibles en este momento.</p>
                        </div>
                    @endforelse
                </div>

                @error('barberId')
                    <span class="text-xs text-red-400 mt-2 block font-semibold uppercase">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            <div class="bg-slate-800 border border-slate-700 p-6 rounded-sm shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-4">
                    <h2 class="text-xs font-bold uppercase tracking-widest text-amber-500 flex items-center gap-2">
                        <span
                            class="w-5 h-5 bg-amber-900/30 text-amber-500 flex items-center justify-center text-[10px] font-mono border border-amber-700">03</span>
                        Selecciona Fecha & Hora
                    </h2>
                    <div class="flex items-center gap-4 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                        <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 bg-amber-600 rounded-sm"></span>
                            Seleccionado
                        </div>
                        <div class="flex items-center gap-1.5"><span
                                class="w-2.5 h-2.5 bg-red-900/30 border border-red-800 rounded-sm"></span>
                            Ocupado/Inactivo
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <div class="flex items-center justify-between border-b border-slate-700 pb-3 mb-4">
                            <span
                                class="text-sm font-black uppercase tracking-wider text-slate-100">{{ $monthName }}</span>
                            <div class="flex gap-1">
                                <button type="button" wire:click="changeMonth('prev')"
                                    class="p-1.5 border border-slate-700 text-xs hover:bg-slate-700 text-slate-300 rounded-sm transition-colors">←</button>
                                <button type="button" wire:click="changeMonth('next')"
                                    class="p-1.5 border border-slate-700 text-xs hover:bg-slate-700 text-slate-300 rounded-sm transition-colors">→</button>
                            </div>
                        </div>

                        <div
                            class="grid grid-cols-7 gap-1 text-center text-[10px] font-black tracking-widest text-slate-500 uppercase mb-2">
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
                                              {{ $isDisabled ? 'text-slate-600 opacity-50 cursor-not-allowed' : '' }}
                                              {{ $dayData['isBusy'] && !$isDisabled ? 'bg-red-900/20 border border-red-800 text-red-500 cursor-not-allowed' : '' }}
                                              {{ !$isDisabled && !$dayData['isBusy'] && !$isSelected ? 'border border-slate-700 bg-slate-900/50 hover:border-amber-500 hover:text-amber-500 text-slate-300' : '' }}
                                              {{ $isSelected ? 'bg-amber-600 text-white font-black' : '' }}"
                                        @if ($isDisabled || $dayData['isBusy']) disabled @endif>
                                        {{ $dayData['day'] }}
                                    </button>
                                @endif
                            @endforeach
                        </div>
                        @error('selectedDate')
                            <span
                                class="text-xs text-red-400 mt-2 block font-semibold uppercase">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex flex-col justify-between">
                        <div>
                            <div class="border-b border-slate-700 pb-3 mb-4">
                                <span class="text-sm font-black uppercase tracking-wider text-slate-500">
                                    Horas Disponibles
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-xs font-bold">
                                @forelse($this->availableHours as $hour)
                                    @if ($hour['available'])
                                        <label
                                            class="border p-3 text-center rounded-sm cursor-pointer transition-all flex items-center justify-center 
                                            {{ $selectedTime === $hour['time'] ? 'bg-amber-600 text-white border-amber-600' : 'bg-slate-900/50 border-slate-700 text-slate-300 hover:border-amber-500' }}">
                                            <input type="radio" wire:model.live="selectedTime"
                                                value="{{ $hour['time'] }}" class="hidden">
                                            {{ $hour['formatted'] }}
                                        </label>
                                    @else
                                        <button type="button"
                                            class="border border-red-900/30 bg-red-900/10 text-red-700 p-3 text-center rounded-sm cursor-not-allowed line-through font-medium"
                                            disabled>
                                            {{ $hour['formatted'] }}
                                        </button>
                                    @endif
                                @empty
                                    <div class="col-span-2 text-slate-500 text-center py-4">
                                        Selecciona un día habilitado y asegúrate de elegir al menos un servicio.
                                    </div>
                                @endforelse
                            </div>
                            @error('selectedTime')
                                <span
                                    class="text-xs text-red-400 mt-2 block font-semibold uppercase">{{ $message }}</span>
                            @enderror
                        </div>
                        <p class="text-[10px] text-slate-500 font-medium mt-4 lg:mt-0">
                            * La tolerancia de espera en la sucursal es de un máximo de 10 minutos.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:sticky lg:top-6 space-y-6">
            <div class="bg-slate-800 border border-amber-900/50 p-6 rounded-sm shadow-sm">
                <h2
                    class="text-xs font-bold uppercase tracking-widest text-amber-500 mb-6 border-b border-slate-700 pb-3">
                    Resumen de la Orden
                </h2>

                <div class="space-y-4 text-sm font-medium">
                    <div class="space-y-2 max-h-40 overflow-y-auto border-b border-slate-700 pb-4">
                        @php $service = $this->getSelectedServiceProperty(); @endphp
                        @if ($service)
                            <div class="flex justify-between items-center text-xs">
                                <div class="flex flex-col">
                                    <span class="text-slate-200 uppercase font-bold">{{ $service->name }}</span>
                                    <span class="text-[10px] text-slate-500">Duración: {{ $service->duration_minutes }}
                                        min</span>
                                </div>
                                <span class="font-black text-amber-500">{{ $company->currency_code }}
                                    {{ number_format($service->price, 2) }}</span>
                            </div>
                        @else
                            <span class="text-xs text-red-400 font-bold uppercase tracking-wide block text-center py-2">
                                [ Elige al menos un servicio ]
                            </span>
                        @endif
                    </div>

                    <div class="space-y-2 text-xs uppercase tracking-wider text-slate-500">
                        <div class="flex items-center justify-between">
                            <span>Tiempo Total Est.:</span>
                            <span class="text-slate-300 font-bold">{{ $this->totalDurationFormatted }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Especialista:</span>
                            <span class="text-slate-300 font-bold">
                                {{ $barberId ? $barbers->find($barberId)?->user?->name : '-' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Fecha:</span>
                            <span class="text-slate-300 font-bold">
                                {{ $selectedDate ? \Carbon\Carbon::parse($selectedDate)->translatedFormat('d \d\e F, Y') : '-' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Horario:</span>
                            <span class="text-amber-500 font-bold">
                                {{ $selectedTime ? \Carbon\Carbon::parse($selectedTime)->format('h:i A') : '-' }}
                            </span>
                        </div>
                    </div>

                    <div class="border-t border-slate-700 pt-4 flex justify-between items-baseline mb-6">
                        <span class="text-xs uppercase tracking-widest font-bold text-slate-400">Total Neto:</span>
                        <span class="text-2xl font-black text-amber-500 tracking-tight">
                            {{ $company->currency_code }} {{ number_format($this->totalPriceSum, 2) }}
                        </span>
                    </div>

                    <button type="button" wire:click="storeBooking" wire:loading.attr="disabled"
                        class="w-full bg-amber-600 hover:bg-amber-700 disabled:bg-slate-700 text-white font-black uppercase tracking-widest text-xs py-4 rounded-sm transition-all duration-300 shadow-md active:scale-[0.98] flex items-center justify-center gap-2">
                        <span wire:loading.remove>Confirmar Reserva</span>
                        <span wire:loading>Procesando...</span>
                    </button>

                </div>
            </div>
        </div>
    </div>
</div>
