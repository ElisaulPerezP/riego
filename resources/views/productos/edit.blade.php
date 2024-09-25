<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Producto') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('productos.update', $producto) }}">
                        @csrf
                        @method('PUT')

                        <!-- Nombre -->
                        <div>
                            <x-input-label for="nombre" :value="__('Nombre')" />
                            <x-text-input id="nombre" class="block mt-1 w-full" type="text" name="nombre" value="{{ $producto->nombre }}" required />
                            <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                        </div>

                        <!-- Descripción -->
                        <div class="mt-4">
                            <x-input-label for="descripcion" :value="__('Descripción')" />
                            <textarea id="descripcion" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="descripcion" required>{{ $producto->descripcion }}</textarea>
                            <x-input-error :messages="$errors->get('descripcion')" class="mt-2" />
                        </div>

                        <!-- Precio -->
                        <div class="mt-4">
                            <x-input-label for="precio" :value="__('Precio')" />
                            <x-text-input id="precio" class="block mt-1 w-full" type="number" step="0.01" name="precio" value="{{ $producto->precio }}" required />
                            <x-input-error :messages="$errors->get('precio')" class="mt-2" />
                        </div>

                        <!-- Cantidad -->
                        <div class="mt-4">
                            <x-input-label for="cantidad" :value="__('Cantidad')" />
                            <x-text-input id="cantidad" class="block mt-1 w-full" type="number" name="cantidad" value="{{ $producto->cantidad }}" required />
                            <x-input-error :messages="$errors->get('cantidad')" class="mt-2" />
                        </div>

                        <!-- Fecha de Vencimiento -->
                        <div class="mt-4">
                            <x-input-label for="fecha_vencimiento" :value="__('Fecha de Vencimiento')" />
                            <x-text-input id="fecha_vencimiento" class="block mt-1 w-full" type="date" name="fecha_vencimiento" value="{{ $producto->fecha_vencimiento }}" required />
                            <x-input-error :messages="$errors->get('fecha_vencimiento')" class="mt-2" />
                        </div>

                        <!-- Responsable -->
                        <div class="mt-4">
                            <x-input-label for="responsable" :value="__('Responsable')" />
                            <x-text-input id="responsable" class="block mt-1 w-full" type="text" name="responsable" value="{{ $producto->responsable }}" required />
                            <x-input-error :messages="$errors->get('responsable')" class="mt-2" />
                        </div>

                        <!-- Teléfono de Emergencia -->
                        <div class="mt-4">
                            <x-input-label for="telefono_emergencia" :value="__('Teléfono de Emergencia')" />
                            <x-text-input id="telefono_emergencia" class="block mt-1 w-full" type="text" name="telefono_emergencia" value="{{ $producto->telefono_emergencia }}" />
                            <x-input-error :messages="$errors->get('telefono_emergencia')" class="mt-2" />
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
