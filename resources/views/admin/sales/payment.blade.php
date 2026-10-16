<x-barber-layout>
    <x-slot name="title">Historial Pagos</x-slot>

    <x-slot name="subtitle">
        <span class="hidden sm:block">Gestiona tus pagos de ventas</span>
    </x-slot>

    @livewire('admin.sales.payments', ['sale' => $sale])

</x-barber-layout>
