<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Listado de Reportes de Riego') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-auto shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- Botón para crear nuevo reporte -->
                    <x-primary-button class="mb-4">
                        <a href="{{ route('reportes.create') }}" class="text-white">
                            {{ __('Crear nuevo Reporte de Riego') }}
                        </a>
                    </x-primary-button>

                    <!-- Mensajes de éxito o error -->
                    @if (session('success'))
                        <div class="bg-green-500 text-white p-4 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="bg-red-500 text-white p-4 rounded mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Tabla con los reportes de riego -->
                    <table class="table-auto w-full border-collapse border border-gray-300">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-2 py-2 border">ID</th>
                                <th class="px-2 py-2 border">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reportes as $reporteRiego)
                                <tr>
                                    <td class="px-2 py-2 border">{{ $reporteRiego->id }}</td>
                                    <td class="px-2 py-2 border">
                                        <x-secondary-button class="mr-2">
                                            <a href="{{ route('reportes.show', $reporteRiego) }}" class="text-black">{{ __('Ver') }}</a>
                                        </x-secondary-button>

                                        <x-primary-button class="mr-2">
                                            <a href="{{ route('reportes.edit', $reporteRiego) }}" class="text-white">{{ __('Editar') }}</a>
                                        </x-primary-button>

                                        <!-- Formulario de eliminación -->
                                        <form action="{{ route('reportes.destroy', $reporteRiego) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <x-danger-button onclick="return confirm('¿Estás seguro de eliminar este reporte de riego?')">
                                                {{ __('Eliminar') }}
                                            </x-danger-button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <!-- Fin de la tabla -->

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
