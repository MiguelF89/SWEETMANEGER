<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detalhes da Encomenda</h2>
                <p class="text-sm text-gray-600">Veja todas as informações da encomenda.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('encomendas.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition">Lista</a>
                <a href="{{ route('encomendas.edit', $encomenda) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">Editar</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-6">
                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 uppercase">Cliente</h3>
                        <p class="mt-2 text-lg font-medium text-gray-900">{{ $encomenda->cliente }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 uppercase">Data de entrega</h3>
                        <p class="mt-2 text-lg font-medium text-gray-900">{{ $encomenda->data_entrega->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 uppercase">Horário de entrega</h3>
                        <p class="mt-2 text-lg font-medium text-gray-900">{{ $encomenda->horario_entrega ?? 'Não informado' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 uppercase">Valor</h3>
                        <p class="mt-2 text-lg font-medium text-gray-900">R$ {{ number_format($encomenda->valor, 2, ',', '.') }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 uppercase">Quantidade</h3>
                        <p class="mt-2 text-lg font-medium text-gray-900">{{ $encomenda->quantidade }}</p>
                    </div>
                </div>

                <div class="grid gap-6 md:grid-cols-3">
                    <div class="rounded-lg bg-gray-50 p-4">
                        <p class="text-sm font-semibold text-gray-500 uppercase">Pago</p>
                        <p class="mt-2 text-lg font-medium {{ $encomenda->pago ? 'text-green-700' : 'text-red-700' }}">{{ $encomenda->pago ? 'Sim' : 'Não' }}</p>
                    </div>
                    <div class="rounded-lg bg-gray-50 p-4">
                        <p class="text-sm font-semibold text-gray-500 uppercase">Repassado</p>
                        <p class="mt-2 text-lg font-medium {{ $encomenda->repassado_cliente ? 'text-blue-700' : 'text-gray-700' }}">{{ $encomenda->repassado_cliente ? 'Sim' : 'Não' }}</p>
                    </div>
                    <div class="rounded-lg bg-gray-50 p-4">
                        <p class="text-sm font-semibold text-gray-500 uppercase">Status</p>
                        <p class="mt-2 inline-flex items-center rounded-full px-3 py-1 text-sm font-medium {{ $encomenda->pago ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $encomenda->pago ? 'Pago' : 'Aguardando' }}
                        </p>
                    </div>
                </div>

                @if($encomenda->link_pagamento)
                    <div class="rounded-lg bg-white border border-gray-200 p-4">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase">Link de Pagamento</h3>
                        <a href="{{ $encomenda->link_pagamento }}" target="_blank" rel="noopener noreferrer" class="mt-2 block text-blue-600 hover:text-blue-800">Abrir link</a>
                    </div>
                @endif

                <div class="rounded-lg bg-white border border-gray-200 p-4">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase">Observações</h3>
                    <p class="mt-2 text-gray-700">{{ $encomenda->observacoes ?: 'Nenhuma observação registrada.' }}</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
