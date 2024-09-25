<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Listado de Cosechas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <x-primary-button class="mb-4">
                        <a href="{{ route('cosecha.create') }}" class="text-white">
                            {{ __('Crear nueva Cosecha') }}
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
                                <th class="px-4 py-2 border">Fecha</th>
                                <th class="px-4 py-2 border">Cantidad</th>
                                <th class="px-4 py-2 border">Porcentaje</th>
                                <th class="px-4 py-2 border">Cajas 125g</th>
                                <th class="px-4 py-2 border">Cajas 250g</th>
                                <th class="px-4 py-2 border">Cajas 500g</th>
                                <th class="px-4 py-2 border">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cosechas as $cosecha)
                                <tr>
                                    <td class="px-4 py-2 border">{{ $cosecha->fecha }}</td>
                                    <td class="px-4 py-2 border">{{ $cosecha->cantidad }}</td>
                                    <td class="px-4 py-2 border">{{ $cosecha->porcentaje }}%</td>
                                    <td class="px-4 py-2 border">{{ $cosecha->cajas125 }}</td>
                                    <td class="px-4 py-2 border">{{ $cosecha->cajas250 }}</td>
                                    <td class="px-4 py-2 border">{{ $cosecha->cajas500 }}</td>
                                    <td class="px-4 py-2 border">
                                        <x-secondary-button class="mr-2">
                                            <a href="{{ route('cosecha.show', $cosecha) }}" class="text-black">{{ __('Ver') }}</a>
                                        </x-secondary-button>

                                        <x-primary-button class="mr-2">
                                            <a href="{{ route('cosecha.edit', $cosecha) }}" class="text-white">{{ __('Editar') }}</a>
                                        </x-primary-button>

                                        <form action="{{ route('cosecha.destroy', $cosecha) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <x-danger-button onclick="return confirm('¿Estás seguro de eliminar esta cosecha?')">
                                                {{ __('Eliminar') }}
                                            </x-danger-button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
