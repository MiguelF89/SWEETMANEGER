<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Instituições') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <a href="{{ route('instituicoes.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Criar Instituição
                        </a>
                    </div>

                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    <table class="w-full border-collapse border border-gray-300">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border border-gray-300 px-4 py-2">ID</th>
                                <th class="border border-gray-300 px-4 py-2">Nome</th>
                                <th class="border border-gray-300 px-4 py-2">Contato</th>
                                <th class="border border-gray-300 px-4 py-2">CNPJ</th>
                                <th class="border border-gray-300 px-4 py-2">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($instituicoes as $instituicao)
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2">{{ $instituicao->id }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $instituicao->nome }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $instituicao->contato }}</td>
                                    <td class="border border-gray-300 px-4 py-2">{{ $instituicao->cnpj }}</td>
                                    <td class="border border-gray-300 px-4 py-2">
                                        <a href="{{ route('instituicoes.edit', $instituicao->id) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-3 rounded text-sm">
                                            Editar
                                        </a>
                                        <form action="{{ route('instituicoes.destroy', $instituicao->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm" onclick="return confirm('Tem certeza?')">
                                                Deletar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $instituicoes->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
