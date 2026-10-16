<div class="space-y-6">

    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <h3 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-4">PROMEDIO GENERAL</h3>
        <div class="flex items-baseline gap-1 mb-6">
            <span class="text-5xl font-black text-gray-900">{{ number_format($average, 1) }}</span>
            <span class="text-lg text-gray-400 font-bold">/ 5.0</span>
        </div>
        <div class="space-y-3">
            @foreach (range(5, 1) as $i)
                <div class="flex items-center gap-3">
                    <span class="text-xs font-bold w-3 text-gray-600">{{ $i }}</span>
                    <div class="flex-grow h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-amber-500 rounded-full"
                            style="width: {{ $totalReviews > 0 ? ($stats[$i] / $totalReviews) * 100 : 0 }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
            <h2 class="text-lg font-bold text-gray-900">Gestión de Reseñas</h2>
            <div class="flex gap-2">
                <select wire:model.live="typeFilter" class="text-sm border-gray-200 rounded-lg">
                    <option value="">Todos los tipos</option>
                    <option value="Barber">Barbero</option>
                    <option value="Service">Servicio</option>
                    <option value="Product">Producto</option>
                </select>
                <select wire:model.live="scoreFilter" class="text-sm border-gray-200 rounded-lg">
                    <option value="">Todas las estrellas</option>
                    <option value="5">5</option>
                    <option value="4">4</option>
                    <option value="3">3</option>
                    <option value="2">2</option>
                    <option value="1">1</option>
                </select>
            </div>
        </div>

        <div class="divide-y divide-gray-100">
            @forelse($reviews as $review)
                <div class="p-5 flex items-start gap-4">
                    <div
                        class="w-10 h-10 rounded-lg bg-gray-900 flex items-center justify-center text-white font-mono text-sm shadow-inner shrink-0">
                        {{ strtoupper(substr($review->user->name, 0, 1)) }}
                    </div>
                    <div class="flex-grow min-w-0">
                        <div class="flex justify-between items-center mb-1">
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 truncate">{{ $review->user->name }}</h4>
                                <p class="text-[10px] text-gray-500 font-bold uppercase">
                                    {{ class_basename($review->rateable_type) }}:
                                    {{ $review->rateable->name ?? ($review->rateable->title ?? 'N/A') }}
                                </p>
                            </div>

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

                        <div class="flex gap-0.5 mb-2">
                            @for ($i = 1; $i <= 5; $i++)
                                <i
                                    class="fa{{ $i <= $review->score ? 's' : 'r' }} fa-star text-[10px] {{ $i <= $review->score ? 'text-amber-500' : 'text-gray-300' }}"></i>
                            @endfor
                        </div>
                        <p class="text-sm text-gray-600">{{ $review->comment }}</p>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center text-gray-400 italic">Sin registros encontrados.</div>
            @endforelse
        </div>

        <div class="p-4 border-t border-gray-100 bg-gray-50">
            {{ $reviews->links() }}
        </div>
    </div>
</div>
