<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
        <div>
            <h2 class="text-lg font-bold text-gray-900 tracking-tight">Gestión de Reseñas</h2>
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Panel de control de feedback</p>
        </div>
        <select wire:model.live="scoreFilter"
            class="text-sm border-gray-200 rounded-lg focus:ring-amber-500 focus:border-amber-500">
            <option value="">Todas las valoraciones</option>
            <option value="5">5 Estrellas</option>
            <option value="4">4 Estrellas</option>
            <option value="3">3 Estrellas</option>
            <option value="2">2 Estrellas</option>
            <option value="1">1 Estrella</option>
        </select>
    </div>

    <div class="divide-y divide-gray-100">
        @forelse($reviews as $review)
            <div class="p-5 flex items-start gap-4">
                <div
                    class="w-10 h-10 rounded-lg bg-gray-900 flex items-center justify-center text-white font-mono text-sm shadow-inner">
                    {{ strtoupper(substr($review->user->name, 0, 1)) }}
                </div>
                <div class="flex-grow min-w-0">
                    <div class="flex justify-between items-center mb-1">
                        <h4 class="text-sm font-bold text-gray-900 truncate">{{ $review->user->name }}</h4>

                        <!-- Contenedor con la fecha y los botones siempre visibles -->
                        <div class="flex items-center gap-4">
                            <span
                                class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $review->created_at->format('M d, Y') }}</span>

                            <div class="flex gap-3">
                                <button onclick="confirmDelete({{ $review->id }})"
                                    class="text-gray-800 font-bold text-xs">
                                    <i class="fas fa-trash-alt mr-1"></i>Eliminar
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-0.5 mb-2">
                        @for ($i = 1; $i <= 5; $i++)
                            <i
                                class="fa{{ $i <= $review->score ? 's' : 'r' }} fa-star text-[10px] {{ $i <= $review->score ? 'text-amber-500' : 'text-gray-300' }}"></i>
                        @endfor
                    </div>
                    <p class="text-sm text-gray-600 leading-relaxed">{{ $review->comment }}</p>
                </div>
            </div>
        @empty
            <div class="p-12 text-center text-gray-400 text-sm italic">Sin registros encontrados.</div>
        @endforelse
    </div>
    <div class="p-4 bg-gray-50 border-t border-gray-100">
        {{ $reviews->links() }}
    </div>
</div>

<script>
    function confirmDelete(id) {
        Swal.fire({
            title: '¿Confirmar acción?',
            text: "Esta reseña será eliminada permanentemente.",
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Sí, borrar',
            customClass: {
                popup: 'rounded-2xl',
                title: 'font-bold'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                @this.call('delete', id);
            }
        })
    }
</script>
