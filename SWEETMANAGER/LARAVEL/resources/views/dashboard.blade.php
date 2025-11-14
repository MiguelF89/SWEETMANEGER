<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Botões de Ação Rápida -->
            <div class="mb-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('instituicoes.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-4 px-6 rounded text-center">
                    Criar Instituição
                </a>
                <a href="{{ route('produtos.create') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-4 px-6 rounded text-center">
                    Criar Produto
                </a>
                <a href="{{ route('vendas.create') }}" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-4 px-6 rounded text-center">
                    Criar Venda
                </a>
            </div>

            <!-- Últimas Instituições -->
            <div class="mb-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Últimas Instituições</h3>
                    <table class="w-full border-collapse border border-gray-300">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border border-gray-300 px-4 py-2">ID</th>
                                <th class="border border-gray-300 px-4 py-2">Nome</th>
                                <th class="border border-gray-300 px-4 py-2">Contato</th>
                                <th class="border border-gray-300 px-4 py-2">CNPJ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($instituicoes as $instituicao)
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2">{{ $instituicao->id }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $instituicao->nome }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $instituicao->contato }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $instituicao->cnpj }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="border border-gray-300 px-4 py-2 text-center">Nenhuma instituição cadastrada</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4">
                        <a href="{{ route('instituicoes.index') }}" class="text-blue-500 hover:text-blue-700">Ver todas as instituições</a>
                    </div>
                </div>
            </div>

            <!-- Últimos Produtos -->
            <div class="mb-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Últimos Produtos</h3>
                    <table class="w-full border-collapse border border-gray-300">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border border-gray-300 px-4 py-2">ID</th>
                                <th class="border border-gray-300 px-4 py-2">Nome</th>
                                <th class="border border-gray-300 px-4 py-2">Preço</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($produtos as $produto)
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2">{{ $produto->id }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $produto->nome }}</td>
                                    <td class="border border-gray-300 px-4 py-2">R$ {{ number_format($produto->preco, 2, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="border border-gray-300 px-4 py-2 text-center">Nenhum produto cadastrado</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4">
                        <a href="{{ route('produtos.index') }}" class="text-blue-500 hover:text-blue-700">Ver todos os produtos</a>
                    </div>
                </div>
            </div>

            <!-- Últimas Vendas -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Últimas Vendas</h3>
                    <table class="w-full border-collapse border border-gray-300">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border border-gray-300 px-4 py-2">ID</th>
                                <th class="border border-gray-300 px-4 py-2">Instituição</th>
                                <th class="border border-gray-300 px-4 py-2">Produto</th>
                                <th class="border border-gray-300 px-4 py-2">Quantidade</th>
                                <th class="border border-gray-300 px-4 py-2">Valor Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($vendas as $venda)
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2">{{ $venda->id }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $venda->instituicao->nome }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $venda->produto->nome }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $venda->quantidade }}</td>
                                    <td class="border border-gray-300 px-4 py-2">R$ {{ number_format($venda->valor_total, 2, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="border border-gray-300 px-4 py-2 text-center">Nenhuma venda cadastrada</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4">
                        <a href="{{ route('vendas.index') }}" class="text-blue-500 hover:text-blue-700">Ver todas as vendas</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
