<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detalles del Stock') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold">{{ $stock->producto->nombre }}</h3>
                    <p><strong>Cantidad en Stock:</strong> {{ $stock->cantidad_en_stock }}</p>
                    <p><strong>Días para Vencimiento:</strong> {{ $stock->dias_para_vencimiento }}</p>

                    <x-secondary-button class="mt-4">
                        <a href="{{ route('stocks.index') }}">{{ __('Volver al listado') }}</a>
                    </x-secondary-button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
