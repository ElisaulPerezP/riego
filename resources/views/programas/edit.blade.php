<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Programa de Riego') }}
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

                    <!-- Formulario para editar el programa de riego -->
                    <form action="{{ route('programa-riego.update', $programaRiego) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Veces por día -->
                        <div class="mb-6">
                            <label for="veces_por_dia" class="block text-sm font-medium text-gray-700">Veces por Día</label>
                            <input type="number" name="veces_por_dia" id="veces_por_dia" value="{{ old('veces_por_dia', $programaRiego->veces_por_dia) }}" min="1" class="mt-1 block w-full" required>
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

                        <!-- Estilos para las barras y sliders -->
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

                        <!-- Gráfica de barras y sliders por surco -->
                        <div class="grid grid-cols-4 gap-4">
                            @for ($i = 1; $i <= 14; $i++)
                                <div>
                                    <!-- Barra gráfica -->
                                    <div class="bar-container" id="bar-container-{{ $i }}">
                                        <div class="bar bar-volumen" id="bar-volumen-{{ $i }}" style="height: {{ ($programaRiego["volumen{$i}"] / 25) * 100 }}%;"></div>
                                        <div class="bar bar-fertilizante1" id="bar-fertilizante1-{{ $i }}" style="height: {{ ($programaRiego["fertilizante1_{$i}"] / 10) * 100 }}%;"></div>
                                        <div class="bar bar-fertilizante2" id="bar-fertilizante2-{{ $i }}" style="height: {{ ($programaRiego["fertilizante2_{$i}"] / 10) * 100 }}%;"></div>
                                    </div>
                                    <div class="period-label">Surco {{ $i }}</div>

                                    <!-- Sliders para ajustar los valores -->
                                    <div class="mt-2">
                                        <label for="volumen{{ $i }}" class="block text-sm font-medium text-gray-700">Volumen</label>
                                        <input type="range" name="volumen{{ $i }}" id="volumen{{ $i }}" min="0" max="25" step="0.1" value="{{ old("volumen{$i}", $programaRiego["volumen{$i}"]) }}" class="w-full slider" data-bar-id="bar-volumen-{{ $i }}" data-max="25">

                                        <label for="fertilizante1_{{ $i }}" class="block text-sm font-medium text-gray-700">Fertilizante 1</label>
                                        <input type="range" name="fertilizante1_{{ $i }}" id="fertilizante1_{{ $i }}" min="0" max="10" step="0.1" value="{{ old("fertilizante1_{$i}", $programaRiego["fertilizante1_{$i}"]) }}" class="w-full slider" data-bar-id="bar-fertilizante1-{{ $i }}" data-max="10">

                                        <label for="fertilizante2_{{ $i }}" class="block text-sm font-medium text-gray-700">Fertilizante 2</label>
                                        <input type="range" name="fertilizante2_{{ $i }}" id="fertilizante2_{{ $i }}" min="0" max="10" step="0.1" value="{{ old("fertilizante2_{$i}", $programaRiego["fertilizante2_{$i}"]) }}" class="w-full slider" data-bar-id="bar-fertilizante2-{{ $i }}" data-max="10">
                                    </div>
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

                    <!-- JavaScript para actualizar las barras en tiempo real -->
                    <script>
                        document.querySelectorAll('.slider').forEach(function(slider) {
                            slider.addEventListener('input', function() {
                                var barId = this.getAttribute('data-bar-id');
                                var bar = document.getElementById(barId);
                                var max = this.getAttribute('data-max');
                                var value = this.value;
                                var heightPercentage = (value / max) * 100;
                                bar.style.height = heightPercentage + '%';
                            });
                        });
                    </script>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
