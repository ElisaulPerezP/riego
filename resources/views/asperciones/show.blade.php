<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detalles de la Asperción') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p><strong>Fecha:</strong> {{ $aspercion->fecha }}</p>
                    <p><strong>Hora:</strong> {{ $aspercion->hora }}</p>
                    <p><strong>Volumen:</strong> {{ $aspercion->volumen }}</p>
                    <p><strong>Tipo de Asperción:</strong> {{ $aspercion->tipo_aspercion }}</p>
                    <p><strong>Responsable:</strong> {{ $aspercion->responsable }}</p>
                    <p><strong>Usuario Responsable:</strong> {{ $aspercion->user->name ?? 'N/A' }}</p>

                    <h3>Productos Utilizados:</h3>
                    <ul>
                        @foreach ($aspercion->productos as $producto)
                            <li>{{ $producto->nombre }}</li>
                        @endforeach
                    </ul>

                    <a href="{{ route('aspercion.index') }}" class="btn btn-secondary">Volver</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
