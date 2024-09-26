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
                    <form action="{{ route('aspercion.update', $aspercion) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="fecha" class="form-label">Fecha</label>
                            <input type="date" class="form-control" id="fecha" name="fecha" value="{{ $aspercion->fecha }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="hora" class="form-label">Hora</label>
                            <input type="time" class="form-control" id="hora" name="hora" value="{{ \Carbon\Carbon::parse($aspercion->hora)->format('H:i') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="volumen" class="form-label">Volumen</label>
                            <input type="number" step="0.01" class="form-control" id="volumen" name="volumen" value="{{ $aspercion->volumen }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="tipo_aspercion" class="form-label">Tipo de Asperción</label>
                            <input type="text" class="form-control" id="tipo_aspercion" name="tipo_aspercion" value="{{ $aspercion->tipo_aspercion }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="responsable" class="form-label">Responsable</label>
                            <input type="text" class="form-control" id="responsable" name="responsable" value="{{ $aspercion->responsable }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="user_id" class="form-label">Usuario Responsable</label>
                            <select class="form-control" id="user_id" name="user_id" required>
                                @foreach ($usuarios as $usuario)
                                    <option value="{{ $usuario->id }}" {{ $usuario->id == $aspercion->user_id ? 'selected' : '' }}>
                                        {{ $usuario->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Productos y Cantidades -->
                        <div id="productos-container" class="mt-4">
                            <x-input-label :value="__('Productos y Cantidades')" />
                            @foreach($aspercion->productos as $index => $producto)
                                <div class="producto-item mt-2">
                                    <select name="productos[]" class="block mt-1 w-full" required>
                                        @foreach($productos as $prod)
                                            <option value="{{ $prod->id }}" {{ $prod->id == $producto->id ? 'selected' : '' }}>
                                                {{ $prod->nombre }} (Stock: {{ $prod->cantidad }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-text-input class="block mt-1 w-full" type="number" name="cantidades[]" min="1" value="{{ $producto->pivot->cantidad_de_producto }}" required />
                                </div>
                            @endforeach
                        </div>

                        <!-- Botón para agregar más productos -->
                        <button type="button" id="agregar-producto" class="mt-2 bg-blue-500 text-white px-4 py-2 rounded">Agregar Producto</button>



                        <button type="submit" class="btn btn-primary">Actualizar</button>
                    </form>
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

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
                @foreach($productos as $producto)
                    <option value="{{ $producto->id }}">{{ $producto->nombre }} (Stock: {{ $producto->cantidad }})</option>
                @endforeach
            </select>
            <x-text-input class="block mt-1 w-full" type="number" name="cantidades[]" min="1" placeholder="Cantidad utilizada" required />
        `;
        container.appendChild(newItem);
    });
</script>