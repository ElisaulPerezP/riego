<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear Asperción') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Formulario para crear asperción -->
                    <form method="POST" action="{{ route('aspercion.store') }}">
                        @csrf

                        <!-- Fecha -->
                        <div>
                            <x-input-label for="fecha" :value="__('Fecha')" />
                            <x-text-input id="fecha" class="block mt-1 w-full" type="date" name="fecha" :value="old('fecha')" required autofocus />
                            <x-input-error :messages="$errors->get('fecha')" class="mt-2" />
                        </div>

                        <!-- Hora -->
                        <div class="mt-4">
                            <x-input-label for="hora" :value="__('Hora')" />
                            <x-text-input id="hora" class="block mt-1 w-full" type="time" name="hora" :value="old('hora')" required />
                            <x-input-error :messages="$errors->get('hora')" class="mt-2" />
                        </div>

                        <!-- Volumen -->
                        <div class="mt-4">
                            <x-input-label for="volumen" :value="__('Volumen')" />
                            <x-text-input id="volumen" class="block mt-1 w-full" type="number" step="0.01" name="volumen" :value="old('volumen')" required />
                            <x-input-error :messages="$errors->get('volumen')" class="mt-2" />
                        </div>

                        <!-- Tipo de Asperción -->
                        <div class="mt-4">
                            <x-input-label for="tipo_aspercion" :value="__('Tipo de Asperción')" />
                            <x-text-input id="tipo_aspercion" class="block mt-1 w-full" type="text" name="tipo_aspercion" :value="old('tipo_aspercion')" required />
                            <x-input-error :messages="$errors->get('tipo_aspercion')" class="mt-2" />
                        </div>

                        <!-- Responsable -->
                        <div class="mt-4">
                            <x-input-label for="responsable" :value="__('Responsable')" />
                            <x-text-input id="responsable" class="block mt-1 w-full" type="text" name="responsable" :value="old('responsable')" required />
                            <x-input-error :messages="$errors->get('responsable')" class="mt-2" />
                        </div>

                        <!-- Usuario Responsable -->
                        <div class="mt-4">
                            <x-input-label for="user_id" :value="__('Usuario Responsable')" />
                            <select id="user_id" name="user_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                                <option value="">{{ __('Selecciona un usuario') }}</option>
                                @foreach($usuarios as $usuario)
                                    <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
                        </div>

                        <!-- Productos -->
                        <div class="mt-4">
                            <x-input-label for="productos" :value="__('Productos')" />
                            <select id="productos" name="productos[]" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" multiple required>
                                @foreach($productos as $producto)
                                    <option value="{{ $producto->id }}">{{ $producto->nombre }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('productos')" class="mt-2" />
                        </div>

                        <!-- Botón de Enviar -->
                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ms-3">
                                {{ __('Crear') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
