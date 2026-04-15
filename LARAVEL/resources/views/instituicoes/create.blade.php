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
                            <input type="text" name="contato" id="contato"
                                   inputmode="numeric"
                                   placeholder="(XX) XXXXX-XXXX"
                                   maxlength="15"
                                   class="w-full px-3 py-2 border border-gray-300 rounded @error('contato') border-red-500 @enderror"
                                   value="{{ old('contato') }}">
                            @error('contato')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="cnpj" class="block text-gray-700 text-sm font-bold mb-2">CNPJ</label>
                            <input type="text" name="cnpj" id="cnpj"
                                   inputmode="numeric"
                                   placeholder="00.000.000/0000-00"
                                   maxlength="18"
                                   class="w-full px-3 py-2 border border-gray-300 rounded @error('cnpj') border-red-500 @enderror"
                                   value="{{ old('cnpj') }}">
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

    @push('scripts')
    <script>
        // Máscara de CNPJ: 00.000.000/0000-00
        document.getElementById('cnpj').addEventListener('input', function () {
            let v = this.value.replace(/\D/g, '').substring(0, 14);
            if (v.length > 12) v = v.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{0,2})/, '$1.$2.$3/$4-$5');
            else if (v.length > 8) v = v.replace(/^(\d{2})(\d{3})(\d{3})(\d{0,4})/, '$1.$2.$3/$4');
            else if (v.length > 5) v = v.replace(/^(\d{2})(\d{3})(\d{0,3})/, '$1.$2.$3');
            else if (v.length > 2) v = v.replace(/^(\d{2})(\d{0,3})/, '$1.$2');
            this.value = v;
        });

        // Máscara de telefone: (XX) XXXX-XXXX ou (XX) XXXXX-XXXX
        document.getElementById('contato').addEventListener('input', function () {
            let v = this.value.replace(/\D/g, '').substring(0, 11);
            if (v.length > 10) v = v.replace(/^(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
            else if (v.length > 6) v = v.replace(/^(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
            else if (v.length > 2) v = v.replace(/^(\d{2})(\d{0,5})/, '($1) $2');
            else if (v.length > 0) v = v.replace(/^(\d{0,2})/, '($1');
            this.value = v;
        });
    </script>
    @endpush
</x-app-layout>
