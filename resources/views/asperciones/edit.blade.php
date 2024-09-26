<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Asperción') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Formulario para editar asperción -->
                    <form method="POST" action="{{ route('aspercion.update', $aspercion) }}">
                        @csrf
                        @method('PUT')

                        <!-- Fecha -->
                        <div>
                            <x-input-label for="fecha" :value="__('Fecha')" />
                            <x-text-input id="fecha" class="block mt-1 w-full" type="date" name="fecha" value="{{ $aspercion->fecha }}" required autofocus />
                            <x-input-error :messages="$errors->get('fecha')" class="mt-2" />
                        </div>

                        <!-- Hora -->
                        <div class="mt-4">
                            <x-input-label for="hora" :value="__('Hora')" />
                            <x-text-input id="hora" class="block mt-1 w-full" type="time" name="hora" value="{{ \Carbon\Carbon::parse($aspercion->hora)->format('H:i') }}" required />
                            <x-input-error :messages="$errors->get('hora')" class="mt-2" />
                        </div>

                        <!-- Volumen -->
                        <div class="mt-4">
                            <x-input-label for="volumen" :value="__('Volumen')" />
                            <x-text-input id="volumen" class="block mt-1 w-full" type="number" step="0.01" name="volumen" value="{{ $aspercion->volumen }}" required />
                            <x-input-error :messages="$errors->get('volumen')" class="mt-2" />
                        </div>

                        <!-- Tipo de Asperción -->
                        <div class="mt-4">
                            <x-input-label for="tipo_aspercion" :value="__('Tipo de Asperción')" />
                            <x-text-input id="tipo_aspercion" class="block mt-1 w-full" type="text" name="tipo_aspercion" value="{{ $aspercion->tipo_aspercion }}" required />
                            <x-input-error :messages="$errors->get('tipo_aspercion')" class="mt-2" />
                        </div>

                        <!-- Responsable -->
                        <div class="mt-4">
                            <x-input-label for="responsable" :value="__('Responsable')" />
                            <x-text-input id="responsable" class="block mt-1 w-full" type="text" name="responsable" value="{{ $aspercion->responsable }}" required />
                            <x-input-error :messages="$errors->get('responsable')" class="mt-2" />
                        </div>

                        <!-- Usuario Responsable -->
                        <div class="mt-4">
                            <x-input-label for="user_id" :value="__('Usuario Responsable')" />
                            <select id="user_id" name="user_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                                @foreach($usuarios as $usuario)
                                    <option value="{{ $usuario->id }}" {{ $usuario->id == $aspercion->user_id ? 'selected' : '' }}>
                                        {{ $usuario->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
                        </div>

                        <!-- Productos y Cantidades -->
                        <div id="productos-container" class="mt-4">
                            <x-input-label :value="__('Productos y Cantidades')" />
                            @foreach($aspercion->productos as $index => $producto)
                                <div class="producto-item mt-2">
                                    <select name="productos[]" class="block mt-1 w-full" required>
                                        @foreach($productos as $prod)
                                            <option value="{{ $prod->id }}" {{ $prod->id == $producto->id ? 'selected' : '' }}>
                                                {{ $prod->nombre }} (Presentación: {{ $prod->cantidad }}, Stock: {{ $prod->stock->cantidad_en_stock ?? 'N/A' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('productos.*')" class="mt-2" />

                                    <x-input-label :value="__('Cantidad a usar')" class="mt-2" />
                                    <input class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" type="number" name="cantidades[]" min="1" value="{{ $producto->pivot->cantidad_de_producto }}" required />
                                    <x-input-error :messages="$errors->get('cantidades.*')" class="mt-2" />
                                </div>
                            @endforeach
                        </div>

                        <!-- Botón para agregar más productos -->
                        <button type="button" id="agregar-producto" class="mt-2 bg-blue-500 text-white px-4 py-2 rounded">Agregar Producto</button>

                        <!-- Botón de Enviar -->
                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-3">
                                {{ __('Actualizar') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    // JavaScript para agregar más campos de producto y cantidad
    document.getElementById('agregar-producto').addEventListener('click', function() {
        const container = document.getElementById('productos-container');
        const newItem = document.createElement('div');
        newItem.classList.add('producto-item', 'mt-2');
        newItem.innerHTML = `
            <select name="productos[]" class="block mt-1 w-full" required>
                <option value="">{{ __('Selecciona un producto') }}</option>
                @foreach($productos as $prod)
                    <option value="{{ $prod->id }}">
                        {{ $prod->nombre }} (Presentación: {{ $prod->cantidad }}, Stock: {{ $prod->stock->cantidad_en_stock ?? 'N/A' }})
                    </option>
                @endforeach
            </select>
            <x-input-label :value="__('Cantidad a usar')" class="mt-2" />
            <input class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" type="number" name="cantidades[]" min="1" placeholder="Cantidad utilizada" required />
        `;
        container.appendChild(newItem);
    });
</script>
