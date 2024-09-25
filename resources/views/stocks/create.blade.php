<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear Stock') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- Botón para crear un nuevo producto -->
                    <div class="mb-4">
                        <x-primary-button>
                            <a href="{{ route('productos.create') }}">
                                {{ __('Crear nuevo Producto') }}
                            </a>
                        </x-primary-button>
                    </div>

                    <!-- Formulario para crear Stock -->
                    <form method="POST" action="{{ route('stocks.store') }}">
                        @csrf

                        <!-- Producto -->
                        <div>
                            <x-input-label for="producto_id" :value="__('Producto')" />
                            <select id="producto_id" name="producto_id" class="block mt-1 w-full">
                                @foreach ($productos as $producto)
                                    <option value="{{ $producto->id }}">{{ $producto->nombre }} (Vence: {{ $producto->fecha_vencimiento }})</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('producto_id')" class="mt-2" />
                        </div>

                        <!-- Cantidad en Stock -->
                        <div class="mt-4">
                            <x-input-label for="cantidad_en_stock" :value="__('Cantidad en Stock')" />
                            <x-text-input id="cantidad_en_stock" class="block mt-1 w-full" type="number" name="cantidad_en_stock" value="{{ old('cantidad_en_stock') }}" required />
                            <x-input-error :messages="$errors->get('cantidad_en_stock')" class="mt-2" />
                        </div>

                        <!-- Botón para crear el Stock -->
                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-4">
                                {{ __('Crear') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
