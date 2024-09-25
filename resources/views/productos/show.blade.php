<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detalles del Producto') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold">{{ $producto->nombre }}</h3>
                    <p><strong>Descripción:</strong> {{ $producto->descripcion }}</p>
                    <p><strong>Precio:</strong> {{ $producto->precio }}</p>
                    <p><strong>Cantidad:</strong> {{ $producto->cantidad }}</p>
                    <p><strong>Fecha de Vencimiento:</strong> {{ $producto->fecha_vencimiento }}</p>
                    <p><strong>Responsable:</strong> {{ $producto->responsable }}</p>
                    <p><strong>Teléfono de Emergencia:</strong> {{ $producto->telefono_emergencia }}</p>
                    <!-- Mostrar más detalles si es necesario -->

                    <!-- Botón para volver al listado -->
                    <div class="mt-4">
                        <x-secondary-button>
                            <a href="{{ route('productos.index') }}">
                                {{ __('Volver al listado') }}
                            </a>
                        </x-secondary-button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
