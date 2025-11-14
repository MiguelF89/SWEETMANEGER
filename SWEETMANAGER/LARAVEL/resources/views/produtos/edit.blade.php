<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Produto') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('produtos.update', $produto->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="nome" class="block text-gray-700 text-sm font-bold mb-2">Nome</label>
                            <input type="text" name="nome" id="nome" class="w-full px-3 py-2 border border-gray-300 rounded @error('nome') border-red-500 @enderror" value="{{ old('nome', $produto->nome) }}">
                            @error('nome')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="preco" class="block text-gray-700 text-sm font-bold mb-2">Preço</label>
                            <input type="number" name="preco" id="preco" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded @error('preco') border-red-500 @enderror" value="{{ old('preco', $produto->preco) }}">
                            @error('preco')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Atualizar
                            </button>
                            <a href="{{ route('produtos.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
