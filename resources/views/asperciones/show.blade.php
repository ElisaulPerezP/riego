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
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left">Producto</th>
                                <th class="px-6 py-3 text-left">Cantidad Utilizada</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($aspercion->productos as $producto)
                                <tr>
                                    <td class="px-6 py-4">{{ $producto->nombre }}</td>
                                    <td class="px-6 py-4">{{ $producto->pivot->cantidad_de_producto }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>


                    <a href="{{ route('aspercion.index') }}" class="btn btn-secondary">Volver</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
