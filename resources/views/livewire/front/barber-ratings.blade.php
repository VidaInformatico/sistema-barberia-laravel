<div class="max-w-4xl mx-auto py-16 space-y-16">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
        <div>
            <h3 class="text-xl font-bold uppercase tracking-widest mb-8">Opiniones del Barbero</h3>
            <div class="flex items-end gap-4 mb-8">
                <span class="text-6xl font-black text-amber-500">{{ number_format($this->barber->ratings()->avg('score'), 1) }}</span>
                <span class="text-stone-400 mb-2 font-medium">/ 5.0 basada en {{ $total }} reseñas</span>
            </div>

            <div class="space-y-3">
                @foreach ([5, 4, 3, 2, 1] as $s)
                    <div class="flex items-center gap-3">
                        <span class="text-sm w-4 font-bold text-stone-300">{{ $s }}</span>
                        <div class="flex-1 h-1.5 bg-stone-800 rounded-full overflow-hidden">
                            <div class="h-full bg-amber-500 rounded-full"
                                style="width: {{ $total > 0 ? ($stats[$s] / $total) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-stone-900 border border-stone-800 p-8 rounded-2xl">
            @auth
                <h4 class="font-bold mb-4">¿Qué tal te pareció este servicio?</h4>
                <form wire:submit.prevent="submit" class="space-y-4">
                    <div class="flex gap-2">
                        @foreach ([1, 2, 3, 4, 5] as $i)
                            <button type="button" wire:click="$set('score', {{ $i }})"
                                class="text-2xl transition-all {{ $score >= $i ? 'text-amber-500' : 'text-stone-700' }}">★</button>
                        @endforeach
                    </div>
                    @error('score') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror

                    <textarea wire:model="comment" rows="3"
                        class="w-full bg-stone-950 border border-stone-800 p-4 rounded-xl focus:border-amber-500 outline-none transition"
                        placeholder="Comparte tu experiencia con este barbero..."></textarea>
                    @error('comment') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

                    <button type="submit"
                        class="w-full bg-amber-500 text-stone-950 font-black uppercase py-3 rounded-lg hover:bg-amber-400 transition">Publicar</button>
                </form>
            @else
                <div class="text-center py-8">
                    <p class="text-stone-400 mb-6 italic">Inicia sesión para valorar a este barbero</p>
                    <a href="{{ route('login') }}"
                        class="inline-block px-8 py-3 border border-stone-700 hover:bg-stone-800 transition rounded-lg uppercase text-xs font-bold tracking-widest">Iniciar Sesión</a>
                </div>
            @endauth
        </div>
    </div>

    <div class="space-y-6">
        <h3 class="text-xl font-bold uppercase tracking-widest">Comentarios recientes</h3>

        @forelse($ratings as $rating)
            <div class="border-b border-stone-800 pb-6 animate-in fade-in duration-500">
                <div class="flex justify-between items-start">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="flex">
                            @for ($i = 0; $i < $rating->score; $i++)
                                <span class="text-amber-500 text-sm">★</span>
                            @endfor
                        </div>
                        <span class="text-stone-500 text-xs ml-2">{{ $rating->created_at->format('d M, Y') }}</span>
                    </div>

                    @auth
                        @if ($rating->user_id === auth()->id())
                            <button type="button" onclick="confirmDelete({{ $rating->id }})"
                                class="text-stone-600 hover:text-red-500 text-[10px] uppercase font-bold transition">
                                Eliminar
                            </button>
                        @endif
                    @endauth
                </div>

                <p class="text-stone-300 leading-relaxed">{{ $rating->comment }}</p>
                <p class="text-stone-600 text-[10px] uppercase font-bold mt-2">— {{ $rating->user->name ?? 'Usuario' }}</p>
            </div>
        @empty
            <div class="text-center py-12 border border-dashed border-stone-800 rounded-2xl">
                <p class="text-stone-500">Aún no hay reseñas. ¡Sé el primero en valorar a este barbero!</p>
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