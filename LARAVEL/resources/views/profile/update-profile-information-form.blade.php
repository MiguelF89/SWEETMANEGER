<x-form-section submit="updateProfileInformation">
    <x-slot name="title">
        Informações do Perfil
    </x-slot>

    <x-slot name="description">
        Atualize as informações de perfil e o endereço de e-mail da sua conta.
    </x-slot>

    <x-slot name="form">
        <!-- Nome -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="name" value="Nome" />
            <x-input id="name" type="text" class="mt-1 block w-full" wire:model="state.name" required autocomplete="name" />
            <x-input-error for="name" class="mt-2" />
        </div>

        <!-- E-mail -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="email" value="E-mail" />
            <x-input id="email" type="email" class="mt-1 block w-full" wire:model="state.email" required autocomplete="username" />
            <x-input-error for="email" class="mt-2" />
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-action-message class="me-3" on="saved">
            Salvo com sucesso.
        </x-action-message>

        <x-button wire:loading.attr="disabled">
            Salvar
        </x-button>
    </x-slot>
</x-form-section>
