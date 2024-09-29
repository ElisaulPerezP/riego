<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard - Programa Actual de Riego') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-auto shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- Mensajes de éxito o error (opcional) -->
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

                    <!-- Programa Actual de Riego -->
                    @if(isset($programaActual) && $programaActual->programaRiego)
                        <div class="mb-8">
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

                            <!-- Tabla con el programa actual de riego -->
                            <h3 class="text-lg font-semibold mb-4">{{ __('Programa Actual de Riego') }}</h3>
                            <table class="table-auto w-full border-collapse border border-gray-300 mb-6">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="px-2 py-2 border">ID</th>
                                        @for ($i = 1; $i <= 14; $i++)
                                            <th class="px-2 py-2 border">Surco {{ $i }}</th>
                                        @endfor
                                        <!-- Eliminada la columna "Mensaje" -->
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="px-2 py-2 border">
                                            {{ $programaActual->programaRiego->id }}
                                            <!-- Agregado el letrero "Mensaje" debajo del ID -->
                                            <div class="mt-2 font-semibold">{{ __('Mensaje') }}</div>
                                        </td>
                                        @for ($i = 1; $i <= 14; $i++)
                                            <td class="px-2 py-2 border">
                                                <div class="bar-container">
                                                    <div class="bar bar-volumen" style="height: {{ ($programaActual->programaRiego["volumen{$i}"] / 25) * 100 }}%;"></div>
                                                    <div class="bar bar-fertilizante1" style="height: {{ ($programaActual->programaRiego["fertilizante1_{$i}"] / 10) * 100 }}%;"></div>
                                                    <div class="bar bar-fertilizante2" style="height: {{ ($programaActual->programaRiego["fertilizante2_{$i}"] / 10) * 100 }}%;"></div>
                                                </div>
                                                <!-- Agregado el mensaje correspondiente debajo de las barras -->
                                                <div class="mt-2 text-sm text-center">
                                                    {{ $programaActual->programaRiego["mensaje{$i}"] }}
                                                </div>
                                            </td>
                                        @endfor
                                        <!-- Eliminada la columna "Mensaje" del cuerpo de la tabla -->
                                    </tr>
                                </tbody>
                            </table>
                            <!-- Fin de la tabla -->
                        </div>
                    @else
                        <!-- Opcional: Mensaje si no hay un programa actual -->
                        <div class="text-center text-gray-500 mb-8">
                            {{ __('No hay un programa de riego actual establecido.') }}
                        </div>
                    @endif

                    <!-- Último Reporte de Riego -->
                    @if(isset($ultimoReporte))
                        <div>
                            <!-- Leyenda de las barras -->
                            <div class="flex mb-4">
                                <div class="flex items-center mr-4">
                                    <div class="bar-legend bar-volumen mr-2"></div>
                                    <span>Volumen (0-25 lts)</span>
                                </div>
                                <div class="flex items-center mr-4">
                                    <div class="bar-legend bar-tiempo mr-2"></div>
                                    <span>Tiempo (0-20 mins)</span>
                                </div>
                            </div>

                            <!-- Estilos para las barras del reporte -->
                            <style>
                                .bar-legend {
                                    width: 10px;
                                    height: 20px;
                                }
                                .bar-container-reporte {
                                    display: flex;
                                    align-items: flex-end;
                                    justify-content: center;
                                    height: 50px; /* Altura total de la barra */
                                    width: 30px; /* Ancho total de la barra */
                                    margin: 0 auto;
                                }
                                .bar-reporte {
                                    width: 8px; /* Ancho de cada barra individual */
                                    margin: 0 1px;
                                }
                                .bar-volumen {
                                    background-color: blue;
                                }
                                .bar-tiempo {
                                    background-color: orange;
                                }
                            </style>

                            <!-- Tabla con el último reporte de riego -->
                            <h3 class="text-lg font-semibold mb-4">{{ __('Último Reporte de Riego') }}</h3>
                            <table class="table-auto w-full border-collapse border border-gray-300 mb-6">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="px-2 py-2 border">ID</th>
                                        @for ($i = 1; $i <= 14; $i++)
                                            <th class="px-2 py-2 border">Surco {{ $i }}</th>
                                        @endfor
                                        <!-- Eliminada la columna "Mensaje" -->
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="px-2 py-2 border">
                                            {{ $ultimoReporte->id }}
                                            <!-- Agregado el letrero "Mensaje" debajo del ID -->
                                            <div class="mt-2 font-semibold">{{ __('Mensaje') }}</div>
                                        </td>
                                        @for ($i = 1; $i <= 14; $i++)
                                            <td class="px-2 py-2 border">
                                                <div class="bar-container-reporte">
                                                    <div class="bar bar-volumen" style="height: {{ ($ultimoReporte["volumen{$i}"] / 25) * 100 }}%;"></div>
                                                    @php
                                                        // Convertir tiempo de H:i:s a minutos decimales
                                                        $tiempo = $ultimoReporte["tiempo{$i}"];
                                                        list($horas, $minutos, $segundos) = explode(':', $tiempo);
                                                        $totalMinutos = $horas * 60 + $minutos + ($segundos / 60);
                                                        // Dado que el rango es 0-20 minutos
                                                        $minutosDecimal = $totalMinutos / 20;
                                                        // Asegurarse de que no exceda 1 (100%)
                                                        $minutosDecimal = min($minutosDecimal, 1);
                                                    @endphp
                                                    <div class="bar bar-tiempo" style="height: {{ ($minutosDecimal) * 100 }}%;"></div>
                                                </div>
                                                <!-- Agregado el mensaje correspondiente debajo de las barras -->
                                                <div class="mt-2 text-sm text-center">
                                                    {{ $ultimoReporte["mensaje{$i}"] }}
                                                </div>
                                            </td>
                                        @endfor
                                        <!-- Eliminada la columna "Mensaje" del cuerpo de la tabla -->
                                    </tr>
                                </tbody>
                            </table>
                            <!-- Fin de la tabla -->

                            <!-- Botón para regenerar gráficos -->
                            <div class="mb-8 text-center">
                                <a href="{{ route('graph') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    {{ __('Regenerar Gráficos') }}
                                </a>
                            </div>
                             <!-- Mostrar la imagen generada si existe -->
                             @if (isset($public_image_url))
                                <div class="mt-8">
                                    <h3 class="text-lg font-semibold mb-4">{{ __('Gráfica de Riego') }}</h3>
                                    <img src="{{ $public_image_url }}" alt="Gráfica de Riego" class="mx-auto">
                                </div>
                            @endif
                        </div>
                    @else
                        <!-- Opcional: Mensaje si no hay reportes -->
                        <div class="text-center text-gray-500">
                            {{ __('No hay reportes de riego disponibles.') }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
