<x-barber-layout>
    <x-slot name="title">Productos</x-slot>

    <x-slot name="subtitle">
        <span class="hidden sm:block">Gestiona tu productos</span>
    </x-slot>

    @livewire('admin.products.index', [], key('products-index'))

    @livewire('admin.products.selector-modal', [], key('product-selector-modal'))

</x-barber-layout>
