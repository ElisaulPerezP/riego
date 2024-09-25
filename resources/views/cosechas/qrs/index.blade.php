<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Listado de Códigos QR de la Cosecha') }} #{{ $cosecha_id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <x-primary-button class="mb-4">
                        <a href="{{ route('qrs.create') }}" class="text-white">
                            {{ __('Crear nuevo QR') }}
                        </a>
                    </x-primary-button>

                    @if (session('success'))
                        <div class="bg-green-500 text-white p-4 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <table class="table-auto w-full border-collapse border border-gray-300">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-4 py-2 border">ID</th>
                                <th class="px-4 py-2 border">QR 125</th>
                                <th class="px-4 py-2 border">QR 250</th>
                                <th class="px-4 py-2 border">QR 500</th>
                                <th class="px-4 py-2 border">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($qrs as $qr)
                                <tr>
                                    <td class="px-4 py-2 border">{{ $qr->id }}</td>
                                    <td class="px-4 py-2 border">{{ $qr->qr125 }}</td>
                                    <td class="px-4 py-2 border">{{ $qr->qr250 }}</td>
                                    <td class="px-4 py-2 border">{{ $qr->qr500 }}</td>
                                    <td class="px-4 py-2 border">
                                        @if ($hayCodigosQr)
                                            <x-secondary-button class="mr-2">
                                                <a href="{{ route('qrs.show', $qr->id) }}" class="text-black">{{ __('Ver') }}</a>
                                            </x-secondary-button>
                                        @endif

                                        <x-primary-button class="mr-2">
                                            <a href="{{ route('qrs.edit', $qr->id) }}" class="text-white">{{ __('Editar') }}</a>
                                        </x-primary-button>

                                        <form action="{{ route('qrs.destroy', $qr->id) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <x-danger-button onclick="return confirm('¿Estás seguro de eliminar este QR?')">
                                                {{ __('Eliminar') }}
                                            </x-danger-button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Botón Volver al listado de cosechas -->
                    <x-primary-button class="mt-4">
                        <a href="{{ route('cosecha.index') }}" class="text-white">{{ __('Volver al listado de Cosechas') }}</a>
                    </x-primary-button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
