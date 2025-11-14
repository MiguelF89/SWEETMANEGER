<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Venda') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('vendas.update', $venda->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="instituicao_id" class="block text-gray-700 text-sm font-bold mb-2">Instituição</label>
                            <select name="instituicao_id" id="instituicao_id" class="w-full px-3 py-2 border border-gray-300 rounded @error('instituicao_id') border-red-500 @enderror">
                                <option value="">Selecione uma instituição</option>
                                @foreach ($instituicoes as $instituicao)
                                    <option value="{{ $instituicao->id }}" {{ old('instituicao_id', $venda->instituicao_id) == $instituicao->id ? 'selected' : '' }}>
                                        {{ $instituicao->nome }}
                                    </option>
                                @endforeach
                            </select>
                            @error('instituicao_id')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="produto_id" class="block text-gray-700 text-sm font-bold mb-2">Produto</label>
                            <select name="produto_id" id="produto_id" class="w-full px-3 py-2 border border-gray-300 rounded @error('produto_id') border-red-500 @enderror">
                                <option value="">Selecione um produto</option>
                                @foreach ($produtos as $produto)
                                    <option value="{{ $produto->id }}" {{ old('produto_id', $venda->produto_id) == $produto->id ? 'selected' : '' }}>
                                        {{ $produto->nome }} - R$ {{ number_format($produto->preco, 2, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>
                            @error('produto_id')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="quantidade" class="block text-gray-700 text-sm font-bold mb-2">Quantidade</label>
                            <input type="number" name="quantidade" id="quantidade" class="w-full px-3 py-2 border border-gray-300 rounded @error('quantidade') border-red-500 @enderror" value="{{ old('quantidade', $venda->quantidade) }}" min="1">
                            @error('quantidade')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Atualizar
                            </button>
                            <a href="{{ route('vendas.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
