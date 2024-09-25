<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detalles de la Cosecha') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold">{{ __('Cosecha de Fecha:') }} {{ $cosecha->fecha }}</h3>
                    <p><strong>{{ __('Cantidad:') }}</strong> {{ $cosecha->cantidad }}</p>
                    <p><strong>{{ __('Porcentaje:') }}</strong> {{ $cosecha->porcentaje }}%</p>

                    <!-- Detalles del empaquetado (cajas) -->
                    <p><strong>{{ __('Cajas de 125g:') }}</strong> {{ $cosecha->cajas125 }}</p>
                    <p><strong>{{ __('Cajas de 250g:') }}</strong> {{ $cosecha->cajas250 }}</p>
                    <p><strong>{{ __('Cajas de 500g:') }}</strong> {{ $cosecha->cajas500 }}</p>

                    <p><strong>{{ __('Responsable:') }}</strong> {{ $cosecha->user->name }}</p>

                    <!-- Productos Relacionados -->
                    <h4 class="text-md font-semibold mt-4">{{ __('Productos Relacionados:') }}</h4>
                    <ul>
                        @foreach($cosecha->productos as $producto)
                            <li>{{ $producto->nombre }}</li>
                        @endforeach
                    </ul>

                    <!-- Códigos Asociados -->
                    <h4 class="text-md font-semibold mt-4">{{ __('Códigos Asociados:') }}</h4>
                    <ul>
                        @foreach($cosecha->codigos as $codigo)
                            <li>{{ $codigo->codigo }}</li>
                        @endforeach
                    </ul>

                    <!-- Botón Volver al listado -->
                    <x-primary-button class="mt-4">
                        <a href="{{ route('cosecha.index') }}" class="text-white">{{ __('Volver al listado') }}</a>
                    </x-primary-button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
