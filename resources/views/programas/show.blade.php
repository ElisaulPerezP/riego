<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detalle del Programa de Riego') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- Botón para regresar al listado -->
                    <x-secondary-button class="mb-4">
                        <a href="{{ route('programa-riego.index') }}" class="text-black">
                            {{ __('Volver al Listado') }}
                        </a>
                    </x-secondary-button>

                    <!-- Detalles del programa -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-2">Información General</h3>
                        <p><strong>ID:</strong> {{ $programaRiego->id }}</p>
                        <p><strong>Veces por Día:</strong> {{ $programaRiego->veces_por_dia }}</p>
                        <!-- Puedes agregar más información general si es necesario -->
                    </div>

                    <!-- Leyenda de las barras -->
                    <div class="flex mb-4">
                        <div class="flex items-center mr-4">
                            <div class="bar-legend bar-volumen mr-2"></div>
                            <span>Volumen (0-25)</span>
                        </div>
                        <div class="flex items-center mr-4">
                            <div class="bar-legend bar-fertilizante1 mr-2"></div>
                            <span>Fertilizante 1 (0-10)</span>
                        </div>
                        <div class="flex items-center">
                            <div class="bar-legend bar-fertilizante2 mr-2"></div>
                            <span>Fertilizante 2 (0-10)</span>
                        </div>
                    </div>

                    <!-- Estilos para las barras -->
                    <style>
                        .bar-legend {
                            width: 10px;
                            height: 20px;
                        }
                        .bar-container {
                            display: flex;
                            align-items: flex-end;
                            justify-content: center;
                            height: 200px; /* Altura total de la barra */
                            width: 50px; /* Ancho total de la barra */
                            margin: 0 auto;
                        }
                        .bar {
                            width: 12px; /* Ancho de cada barra individual */
                            margin: 0 2px;
                        }
                        .bar-volumen {
                            background-color: blue;
                        }
                        .bar-fertilizante1 {
                            background-color: green;
                        }
                        .bar-fertilizante2 {
                            background-color: red;
                        }
                        .period-label {
                            text-align: center;
                            margin-top: 5px;
                        }
                    </style>

                    <!-- Gráfica de barras por periodo -->
                    <div class="grid grid-cols-4 gap-4">
                        @for ($i = 1; $i <= 14; $i++)
                            <div>
                                <div class="bar-container">
                                    <div class="bar bar-volumen" style="height: {{ ($programaRiego["volumen{$i}"] / 25) * 100 }}%;"></div>
                                    <div class="bar bar-fertilizante1" style="height: {{ ($programaRiego["fertilizante1_{$i}"] / 10) * 100 }}%;"></div>
                                    <div class="bar bar-fertilizante2" style="height: {{ ($programaRiego["fertilizante2_{$i}"] / 10) * 100 }}%;"></div>
                                </div>
                                <div class="period-label">Surco {{ $i }}</div>
                            </div>
                        @endfor
                    </div>

                    <!-- Botones de acción -->
                    <div class="mt-6">
                        <x-primary-button class="mr-2">
                            <a href="{{ route('programa-riego.edit', $programaRiego) }}" class="text-white">{{ __('Editar') }}</a>
                        </x-primary-button>

                        <form action="{{ route('programa-riego.destroy', $programaRiego) }}" method="POST" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <x-danger-button onclick="return confirm('¿Estás seguro de eliminar este programa de riego?')">
                                {{ __('Eliminar') }}
                            </x-danger-button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
