<x-barber-layout>
    <x-slot name="title">Ventas</x-slot>

    <x-slot name="subtitle">
        <span class="hidden sm:block">Gestiona tus ventas</span>
    </x-slot>

    @livewire('barber.sales.create', [], key('sales-create'))

    @livewire('admin.clients.client-selector-modal', [], key('client-selector-modal'))

</x-barber-layout>
