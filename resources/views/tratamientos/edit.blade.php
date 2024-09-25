<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Tratamiento') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('tratamiento.update', $tratamiento) }}">
                        @csrf
                        @method('PUT')

                        <!-- Agrónomo -->
                        <div>
                            <x-input-label for="agronomo" :value="__('Agrónomo')" />
                            <x-text-input id="agronomo" class="block mt-1 w-full" type="text" name="agronomo" value="{{ $tratamiento->agronomo }}" required />
                            <x-input-error :messages="$errors->get('agronomo')" class="mt-2" />
                        </div>

                        <!-- Cantidad -->
                        <div class="mt-4">
                            <x-input-label for="cantidad" :value="__('Cantidad')" />
                            <x-text-input id="cantidad" class="block mt-1 w-full" type="number" name="cantidad" value="{{ $tratamiento->cantidad }}" required />
                            <x-input-error :messages="$errors->get('cantidad')" class="mt-2" />
                        </div>

                        <!-- Frecuencia -->
                        <div class="mt-4">
                            <x-input-label for="frecuencia" :value="__('Frecuencia')" />
                            <x-text-input id="frecuencia" class="block mt-1 w-full" type="text" name="frecuencia" value="{{ $tratamiento->frecuencia }}" required />
                            <x-input-error :messages="$errors->get('frecuencia')" class="mt-2" />
                        </div>

                        <!-- Diagnóstico -->
                        <div class="mt-4">
                            <x-input-label for="diagnostico" :value="__('Diagnóstico')" />
                            <textarea id="diagnostico" class="block mt-1 w-full" name="diagnostico" required>{{ $tratamiento->diagnostico }}</textarea>
                            <x-input-error :messages="$errors->get('diagnostico')" class="mt-2" />
                        </div>

                        <!-- Agrónomo Responsable -->
                        <div class="mt-4">
                            <x-input-label for="user_id" :value="__('Agrónomo Responsable')" />
                            <select id="user_id" name="user_id" class="block mt-1 w-full">
                                @foreach ($usuarios as $usuario)
                                    <option value="{{ $usuario->id }}" {{ $usuario->id == $tratamiento->user_id ? 'selected' : '' }}>
                                        {{ $usuario->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
                        </div>

                        <!-- Productos -->
                        <div class="mt-4">
                            <x-input-label for="productos" :value="__('Productos')" />
                            <select id="productos" name="productos[]" class="block mt-1 w-full" multiple>
                                @foreach ($productos as $producto)
                                    <option value="{{ $producto->id }}" {{ in_array($producto->id, $tratamiento->productos->pluck('id')->toArray()) ? 'selected' : '' }}>
                                        {{ $producto->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('productos')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-4">
                                {{ __('Actualizar') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
