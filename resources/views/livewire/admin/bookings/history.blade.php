<div class="page-container">
    <header class="page-header">
        <div class="flex items-center gap-3">
            <div class="header-icon">
                <i class="fas fa-history"></i>
            </div>
            <div>
                <h2 class="text-2xl font-black tracking-tight text-slate-800 leading-none">Auditoría de Estados
                </h2>
                <p class="text-slate-400 text-[11px] font-bold uppercase tracking-wider mt-1">
                    Historial y seguimiento de cambios en reservas
                </p>
            </div>
        </div>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 bg-white p-4 rounded-xl border border-slate-200 shadow-sm mb-6">

        <div class="relative w-full">
            <input type="text" wire:model.live.debounce.300ms="search" 
                class="w-full bg-slate-50 border border-slate-200 rounded-lg pl-10 pr-4 py-2.5 text-sm font-bold text-slate-700 placeholder-slate-400 focus:outline-none focus:border-slate-400"
                placeholder="N° Reserva...">
            <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                <i class="fas fa-search text-sm"></i>
            </div>
        </div>
    
        <div class="relative w-full">
            <select wire:model.live="statusFilter" 
                class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm font-bold text-slate-700 focus:outline-none focus:border-slate-400 cursor-pointer">
                <option value="">Cualquier estado</option>
                <option value="pending">Pendiente</option>
                <option value="confirmed">Confirmada</option>
                <option value="in_progress">En progreso</option>
                <option value="completed">Completada</option>
                <option value="cancelled">Cancelada</option>
                <option value="no_show">No asistió</option>
            </select>
        </div>
    
        <div class="relative w-full">
            <input type="date" wire:model.live="startDate" 
                class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm font-bold text-slate-700 focus:outline-none focus:border-slate-400 cursor-pointer">
        </div>
    
        <div class="relative w-full">
            <input type="date" wire:model.live="endDate" 
                class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 text-sm font-bold text-slate-700 focus:outline-none focus:border-slate-400 cursor-pointer">
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden divide-y divide-slate-100">
        @forelse($histories as $log)
            <div class="p-5 flex items-start gap-4 hover:bg-slate-50 transition-colors" wire:key="history-log-{{ $log->id }}">
                <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold text-xs shrink-0">
                    {{ strtoupper(substr($log->changedBy->name ?? 'S', 0, 1)) }}
                </div>

                <div class="flex-grow min-w-0">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h4 class="text-sm font-bold text-slate-800">Reserva
                                #{{ $log->booking->booking_number ?? $log->booking_id }}</h4>
                            <p class="text-[10px] text-slate-400 uppercase font-bold tracking-wide">Por:
                                {{ $log->changedBy->name ?? 'Sistema' }}</p>
                        </div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase">{{ $log->created_at->format('d M, H:i') }}</span>
                    </div>

                    <div class="flex items-center gap-2 mb-3">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-slate-100 text-slate-600 uppercase">{{ $log->previous_status }}</span>
                        <i class="fas fa-arrow-right text-slate-300 text-[10px]"></i>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-600 uppercase">
                            {{ $log->new_status }}
                        </span>
                    </div>

                    @if ($log->comment)
                        <div class="bg-slate-50 border border-slate-100 rounded-lg p-3 text-sm text-slate-600 italic">
                            <span class="block text-[10px] font-bold text-slate-400 uppercase mb-1 not-italic">Comentario:</span>
                            "{{ $log->comment }}"
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="p-12 text-center">
                <i class="fas fa-history text-slate-200 text-3xl mb-3 block"></i>
                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">No hay historial disponible</p>
            </div>
        @endforelse
    </div>

    @if (method_exists($histories, 'links'))
        <div class="mt-4 px-2">
            {{ $histories->links() }}
        </div>
    @endif
</div>