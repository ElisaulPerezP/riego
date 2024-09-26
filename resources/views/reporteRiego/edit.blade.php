<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Reporte de Riego') }}
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

                    <!-- Formulario para editar el reporte de riego -->
                    <form action="{{ route('reportes.update', $reporteRiego) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Campos por surco -->
                        <div class="grid grid-cols-4 gap-4">
                            @for ($i = 1; $i <= 14; $i++)
                                <div>
                                    <label for="volumen{{ $i }}" class="block text-sm font-medium text-gray-700">Volumen Surco {{ $i }}</label>
                                    <input type="number" name="volumen{{ $i }}" id="volumen{{ $i }}" value="{{ old("volumen{$i}", $reporteRiego["volumen{$i}"]) }}" min="0" max="25" step="0.1" class="w-full" required>

                                    <label for="tiempo{{ $i }}" class="block text-sm font-medium text-gray-700">Tiempo Surco {{ $i }}</label>
                                    <input type="time" name="tiempo{{ $i }}" id="tiempo{{ $i }}" value="{{ old("tiempo{$i}", $reporteRiego["tiempo{$i}"]) }}" class="w-full" required>

                                    <label for="mensaje{{ $i }}" class="block text-sm font-medium text-gray-700">Mensaje Surco {{ $i }}</label>
                                    <input type="text" name="mensaje{{ $i }}" id="mensaje{{ $i }}" value="{{ old("mensaje{$i}", $reporteRiego["mensaje{$i}"]) }}" class="w-full">
                                </div>
                            @endfor
                        </div>

                        <!-- Botón para guardar cambios -->
                        <div class="mt-6">
                            <x-primary-button type="submit">
                                {{ __('Guardar Cambios') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
