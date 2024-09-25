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

                        <div class="mb-3">
                            <label for="productos" class="form-label">Productos</label>
                            <select class="form-control" id="productos" name="productos[]" multiple required>
                                @foreach ($productos as $producto)
                                    <option value="{{ $producto->id }}" {{ in_array($producto->id, $aspercion->productos->pluck('id')->toArray()) ? 'selected' : '' }}>
                                        {{ $producto->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

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
