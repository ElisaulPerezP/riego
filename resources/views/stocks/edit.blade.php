<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Stock') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('stocks.update', $stock) }}">
                        @csrf
                        @method('PUT')

                        <!-- Producto -->
                        <div>
                            <x-input-label for="producto_id" :value="__('Producto')" />
                            <select id="producto_id" name="producto_id" class="block mt-1 w-full">
                                @foreach ($productos as $producto)
                                    <option value="{{ $producto->id }}" {{ $producto->id == $stock->producto_id ? 'selected' : '' }}>
                                        {{ $producto->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('producto_id')" class="mt-2" />
                        </div>

                        <!-- Cantidad en Stock -->
                        <div class="mt-4">
                            <x-input-label for="cantidad_en_stock" :value="__('Cantidad en Stock')" />
                            <x-text-input id="cantidad_en_stock" class="block mt-1 w-full" type="number" name="cantidad_en_stock" value="{{ $stock->cantidad_en_stock }}" required />
                            <x-input-error :messages="$errors->get('cantidad_en_stock')" class="mt-2" />
                        </div>

                        <!-- Días para Vencimiento -->
                        <div class="mt-4">
                            <x-input-label for="dias_para_vencimiento" :value="__('Días para Vencimiento')" />
                            <x-text-input id="dias_para_vencimiento" class="block mt-1 w-full" type="number" name="dias_para_vencimiento" value="{{ $stock->dias_para_vencimiento }}" required />
                            <x-input-error :messages="$errors->get('dias_para_vencimiento')" class="mt-2" />
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
