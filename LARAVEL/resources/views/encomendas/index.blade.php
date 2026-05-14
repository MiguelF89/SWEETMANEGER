<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Encomendas</h2>
                <p class="text-sm text-gray-600">Visualize e gerencie suas encomendas.</p>
            </div>
            <a href="{{ route('encomendas.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                Nova Encomenda
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="rounded-lg bg-green-50 border border-green-200 p-4 text-sm text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="GET" action="{{ route('encomendas.index') }}" class="grid gap-4 md:grid-cols-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Busca</label>
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cliente, descrição" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Todos</option>
                            <option value="pago" {{ ($status ?? '') === 'pago' ? 'selected' : '' }}>Pago</option>
                            <option value="pendente" {{ ($status ?? '') === 'pendente' ? 'selected' : '' }}>Pendente</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Repassado</label>
                        <select name="repassado" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Todos</option>
                            <option value="sim" {{ ($repassado ?? '') === 'sim' ? 'selected' : '' }}>Sim</option>
                            <option value="nao" {{ ($repassado ?? '') === 'nao' ? 'selected' : '' }}>Não</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Ordenar por entrega</label>
                        <select name="sort" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="asc" {{ ($sort ?? 'asc') === 'asc' ? 'selected' : '' }}>Mais antigas primeiro</option>
                            <option value="desc" {{ ($sort ?? '') === 'desc' ? 'selected' : '' }}>Mais próximas primeiro</option>
                        </select>
                    </div>

                    <div class="md:col-span-4 flex justify-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-slate-600 text-white rounded-md hover:bg-slate-700 transition">
                            Atualizar
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Cliente</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Valor</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Data entrega</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Horário</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pago</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Repassado</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($encomendas as $encomenda)
                            <tr>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ $encomenda->cliente }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">R$ {{ number_format($encomenda->valor, 2, ',', '.') }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ $encomenda->data_entrega->format('d/m/Y') }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ $encomenda->horario_entrega ?? '—' }}</td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $encomenda->pago ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $encomenda->pago ? 'Pago' : 'Pendente' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $encomenda->repassado_cliente ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $encomenda->repassado_cliente ? 'Sim' : 'Não' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($encomenda->pago && $encomenda->repassado_cliente)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-green-100 text-green-800">Concluída</span>
                                    @elseif($encomenda->pago)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-yellow-100 text-yellow-800">Pago</span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-red-100 text-red-800">Aguardando</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <a href="{{ route('encomendas.show', $encomenda) }}" class="text-blue-600 hover:text-blue-900">Visualizar</a>
                                    <a href="{{ route('encomendas.edit', $encomenda) }}" class="text-indigo-600 hover:text-indigo-900">Editar</a>
                                    <form action="{{ route('encomendas.destroy', $encomenda) }}" method="POST" class="inline-block" onsubmit="return confirm('Remover encomenda?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Excluir</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-4 text-center text-sm text-gray-500">Nenhuma encomenda encontrada.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                {{ $encomendas->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
