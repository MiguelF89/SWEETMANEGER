<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Criar Instituição') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('instituicoes.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="nome" class="block text-gray-700 text-sm font-bold mb-2">Nome</label>
                            <input type="text" name="nome" id="nome" class="w-full px-3 py-2 border border-gray-300 rounded @error('nome') border-red-500 @enderror" value="{{ old('nome') }}">
                            @error('nome')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="contato" class="block text-gray-700 text-sm font-bold mb-2">Contato</label>
                            <input type="text" name="contato" id="contato" class="w-full px-3 py-2 border border-gray-300 rounded @error('contato') border-red-500 @enderror" value="{{ old('contato') }}">
                            @error('contato')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="cnpj" class="block text-gray-700 text-sm font-bold mb-2">CNPJ</label>
                            <input type="text" name="cnpj" id="cnpj" class="w-full px-3 py-2 border border-gray-300 rounded @error('cnpj') border-red-500 @enderror" value="{{ old('cnpj') }}">
                            @error('cnpj')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Salvar
                            </button>
                            <a href="{{ route('instituicoes.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
