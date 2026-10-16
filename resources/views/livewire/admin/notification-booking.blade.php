<div wire:poll.15s="getPendingCount" class="relative inline-block">
    <a href="{{ route('admin.bookings.index') }}"
        class="relative p-2 text-gray-400 hover:text-gray-600 focus:outline-none transition" title="Ver citas pendientes">
        <i class="fa-solid fa-bell text-lg md:text-xl"></i>

        @if ($count > 0)
            <span class="absolute top-1 right-1 w-4 h-4 bg-amber-500 rounded-full animate-ping opacity-75"></span>

            <span
                class="absolute top-1 right-1 min-w-4 h-4 px-1 bg-amber-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center border border-white">
                {{ $count }}
            </span>
        @endif
    </a>
</div>
