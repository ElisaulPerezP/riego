<!-- resources/views/cosechas/qrs/create.blade.php -->

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear QR para Cosecha #') }}{{ $cosecha->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <!-- ... -->
        <div class="p-6 text-gray-900">
            <form method="POST" action="{{ route('qrs.store') }}">
                @csrf

                <!-- Campo oculto para cosecha_id -->
                <input type="hidden" name="cosecha_id" value="{{ $cosecha->id }}">

                <!-- Mostrar información de la cosecha -->
                <div>
                    <p><strong>{{ __('Fecha de Cosecha:') }}</strong> {{ $cosecha->fecha }}</p>
                    <p><strong>{{ __('Cantidad:') }}</strong> {{ $cosecha->cantidad }}</p>
                    <!-- Otros campos de la cosecha si es necesario -->
                </div>

                <!-- QR 125 -->
                <div class="mt-4">
                    <x-input-label for="qr125" :value="__('QR 125')" />
                    <x-text-input id="qr125" class="block mt-1 w-full" type="text" name="qr125" value="{{ old('qr125') }}" />
                    <x-input-error :messages="$errors->get('qr125')" class="mt-2" />
                </div>

                <!-- Repite para QR 250 y QR 500 -->
                <!-- ... -->

                <div class="flex items-center justify-end mt-4">
                    <x-primary-button class="ml-4">
                        {{ __('Crear') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
        <!-- ... -->
    </div>
</x-app-layout>
