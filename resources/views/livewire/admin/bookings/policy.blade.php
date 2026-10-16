<div>
    <div class="page-container">
        <header class="page-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div class="flex items-center gap-3">
                <div
                    class="header-icon w-12 h-12 rounded-2xl bg-gradient-to-tr from-indigo-600 to-violet-500 text-white flex items-center justify-center shadow-lg shadow-indigo-500/30">
                    <i class="fas fa-shield-alt text-lg"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black tracking-tight text-slate-800 leading-none">Política de Reserva</h2>
                    <p class="text-slate-400 text-[11px] font-bold uppercase tracking-wider mt-1">
                        Configuración única de tiempos, cancelaciones y penalizaciones de citas
                    </p>
                </div>
            </div>

            @if (!$policy)
                <button wire:click="create"
                    class="btn-primary inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-600 text-white font-bold text-sm shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 hover:-translate-y-0.5 transition-all duration-200">
                    <i class="fas fa-plus"></i>
                    Crear Política
                </button>
            @endif
        </header>

        @if ($policy)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5"
                wire:key="policy-card-{{ $policy->id }}">

                <div
                    class="group relative bg-white rounded-3xl p-6 border border-slate-100 shadow-xl shadow-slate-100/80 hover:shadow-2xl hover:shadow-indigo-500/10 transition-all duration-300 flex flex-col justify-between overflow-hidden">
                    <div
                        class="absolute -right-6 -bottom-6 w-24 h-24 bg-gradient-to-br from-indigo-500/10 to-transparent rounded-full pointer-events-none group-hover:scale-125 transition-transform duration-500">
                    </div>
                    <div>
                        <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-50">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-50 to-indigo-100/60 text-indigo-600 flex items-center justify-center shadow-inner">
                                    <i class="fas fa-calendar-alt text-base"></i>
                                </div>
                                <div>
                                    <span
                                        class="text-[10px] font-black uppercase tracking-wider text-slate-400 block">Configuración</span>
                                    <span class="text-sm font-black text-slate-800">Máx. Anticipación</span>
                                </div>
                            </div>
                        </div>
                        <div class="py-2">
                            <span
                                class="text-3xl font-black bg-gradient-to-r from-indigo-600 to-violet-600 bg-clip-text text-transparent">{{ $policy->max_days_in_advance }}</span>
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider ml-1">días</span>
                        </div>
                    </div>
                </div>

                <div
                    class="group relative bg-white rounded-3xl p-6 border border-slate-100 shadow-xl shadow-slate-100/80 hover:shadow-2xl hover:shadow-blue-500/10 transition-all duration-300 flex flex-col justify-between overflow-hidden">
                    <div
                        class="absolute -right-6 -bottom-6 w-24 h-24 bg-gradient-to-br from-blue-500/10 to-transparent rounded-full pointer-events-none group-hover:scale-125 transition-transform duration-500">
                    </div>
                    <div>
                        <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-50">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-50 to-blue-100/60 text-blue-600 flex items-center justify-center shadow-inner">
                                    <i class="fas fa-clock text-base"></i>
                                </div>
                                <div>
                                    <span
                                        class="text-[10px] font-black uppercase tracking-wider text-slate-400 block">Configuración</span>
                                    <span class="text-sm font-black text-slate-800">Mín. Horas Reserva</span>
                                </div>
                            </div>
                        </div>
                        <div class="py-2">
                            <span
                                class="text-3xl font-black bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent">{{ $policy->min_hours_before_booking }}</span>
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider ml-1">hrs
                                antes</span>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Límite Cancelar -->
                <div
                    class="group relative bg-white rounded-3xl p-6 border border-slate-100 shadow-xl shadow-slate-100/80 hover:shadow-2xl hover:shadow-amber-500/10 transition-all duration-300 flex flex-col justify-between overflow-hidden">
                    <div
                        class="absolute -right-6 -bottom-6 w-24 h-24 bg-gradient-to-br from-amber-500/10 to-transparent rounded-full pointer-events-none group-hover:scale-125 transition-transform duration-500">
                    </div>
                    <div>
                        <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-50">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-50 to-amber-100/60 text-amber-600 flex items-center justify-center shadow-inner">
                                    <i class="fas fa-ban text-base"></i>
                                </div>
                                <div>
                                    <span
                                        class="text-[10px] font-black uppercase tracking-wider text-slate-400 block">Configuración</span>
                                    <span class="text-sm font-black text-slate-800">Límite Cancelar</span>
                                </div>
                            </div>
                        </div>
                        <div class="py-2">
                            <span
                                class="text-3xl font-black bg-gradient-to-r from-amber-600 to-orange-600 bg-clip-text text-transparent">{{ $policy->cancel_before_hours }}</span>
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider ml-1">hrs
                                antes</span>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Tolerancia -->
                <div
                    class="group relative bg-white rounded-3xl p-6 border border-slate-100 shadow-xl shadow-slate-100/80 hover:shadow-2xl hover:shadow-purple-500/10 transition-all duration-300 flex flex-col justify-between overflow-hidden">
                    <div
                        class="absolute -right-6 -bottom-6 w-24 h-24 bg-gradient-to-br from-purple-500/10 to-transparent rounded-full pointer-events-none group-hover:scale-125 transition-transform duration-500">
                    </div>
                    <div>
                        <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-50">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-12 h-12 rounded-2xl bg-gradient-to-br from-purple-50 to-purple-100/60 text-purple-600 flex items-center justify-center shadow-inner">
                                    <i class="fas fa-hourglass-half text-base"></i>
                                </div>
                                <div>
                                    <span
                                        class="text-[10px] font-black uppercase tracking-wider text-slate-400 block">Configuración</span>
                                    <span class="text-sm font-black text-slate-800">Tolerancia</span>
                                </div>
                            </div>
                        </div>
                        <div class="py-2">
                            <span
                                class="text-3xl font-black bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">{{ $policy->grace_period_minutes }}</span>
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider ml-1">min</span>
                        </div>
                    </div>
                </div>

                <!-- Card 5: Canc. Cliente -->
                <div
                    class="group relative bg-white rounded-3xl p-6 border border-slate-100 shadow-xl shadow-slate-100/80 hover:shadow-2xl hover:shadow-emerald-500/10 transition-all duration-300 flex flex-col justify-between overflow-hidden">
                    <div
                        class="absolute -right-6 -bottom-6 w-24 h-24 bg-gradient-to-br from-emerald-500/10 to-transparent rounded-full pointer-events-none group-hover:scale-125 transition-transform duration-500">
                    </div>
                    <div>
                        <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-50">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-50 to-emerald-100/60 text-emerald-600 flex items-center justify-center shadow-inner">
                                    <i class="fas fa-user-shield text-base"></i>
                                </div>
                                <div>
                                    <span
                                        class="text-[10px] font-black uppercase tracking-wider text-slate-400 block">Configuración</span>
                                    <span class="text-sm font-black text-slate-800">Canc. Cliente</span>
                                </div>
                            </div>
                        </div>
                        <div class="py-2">
                            @if ($policy->allow_client_cancellation)
                                <span
                                    class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-black bg-emerald-50 text-emerald-600 rounded-xl border border-emerald-100">
                                    <i class="fas fa-check-circle"></i> Permitido
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-black bg-rose-50 text-rose-600 rounded-xl border border-rose-100">
                                    <i class="fas fa-times-circle"></i> Restringido
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Panel de Acciones Rápidas (Grid de cierre) -->
                <div
                    class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-3xl p-6 shadow-xl flex flex-col justify-between text-white relative overflow-hidden">
                    <div class="absolute right-0 bottom-0 opacity-10 translate-x-4 translate-y-4 pointer-events-none">
                        <i class="fas fa-sliders-h text-8xl"></i>
                    </div>
                    <div>
                        <span
                            class="text-[10px] font-black uppercase tracking-widest text-slate-400 block">Gestión</span>
                        <span class="text-sm font-black text-white">Acciones de Política</span>
                    </div>
                    <div class="flex items-center gap-2 mt-4 pt-4 border-t border-slate-800">
                        <button wire:click="edit({{ $policy->id }})"
                            class="flex-1 px-4 py-2.5 rounded-xl bg-amber-500/20 hover:bg-amber-500 text-amber-400 hover:text-white transition-all text-xs font-bold flex items-center justify-center gap-2 border border-amber-500/30">
                            <i class="fas fa-edit text-sm"></i> Editar
                        </button>
                        <button wire:click="deleteConfirm({{ $policy->id }})"
                            class="flex-1 px-4 py-2.5 rounded-xl bg-rose-500/20 hover:bg-rose-500 text-rose-400 hover:text-white transition-all text-xs font-bold flex items-center justify-center gap-2 border border-rose-500/30">
                            <i class="fas fa-trash-alt text-sm"></i> Eliminar
                        </button>
                    </div>
                </div>

            </div>
        @else
            <div class="bg-white p-16 rounded-3xl border border-slate-100 text-center shadow-xl shadow-slate-100">
                <div
                    class="w-16 h-16 rounded-3xl bg-slate-50 text-slate-300 flex items-center justify-center mx-auto mb-4 text-2xl shadow-inner">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">No hay política configurada</p>
            </div>
        @endif
    </div>

    @if ($isOpen)
        <div class="modal-overlay">
            <div class="modal-backdrop" wire:click="closeModal"></div>

            <div class="modal-container max-w-lg">
                <div class="modal-header">
                    <div class="flex items-center gap-3">
                        <div class="modal-icon-box">
                            <i class="fas {{ $policy_id ? 'fa-edit' : 'fa-plus' }}"></i>
                        </div>
                        <h3 class="modal-title">
                            {{ $policy_id ? 'Editar Política de Reserva' : 'Nueva Política de Reserva' }}
                        </h3>
                    </div>
                    <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        <div>
                            <label class="modal-label">Días Máx. de Anticipación</label>
                            <div class="modal-input-group">
                                <i class="fas fa-calendar-alt modal-input-icon"></i>
                                <input type="number" wire:model="max_days_in_advance" class="modal-input"
                                    placeholder="Ej: 30">
                            </div>
                            @error('max_days_in_advance')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="modal-label">Mín. Horas para Reservar</label>
                            <div class="modal-input-group">
                                <i class="fas fa-clock modal-input-icon"></i>
                                <input type="number" wire:model="min_hours_before_booking" class="modal-input"
                                    placeholder="Ej: 3">
                            </div>
                            @error('min_hours_before_booking')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="modal-label">Límite Cancelación (Horas)</label>
                            <div class="modal-input-group">
                                <i class="fas fa-hourglass-half modal-input-icon"></i>
                                <input type="number" wire:model="cancel_before_hours" class="modal-input"
                                    placeholder="Ej: 24">
                            </div>
                            @error('cancel_before_hours')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="modal-label">Minutos de Tolerancia</label>
                            <div class="modal-input-group">
                                <i class="fas fa-stopwatch modal-input-icon"></i>
                                <input type="number" wire:model="grace_period_minutes" class="modal-input"
                                    placeholder="Ej: 15">
                            </div>
                            @error('grace_period_minutes')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="sm:col-span-2 py-1">
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <div class="relative">
                                    <input type="checkbox" wire:model="allow_client_cancellation"
                                        class="sr-only peer">
                                    <div
                                        class="w-10 h-5 bg-slate-200 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600">
                                    </div>
                                </div>
                                <span
                                    class="text-xs font-bold text-slate-600 uppercase group-hover:text-indigo-600 transition-colors">¿Permitir
                                    cancelación al cliente?</span>
                            </label>
                            @error('allow_client_cancellation')
                                <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button wire:click="closeModal" class="btn-modal-secondary">Cancelar</button>
                    <button wire:click="store" class="btn-modal-primary" wire:loading.attr="disabled">
                        <span class="relative z-10" wire:loading.remove wire:target="store">
                            {{ $policy_id ? 'Actualizar' : 'Guardar' }}
                        </span>
                        <span class="relative z-10" wire:loading wire:target="store">
                            Procesando...
                        </span>
                        <i
                            class="fas fa-check-circle text-indigo-400 group-hover:text-white transition-colors text-xs relative z-10"></i>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
