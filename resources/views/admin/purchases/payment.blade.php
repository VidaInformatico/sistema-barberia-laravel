<x-barber-layout>
    <x-slot name="title">Historial Pagos</x-slot>

    <x-slot name="subtitle">
        <span class="hidden sm:block">Gestiona tus pagos de compras</span>
    </x-slot>

    @livewire('admin.purchases.payments', ['purchase' => $purchase])

</x-barber-layout>
