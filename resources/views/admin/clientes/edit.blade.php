<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Cliente') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('admin.clientes.update', $cliente->id) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="tipo_documento" :value="__('Tipo Documento')" />
                                <select name="tipo_documento" id="tipo_documento" class="block mt-1 w-full rounded border-gray-300">
                                    <option value="DNI" {{ $cliente->tipo_documento == 'DNI' ? 'selected' : '' }}>DNI</option>
                                    <option value="RUC" {{ $cliente->tipo_documento == 'RUC' ? 'selected' : '' }}>RUC</option>
                                    <option value="SIN_DNI" {{ $cliente->tipo_documento == 'SIN_DNI' ? 'selected' : '' }}>Sin DNI</option>
                                </select>
                                <x-input-error :messages="$errors->get('tipo_documento')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="num_documento" :value="__('Número de Documento')" />
                                <x-text-input id="num_documento" class="block mt-1 w-full" type="text" name="num_documento" :value="old('num_documento', $cliente->num_documento)" required />
                                <x-input-error :messages="$errors->get('num_documento')" class="mt-2" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="razon_social" :value="__('Nombre o Razón Social')" />
                            <x-text-input id="razon_social" class="block mt-1 w-full" type="text" name="razon_social" :value="old('razon_social', $cliente->razon_social)" required />
                            <x-input-error :messages="$errors->get('razon_social')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="email" :value="__('Email')" />
                                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $cliente->email)" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="telefono" :value="__('Teléfono')" />
                                <x-text-input id="telefono" class="block mt-1 w-full" type="text" name="telefono" :value="old('telefono', $cliente->telefono)" />
                                <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.dashboard') }}" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-4">
                                Cancelar
                            </a>
                            <x-primary-button class="ml-4">
                                {{ __('Actualizar Cliente') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>