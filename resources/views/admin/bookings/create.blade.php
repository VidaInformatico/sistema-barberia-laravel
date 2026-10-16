<x-barber-layout>
    <x-slot name="title">Nueva reserva</x-slot>

    <x-slot name="subtitle">
        <span class="hidden sm:block">Gestiona tu reserva</span>
    </x-slot>

    @livewire('admin.bookings.create', [], key('booking-create'))

    @livewire('admin.clients.client-selector-modal', [], key('client-selector-modal'))

</x-barber-layout>