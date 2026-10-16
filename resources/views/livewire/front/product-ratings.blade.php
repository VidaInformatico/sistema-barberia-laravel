<div class="max-w-4xl mx-auto px-4 sm:px-6 py-12 sm:py-16 space-y-12 sm:space-y-16">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12">
        <!-- Estadísticas -->
        <div>
            <h3 class="text-lg sm:text-xl font-bold uppercase tracking-widest mb-6 sm:mb-8 text-white">Opiniones del Producto</h3>
            <div class="flex items-end gap-3 sm:gap-4 mb-6 sm:mb-8">
                <span class="text-5xl sm:text-6xl font-black text-amber-500">
                    {{ number_format($product->ratings()->avg('score'), 1) }}
                </span>
                <span class="text-stone-400 mb-1 sm:mb-2 text-xs sm:text-sm font-medium">
                    / 5.0 basada en {{ $total }} reseñas
                </span>
            </div>

            <div class="space-y-3">
                @foreach ([5, 4, 3, 2, 1] as $s)
                    <div class="flex items-center gap-3">
                        <span class="text-xs sm:text-sm w-4 font-bold text-stone-300">{{ $s }}</span>
                        <div class="flex-1 h-1.5 bg-stone-800 rounded-full overflow-hidden">
                            <div class="h-full bg-amber-500 rounded-full transition-all duration-500"
                                style="width: {{ $total > 0 ? ($stats[$s] / $total) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Formulario de Reseña -->
        <div class="bg-stone-900 border border-stone-800 p-6 sm:p-8 rounded-2xl shadow-xl">
            @auth
                <h4 class="font-bold text-sm sm:text-base text-white mb-4">¿Qué tal te pareció este producto?</h4>
                <form wire:submit.prevent="submit" class="space-y-4">
                    <!-- Selector de estrellas interactivo -->
                    <div class="flex gap-2">
                        @foreach ([1, 2, 3, 4, 5] as $i)
                            <button type="button" wire:click="$set('score', {{ $i }})"
                                class="text-2xl sm:text-3xl transition-all {{ $score >= $i ? 'text-amber-500 scale-110' : 'text-stone-700 hover:text-stone-500' }}">
                                <i class="fa-solid fa-star"></i>
                            </button>
                        @endforeach
                    </div>
                    @error('score')
                        <span class="text-red-500 text-xs block">{{ $message }}</span>
                    @enderror

                    <textarea wire:model="comment" rows="3"
                        class="w-full bg-stone-950 border border-stone-800 text-white text-xs sm:text-sm p-4 rounded-xl focus:border-amber-500 outline-none transition placeholder-stone-600"
                        placeholder="Tu opinión ayuda a otros..."></textarea>
                    @error('comment')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror

                    <button type="submit"
                        class="w-full bg-amber-500 text-stone-950 font-black uppercase text-xs sm:text-sm tracking-widest py-3 px-6 rounded-lg hover:bg-amber-400 transition flex items-center justify-center gap-2 shadow-lg shadow-amber-500/10">
                        <i class="fa-solid fa-paper-plane text-xs"></i>
                        <span>Publicar opinión</span>
                    </button>
                </form>
            @else
                <div class="text-center py-6 sm:py-8">
                    <p class="text-stone-400 mb-6 text-xs sm:text-sm italic">Inicia sesión para valorar este producto</p>
                    <a href="{{ route('login') }}"
                        class="inline-flex items-center gap-2 px-6 sm:px-8 py-3 border border-stone-700 hover:bg-stone-800 transition rounded-lg uppercase text-xs font-bold tracking-widest text-white">
                        <i class="fa-solid fa-right-to-bracket text-amber-500"></i>
                        <span>Iniciar Sesión</span>
                    </a>
                </div>
            @endauth
        </div>
    </div>

    <!-- Lista de Comentarios Recientes -->
    <div class="space-y-6">
        <h3 class="text-lg sm:text-xl font-bold uppercase tracking-widest text-white">Comentarios recientes</h3>

        @forelse($ratings as $rating)
            <div class="border-b border-stone-800 pb-6">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2 mb-2">
                    <div class="flex items-center gap-2">
                        <div class="flex text-amber-500 text-xs sm:text-sm">
                            @for ($i = 0; $i < $rating->score; $i++)
                                <i class="fa-solid fa-star"></i>
                            @endfor
                        </div>
                        <span class="text-stone-500 text-xs ml-2">{{ $rating->created_at->format('d M, Y') }}</span>
                    </div>

                    @auth
                        @if ($rating->user_id === auth()->id())
                            <button type="button" onclick="confirmDelete({{ $rating->id }})"
                                class="text-stone-500 hover:text-red-500 text-[10px] uppercase font-bold transition flex items-center gap-1 self-start sm:self-auto">
                                <i class="fa-solid fa-trash-can"></i> Eliminar
                            </button>
                        @endif
                    @endauth
                </div>

                <p class="text-stone-300 text-xs sm:text-sm leading-relaxed mb-2">{{ $rating->comment }}</p>
                <p class="text-stone-500 text-[10px] uppercase font-bold tracking-wider">
                    — {{ $rating->user->name ?? 'Usuario' }}
                </p>
            </div>
        @empty
            <div class="text-center py-12 border border-dashed border-stone-800 rounded-2xl p-6">
                <i class="fa-regular fa-comment-dots text-stone-600 text-3xl mb-3"></i>
                <p class="text-stone-500 text-xs sm:text-sm">Aún no hay opiniones. ¡Sé el primero en valorar este producto!</p>
            </div>
        @endforelse
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
