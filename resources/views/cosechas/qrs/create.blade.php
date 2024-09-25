<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear QR para Cosecha #') }}{{ $cosecha->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- Información de la cosecha -->
                    <div>
                        <p><strong>{{ __('Fecha de Cosecha:') }}</strong> {{ $cosecha->fecha }}</p>
                        <p><strong>{{ __('Cantidad:') }}</strong> {{ $cosecha->cantidad }}</p>
                        <p><strong>{{ __('Porcentaje:') }}</strong> {{ $cosecha->porcentaje }}%</p>
                        <p><strong>{{ __('Cajas 125g:') }}</strong> {{ $cosecha->cajas125 }}</p>
                        <p><strong>{{ __('Cajas 250g:') }}</strong> {{ $cosecha->cajas250 }}</p>
                        <p><strong>{{ __('Cajas 500g:') }}</strong> {{ $cosecha->cajas500 }}</p>
                    </div>

                    <!-- Botón para Crear QR Automáticamente -->
                    <form method="POST" action="{{ route('qrs.store') }}">
                        @csrf
                        <!-- Campo oculto para el ID de la cosecha -->
                        <input type="hidden" name="cosecha_id" value="{{ $cosecha->id }}">

                        <!-- Botón para enviar el formulario y crear los QR automáticamente -->
                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-4 bg-green-500 hover:bg-green-700">
                                {{ __('Crear QR Automáticamente') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
