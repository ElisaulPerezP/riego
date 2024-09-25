<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detalles del Tratamiento') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold">{{ $tratamiento->agronomo }}</h3>
                    <p><strong>Cantidad:</strong> {{ $tratamiento->cantidad }}</p>
                    <p><strong>Frecuencia:</strong> {{ $tratamiento->frecuencia }}</p>
                    <p><strong>Diagnóstico:</strong> {{ $tratamiento->diagnostico }}</p>
                    <p><strong>Agrónomo Responsable:</strong> {{ $tratamiento->user->name ?? 'N/A' }}</p>
                    <p><strong>Notas:</strong> {{ $tratamiento->notas }}</p>

                    <h4 class="mt-4 text-lg font-semibold">Productos</h4>
                    <ul>
                        @foreach ($tratamiento->productos as $producto)
                            <li>{{ $producto->nombre }}</li>
                        @endforeach
                    </ul>

                    <x-secondary-button class="mt-4">
                        <a href="{{ route('tratamiento.index') }}">
                            {{ __('Volver al listado') }}
                        </a>
                    </x-secondary-button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
