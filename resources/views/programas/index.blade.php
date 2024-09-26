<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Listado de Programas de Riego') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-auto shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- Botón para crear nuevo programa -->
                    <x-primary-button class="mb-4">
                        <a href="{{ route('programa-riego.create') }}" class="text-white">
                            {{ __('Crear nuevo Programa de Riego') }}
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
                            height: 50px; /* Altura total de la barra */
                            width: 30px; /* Ancho total de la barra */
                            margin: 0 auto;
                        }
                        .bar {
                            width: 8px; /* Ancho de cada barra individual */
                            margin: 0 1px;
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
                    </style>

                    <!-- Tabla con las gráficas de barras -->
                    <table class="table-auto w-full border-collapse border border-gray-300">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-2 py-2 border">ID</th>
                                @for ($i = 1; $i <= 14; $i++)
                                    <th class="px-2 py-2 border">Surco {{ $i }}</th>
                                @endfor
                                <th class="px-2 py-2 border">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($programas as $programa)
                                <tr>
                                    <td class="px-2 py-2 border">
                                        <!-- Formulario para seleccionar el programa actual -->
                                        <form action="{{ route('set.current-program') }}" method="POST">
                                            @csrf
                                            <!-- Checkbox exclusivo -->
                                            <input type="radio" name="programa_riego_id" value="{{ $programa->id }}"
                                                @if(isset($programaActual) && $programaActual->programa_riego_id == $programa->id) checked @endif
                                                onchange="this.form.submit();">
                                            {{ $programa->id }}
                                        </form>
                                    </td>
                                    @for ($i = 1; $i <= 14; $i++)
                                        <td class="px-2 py-2 border">
                                            <div class="bar-container">
                                                <div class="bar bar-volumen" style="height: {{ ($programa["volumen{$i}"] / 25) * 100 }}%;"></div>
                                                <div class="bar bar-fertilizante1" style="height: {{ ($programa["fertilizante1_{$i}"] / 10) * 100 }}%;"></div>
                                                <div class="bar bar-fertilizante2" style="height: {{ ($programa["fertilizante2_{$i}"] / 10) * 100 }}%;"></div>
                                            </div>
                                        </td>
                                    @endfor
                                    <td class="px-2 py-2 border">
                                        <x-secondary-button class="mr-2">
                                            <a href="{{ route('programa-riego.show', $programa) }}" class="text-black">{{ __('Ver') }}</a>
                                        </x-secondary-button>

                                        <x-primary-button class="mr-2">
                                            <a href="{{ route('programa-riego.edit', $programa) }}" class="text-white">{{ __('Editar') }}</a>
                                        </x-primary-button>

                                        <!-- Formulario de eliminación -->
                                        <form action="{{ route('programa-riego.destroy', $programa) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <x-danger-button onclick="return confirm('¿Estás seguro de eliminar este programa de riego?')">
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
