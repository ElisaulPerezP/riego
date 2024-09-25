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
                    <h4 class="text-md font-semibold mt-4">{{ __('Descarga de Imágenes QR:') }}</h4>
                    
                    @if($qr->qr125)
                        <p>
                            <strong>{{ __('QR 125g:') }}</strong>
                            <a href="{{ config('app.nurse_url') }}/{{ $qr->qr125 }}" download>
                                {{ __('Descargar QR 125g') }}
                            </a>
                        </p>
                    @endif

                    @if($qr->qr250)
                        <p>
                            <strong>{{ __('QR 250g:') }}</strong>
                            <a href="{{ config('app.nurse_url') }}/{{ $qr->qr250 }}" download>
                                {{ __('Descargar QR 250g') }}
                            </a>
                        </p>
                    @endif

                    @if($qr->qr500)
                        <p>
                            <strong>{{ __('QR 500g:') }}</strong>
                            <a href="{{ config('app.nurse_url') }}/{{ $qr->qr500 }}" download>
                                {{ __('Descargar QR 500g') }}
                            </a>
                        </p>
                    @endif

                    <!-- UUIDs Asociados -->
                    <h4 class="text-md font-semibold mt-4">{{ __('UUIDs Asociados:') }}</h4>
                    <p><strong>{{ __('UUID 125g:') }}</strong> {{ $qr->uuid125 }}</p>
                    <p><strong>{{ __('UUID 250g:') }}</strong> {{ $qr->uuid250 }}</p>
                    <p><strong>{{ __('UUID 500g:') }}</strong> {{ $qr->uuid500 }}</p>

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
