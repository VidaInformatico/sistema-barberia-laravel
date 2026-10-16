<x-barber-layout>
    <x-slot name="title">Reservas</x-slot>

    <x-slot name="subtitle">
        <span class="hidden sm:block">Gestiona tus reservas</span>
    </x-slot>

    @livewire('barber.bookings.index')
</x-barber-layout>