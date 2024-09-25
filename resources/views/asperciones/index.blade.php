<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Listado de Asperciones') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Botón para crear nueva Asperción -->
                    <div class="mb-4">
                        <x-primary-button>
                            <a href="{{ route('aspercion.create') }}">
                                {{ __('Crear nueva Asperción') }}
                            </a>
                        </x-primary-button>
                    </div>

                    <!-- Mensaje de éxito -->
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Tabla de Asperciones -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-300">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hora</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Volumen</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo de Asperción</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Responsable</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usuario Responsable</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($asperciones as $aspercion)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $aspercion->fecha }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $aspercion->hora }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $aspercion->volumen }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $aspercion->tipo_aspercion }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $aspercion->responsable }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $aspercion->user->name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <!-- Botón Ver -->
                                            <x-secondary-button>
                                                <a href="{{ route('aspercion.show', $aspercion) }}">
                                                    {{ __('Ver') }}
                                                </a>
                                            </x-secondary-button>
                                            
                                            <!-- Botón Editar -->
                                            <x-primary-button class="ml-2">
                                                <a href="{{ route('aspercion.edit', $aspercion) }}">
                                                    {{ __('Editar') }}
                                                </a>
                                            </x-primary-button>

                                            <!-- Botón Eliminar -->
                                            <form action="{{ route('aspercion.destroy', $aspercion) }}" method="POST" style="display:inline-block;" class="ml-2">
                                                @csrf
                                                @method('DELETE')
                                                <x-danger-button onclick="return confirm('¿Estás seguro?')">
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
    </div>
</x-app-layout>
