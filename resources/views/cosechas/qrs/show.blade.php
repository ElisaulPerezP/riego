<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detalles del QR') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold">{{ __('Detalles del QR ID:') }} {{ $qr->id }}</h3>

                    <!-- Información básica del QR -->
                    <p><strong>{{ __('Cosecha ID:') }}</strong> {{ $qr->cosecha_id }}</p>

                    <!-- Códigos QR -->
                    <p><strong>{{ __('QR 125:') }}</strong> {{ $qr->qr125 }}</p>
                    <p><strong>{{ __('QR 250:') }}</strong> {{ $qr->qr250 }}</p>
                    <p><strong>{{ __('QR 500:') }}</strong> {{ $qr->qr500 }}</p>

                    <!-- UUIDs Asociados -->
                    <h4 class="text-md font-semibold mt-4">{{ __('UUIDs Asociados:') }}</h4>
                    <p><strong>{{ __('UUID 125:') }}</strong> {{ json_encode($qr->uuid125) }}</p>
                    <p><strong>{{ __('UUID 250:') }}</strong> {{ json_encode($qr->uuid250) }}</p>
                    <p><strong>{{ __('UUID 500:') }}</strong> {{ json_encode($qr->uuid500) }}</p>

                    <!-- Botón Volver al listado -->
                    <x-primary-button class="mt-4">
                        <a href="{{ route('cosecha.index') }}" class="text-white">{{ __('Volver al listado') }}</a>
                    </x-primary-button>

                    <!-- Botón Eliminar -->
                    <form action="{{ route('qrs.destroy', $qr->id) }}" method="POST" class="inline-block mt-4">
                        @csrf
                        @method('DELETE')
                        
                        <x-danger-button onclick="return confirm('¿Estás seguro de que deseas eliminar este QR?')">
                            {{ __('Eliminar') }}
                        </x-danger-button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
