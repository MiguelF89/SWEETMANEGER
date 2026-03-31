<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Produto
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
                            <label for="nome" class="block text-gray-700 text-sm font-bold mb-2">Nome do Produto</label>
                            <input type="text" name="nome" id="nome"
                                class="w-full px-3 py-2 border border-gray-300 rounded @error('nome') border-red-500 @enderror"
                                value="{{ old('nome', $produto->nome) }}">
                            @error('nome')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="preco" class="block text-gray-700 text-sm font-bold mb-2">Preço</label>
                            <div class="flex items-center border border-gray-300 rounded @error('preco') border-red-500 @enderror overflow-hidden">
                                <span class="bg-gray-100 border-r border-gray-300 px-3 py-2 text-gray-600 font-semibold select-none">R$</span>
                                <input type="number" name="preco" id="preco" step="0.01" min="0"
                                    class="flex-1 px-3 py-2 focus:outline-none"
                                    value="{{ old('preco', $produto->preco) }}"
                                    oninput="atualizarPreview(this.value)">
                            </div>
                            <p class="mt-2 text-sm text-gray-500">
                                Valor formatado: <span id="preco-preview" class="font-semibold text-green-600">R$ 0,00</span>
                            </p>
                            @error('preco')
                                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
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

    <script>
        function atualizarPreview(valor) {
            const num = parseFloat(valor);
            const el = document.getElementById('preco-preview');
            if (isNaN(num) || valor === '') {
                el.textContent = 'R$ 0,00';
            } else {
                el.textContent = num.toLocaleString('pt-BR', {
                    style: 'currency',
                    currency: 'BRL'
                });
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('preco');
            if (input.value) atualizarPreview(input.value);
        });
    </script>
</x-app-layout>
