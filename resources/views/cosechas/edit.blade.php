<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Cosecha') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('cosecha.update', $cosecha) }}">
                        @csrf
                        @method('PUT')

                        <!-- Fecha -->
                        <div>
                            <x-input-label for="fecha" :value="__('Fecha')" />
                            <x-text-input id="fecha" class="block mt-1 w-full" type="date" name="fecha" value="{{ old('fecha', $cosecha->fecha) }}" required />
                            <x-input-error :messages="$errors->get('fecha')" class="mt-2" />
                        </div>

                        <!-- Cantidad -->
                        <div class="mt-4">
                            <x-input-label for="cantidad" :value="__('Cantidad')" />
                            <x-text-input id="cantidad" class="block mt-1 w-full" type="number" step="0.01" name="cantidad" value="{{ old('cantidad', $cosecha->cantidad) }}" required />
                            <x-input-error :messages="$errors->get('cantidad')" class="mt-2" />
                        </div>

                        <!-- Porcentaje -->
                        <div class="mt-4">
                            <x-input-label for="porcentaje" :value="__('Porcentaje')" />
                            <x-text-input id="porcentaje" class="block mt-1 w-full" type="number" step="0.01" name="porcentaje" value="{{ old('porcentaje', $cosecha->porcentaje) }}" required />
                            <x-input-error :messages="$errors->get('porcentaje')" class="mt-2" />
                        </div>

                        <!-- Cajas 125 -->
                        <div class="mt-4">
                            <x-input-label for="cajas125" :value="__('Cajas 125g')" />
                            <x-text-input id="cajas125" class="block mt-1 w-full" type="number" name="cajas125" value="{{ old('cajas125', $cosecha->cajas125) }}" required />
                            <x-input-error :messages="$errors->get('cajas125')" class="mt-2" />
                        </div>

                        <!-- Cajas 250 -->
                        <div class="mt-4">
                            <x-input-label for="cajas250" :value="__('Cajas 250g')" />
                            <x-text-input id="cajas250" class="block mt-1 w-full" type="number" name="cajas250" value="{{ old('cajas250', $cosecha->cajas250) }}" required />
                            <x-input-error :messages="$errors->get('cajas250')" class="mt-2" />
                        </div>

                        <!-- Cajas 500 -->
                        <div class="mt-4">
                            <x-input-label for="cajas500" :value="__('Cajas 500g')" />
                            <x-text-input id="cajas500" class="block mt-1 w-full" type="number" name="cajas500" value="{{ old('cajas500', $cosecha->cajas500) }}" required />
                            <x-input-error :messages="$errors->get('cajas500')" class="mt-2" />
                        </div>

                        <!-- Usuario Responsable -->
                        <div class="mt-4">
                            <x-input-label for="user_id" :value="__('Responsable')" />
                            <select id="user_id" name="user_id" class="block mt-1 w-full">
                                @foreach($usuarios as $usuario)
                                    <option value="{{ $usuario->id }}" {{ old('user_id', $cosecha->user_id) == $usuario->id ? 'selected' : '' }}>
                                        {{ $usuario->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
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
