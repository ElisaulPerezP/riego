<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Listado de Stocks') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Botón de Crear nuevo Stock -->
                    <div class="mb-4">
                        <x-primary-button>
                            <a href="{{ route('stocks.create') }}">
                                {{ __('Crear nuevo Stock') }}
                            </a>
                        </x-primary-button>
                    </div>

                    <!-- Mensaje de éxito -->
                    @if (session('success'))
                        <div class="bg-green-500 text-white p-4 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Tabla de Stocks -->
                    <table class="table-auto w-full border-collapse border border-gray-200">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="border px-4 py-2">Producto</th>
                                <th class="border px-4 py-2">Cantidad en Stock</th>
                                <th class="border px-4 py-2">Días para Vencimiento</th>
                                <th class="border px-4 py-2">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($stocks as $stock)
                                <tr class="bg-white">
                                    <td class="border px-4 py-2">{{ $stock->producto->nombre }}</td>
                                    <td class="border px-4 py-2">{{ $stock->cantidad_en_stock }}</td>
                                    <td class="border px-4 py-2">
                                        {{ $stock->dias_para_vencimiento > 0 ? $stock->dias_para_vencimiento : 'Vencido' }}
                                    </td>
                                    <td class="border px-4 py-2">
                                        <!-- Botón Ver -->
                                        <x-secondary-button>
                                            <a href="{{ route('stocks.show', $stock) }}">{{ __('Ver') }}</a>
                                        </x-secondary-button>

                                        <!-- Botón Editar -->
                                        <x-primary-button>
                                            <a href="{{ route('stocks.edit', $stock) }}">{{ __('Editar') }}</a>
                                        </x-primary-button>

                                        <!-- Botón Eliminar -->
                                        <form action="{{ route('stocks.destroy', $stock) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <x-danger-button class="inline-flex items-center">
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
