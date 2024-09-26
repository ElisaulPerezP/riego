<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detalle del Reporte de Riego') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- Botón para regresar al listado -->
                    <x-secondary-button class="mb-4">
                        <a href="{{ route('reportes.index') }}" class="text-black">
                            {{ __('Volver al Listado') }}
                        </a>
                    </x-secondary-button>

                    <!-- Detalles del reporte -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-2">Información General</h3>
                        <p><strong>ID:</strong> {{ $reporteRiego->id }}</p>
                        <!-- Puedes agregar más información general si es necesario -->
                    </div>

                    <!-- Gráfica de datos por surco -->
                    <div class="grid grid-cols-4 gap-4">
                        @for ($i = 1; $i <= 14; $i++)
                            <div>
                                <h4 class="font-semibold">Surco {{ $i }}</h4>
                                <p><strong>Volumen:</strong> {{ $reporteRiego["volumen{$i}"] }}</p>
                                <p><strong>Tiempo:</strong> {{ $reporteRiego["tiempo{$i}"] }}</p>
                                <p><strong>Mensaje:</strong> {{ $reporteRiego["mensaje{$i}"] }}</p>
                            </div>
                        @endfor
                    </div>

                    <!-- Botones de acción -->
                    <div class="mt-6">
                        <x-primary-button class="mr-2">
                            <a href="{{ route('reportes.edit', $reporteRiego) }}" class="text-white">{{ __('Editar') }}</a>
                        </x-primary-button>

                        <form action="{{ route('reportes.destroy', $reporteRiego) }}" method="POST" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <x-danger-button onclick="return confirm('¿Estás seguro de eliminar este reporte de riego?')">
                                {{ __('Eliminar') }}
                            </x-danger-button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
