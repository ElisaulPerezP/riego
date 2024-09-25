<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar QR') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('qrs.update', $qr) }}">
                        @csrf
                        @method('PUT')

                        <!-- Cosecha ID -->
                        <div>
                            <x-input-label for="cosecha_id" :value="__('Cosecha ID')" />
                            <select id="cosecha_id" name="cosecha_id" class="block mt-1 w-full">
                                @foreach($cosechas as $cosecha)
                                    <option value="{{ $cosecha->id }}" {{ old('cosecha_id', $qr->cosecha_id) == $cosecha->id ? 'selected' : '' }}>
                                        {{ $cosecha->id }} - {{ $cosecha->fecha }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('cosecha_id')" class="mt-2" />
                        </div>

                        <!-- QR 125 -->
                        <div class="mt-4">
                            <x-input-label for="qr125" :value="__('QR 125')" />
                            <x-text-input id="qr125" class="block mt-1 w-full" type="text" name="qr125" value="{{ old('qr125', $qr->qr125) }}" />
                            <x-input-error :messages="$errors->get('qr125')" class="mt-2" />
                        </div>

                        <!-- QR 250 -->
                        <div class="mt-4">
                            <x-input-label for="qr250" :value="__('QR 250')" />
                            <x-text-input id="qr250" class="block mt-1 w-full" type="text" name="qr250" value="{{ old('qr250', $qr->qr250) }}" />
                            <x-input-error :messages="$errors->get('qr250')" class="mt-2" />
                        </div>

                        <!-- QR 500 -->
                        <div class="mt-4">
                            <x-input-label for="qr500" :value="__('QR 500')" />
                            <x-text-input id="qr500" class="block mt-1 w-full" type="text" name="qr500" value="{{ old('qr500', $qr->qr500) }}" />
                            <x-input-error :messages="$errors->get('qr500')" class="mt-2" />
                        </div>

                        <!-- UUID 125 -->
                        <div class="mt-4">
                            <x-input-label for="uuid125" :value="__('UUID 125')" />
                            <x-text-input id="uuid125" class="block mt-1 w-full" type="text" name="uuid125[]" value="{{ old('uuid125', json_encode($qr->uuid125)) }}" />
                            <x-input-error :messages="$errors->get('uuid125')" class="mt-2" />
                        </div>

                        <!-- UUID 250 -->
                        <div class="mt-4">
                            <x-input-label for="uuid250" :value="__('UUID 250')" />
                            <x-text-input id="uuid250" class="block mt-1 w-full" type="text" name="uuid250[]" value="{{ old('uuid250', json_encode($qr->uuid250)) }}" />
                            <x-input-error :messages="$errors->get('uuid250')" class="mt-2" />
                        </div>

                        <!-- UUID 500 -->
                        <div class="mt-4">
                            <x-input-label for="uuid500" :value="__('UUID 500')" />
                            <x-text-input id="uuid500" class="block mt-1 w-full" type="text" name="uuid500[]" value="{{ old('uuid500', json_encode($qr->uuid500)) }}" />
                            <x-input-error :messages="$errors->get('uuid500')" class="mt-2" />
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
