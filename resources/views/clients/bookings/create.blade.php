<x-barber-layout>
    <x-slot name="title">Nueva reserva</x-slot>

    <x-slot name="subtitle">
        <span class="hidden sm:block">Gestiona tu reserva</span>
    </x-slot>

    @livewire('client.bookings.create')
</x-barber-layout>