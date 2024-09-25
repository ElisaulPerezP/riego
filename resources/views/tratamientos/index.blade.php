<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Listado de Tratamientos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <x-primary-button class="mb-4">
                        <a href="{{ route('tratamiento.create') }}" class="text-white">
                            {{ __('Crear nuevo Tratamiento') }}
                        </a>
                    </x-primary-button>

                    @if (session('success'))
                        <div class="bg-green-500 text-white p-4 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <table class="table-auto w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-4 py-2 border">Agrónomo</th>
                                <th class="px-4 py-2 border">Cantidad</th>
                                <th class="px-4 py-2 border">Frecuencia</th>
                                <th class="px-4 py-2 border">Producto</th>
                                <th class="px-4 py-2 border">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tratamientos as $tratamiento)
                                <tr>
                                    <td class="px-4 py-2 border">{{ $tratamiento->agronomo }}</td>
                                    <td class="px-4 py-2 border">{{ $tratamiento->cantidad }}</td>
                                    <td class="px-4 py-2 border">{{ $tratamiento->frecuencia }}</td>
                                    <td class="px-4 py-2 border">
                                        @foreach ($tratamiento->productos as $producto)
                                            {{ $producto->nombre }}{{ !$loop->last ? ',' : '' }}
                                        @endforeach
                                    </td>
                                    <td class="px-4 py-2 border">
                                        <!-- Botón Ver -->
                                        <x-secondary-button class="mr-2">
                                            <a href="{{ route('tratamiento.show', $tratamiento) }}" class="text-black">{{ __('Ver') }}</a>
                                        </x-secondary-button>

                                        <!-- Botón Editar -->
                                        <x-primary-button class="mr-2">
                                            <a href="{{ route('tratamiento.edit', $tratamiento) }}" class="text-white">{{ __('Editar') }}</a>
                                        </x-primary-button>

                                        <!-- Botón Eliminar -->
                                        <form action="{{ route('tratamiento.destroy', $tratamiento) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <x-danger-button onclick="return confirm('¿Estás seguro de eliminar este tratamiento?')">
                                                {{ __('Eliminar') }}
                                            </x-danger-button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
