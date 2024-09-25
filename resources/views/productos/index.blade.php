<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Listado de Productos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <!-- Botón para crear nuevo producto -->
                    <div class="mb-4">
                        <x-primary-button>
                            <a href="{{ route('productos.create') }}">
                                {{ __('Crear nuevo Producto') }}
                            </a>
                        </x-primary-button>
                    </div>

                    <!-- Mensaje de éxito -->
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Tabla de productos -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-300 table-auto text-left">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 text-xs font-medium text-gray-500 uppercase">Nombre</th>
                                    <th class="px-4 py-2 text-xs font-medium text-gray-500 uppercase">Descripción</th>
                                    <th class="px-4 py-2 text-xs font-medium text-gray-500 uppercase">Precio</th>
                                    <th class="px-4 py-2 text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                                    <th class="px-4 py-2 text-xs font-medium text-gray-500 uppercase">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($productos as $producto)
                                    <tr>
                                        <td class="px-4 py-2 whitespace-nowrap">{{ $producto->nombre }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap">{{ $producto->descripcion }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap">{{ $producto->precio }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap">{{ $producto->cantidad }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap flex">
                                            <!-- Botón Ver -->
                                            <x-secondary-button>
                                                <a href="{{ route('productos.show', $producto) }}">
                                                    {{ __('Ver') }}
                                                </a>
                                            </x-secondary-button>
                                            
                                            <!-- Botón Editar -->
                                            <x-primary-button class="ml-2">
                                                <a href="{{ route('productos.edit', $producto) }}">
                                                    {{ __('Editar') }}
                                                </a>
                                            </x-primary-button>

                                            <!-- Botón Eliminar -->
                                            <form action="{{ route('productos.destroy', $producto) }}" method="POST" class="inline-block ml-2">
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
