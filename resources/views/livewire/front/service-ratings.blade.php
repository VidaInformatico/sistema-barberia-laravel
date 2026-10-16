<div class="space-y-12">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 text-white">

        <div class="lg:col-span-1 space-y-6">
            <h3 class="text-2xl font-black uppercase tracking-tight">Valoraciones</h3>
            <div class="bg-white/5 p-8 rounded-3xl border border-white/10 backdrop-blur-md">
                <div class="text-5xl font-black mb-1 text-amber-500">
                    {{ number_format($service->ratings()->avg('score'), 1) }}
                </div>
                <p class="text-sm text-gray-400 mb-6">Promedio basado en {{ $total }} opiniones</p>

                <div class="space-y-3">
                    @foreach ([5, 4, 3, 2, 1] as $s)
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-bold w-4">{{ $s }}</span>
                            <div class="flex-1 h-2 bg-black/40 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-amber-600 to-amber-400 transition-all duration-500"
                                    style="width: {{ $total > 0 ? ($stats[$s] / $total) * 100 : 0 }}%"></div>
                            </div>
                            <span class="w-8 text-xs text-gray-500 text-right">{{ $stats[$s] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="lg:col-span-2">
            @auth
                <div class="bg-gradient-to-br from-white/10 to-white/5 p-8 rounded-3xl border border-white/10">
                    <h3 class="text-xl font-bold mb-6">¿Qué te pareció el servicio?</h3>
                    <form wire:submit.prevent="submit" class="space-y-6">
                        <div class="flex gap-2">
                            @foreach ([1, 2, 3, 4, 5] as $i)
                                <button type="button" wire:click="$set('score', {{ $i }})"
                                    class="text-2xl transition-transform hover:scale-125 {{ $score >= $i ? 'text-amber-400' : 'text-gray-600' }}">
                                    ★
                                </button>
                            @endforeach
                        </div>
                        <textarea wire:model="comment" rows="3"
                            class="w-full bg-black/30 border border-white/10 rounded-2xl p-4 focus:ring-2 focus:ring-amber-500 outline-none transition-all"
                            placeholder="Escribe tu experiencia..."></textarea>
                        <button type="submit"
                            class="px-8 py-2.5 bg-amber-500 hover:bg-amber-400 text-black font-black uppercase tracking-wider rounded-xl transition-all">
                            Publicar Reseña
                        </button>
                    </form>
                </div>
            @else
                <div
                    class="h-full flex flex-col items-center justify-center bg-white/5 rounded-3xl border border-dashed border-white/20 p-12 text-center">
                    <p class="text-gray-400 mb-6">Inicia sesión para compartir tu experiencia con este servicio.</p>
                    <a href="{{ route('login') }}"
                        class="px-8 py-3 bg-white text-black font-bold rounded-xl hover:bg-gray-200 transition-all">
                        Ir a Iniciar Sesión
                    </a>
                </div>
            @endauth
        </div>
    </div>

    <div class="space-y-6">
        <h3 class="text-xl font-bold border-l-4 border-amber-500 pl-4">Comentarios recientes</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse ($ratings as $rating)
                <div class="p-6 bg-white/5 rounded-2xl border border-white/5 hover:border-amber-500/30 transition-all">
                    <div class="flex justify-between items-start mb-4">
                        <div class="text-amber-500">
                            @for ($i = 1; $i <= 5; $i++)
                                <span class="{{ $i <= $rating->score ? 'text-amber-400' : 'text-gray-700' }}">★</span>
                            @endfor
                        </div>
                        <span
                            class="text-[10px] text-gray-500 uppercase">{{ $rating->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-gray-300 italic">"{{ $rating->comment }}"</p>
                    <p class="text-xs text-gray-500 mt-4 font-bold">— {{ $rating->user->name ?? 'Cliente Anónimo' }}
                    </p>
                    @auth
                        @if ($rating->user_id === auth()->id())
                            <button type="button" onclick="confirmDelete({{ $rating->id }})"
                                class="text-stone-600 hover:text-red-500 text-[10px] uppercase font-bold transition">
                                Eliminar
                            </button>
                        @endif
                    @endauth
                </div>
            @empty
                <p class="text-gray-500 italic">Aún no hay reseñas para este servicio.</p>
            @endforelse
        </div>
    </div>
</div>

<script>
    function confirmDelete(id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "No podrás revertir esta acción.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d69e2e',
            cancelButtonColor: '#44403c',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            background: '#1c1917',
            color: '#fff'
        }).then((result) => {
            if (result.isConfirmed) {
                @this.call('deleteRating', id);
            }
        });
    }
</script>
