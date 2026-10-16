<x-barber-layout>
    <x-slot name="title">Ordenes</x-slot>

    <x-slot name="subtitle">
        <span class="hidden sm:block">Lista de ordenes</span>
    </x-slot>

    @livewire('client.orders.index')

</x-barber-layout>