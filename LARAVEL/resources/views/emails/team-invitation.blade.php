@component('mail::message')
{{ __('Você foi convidado para entrar na equipe :team!', ['team' => $invitation->team->name]) }}

@if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::registration()))
{{ __('Se você não tem uma conta, crie uma clicando no botão abaixo. Após criar a conta, aceite o convite para ingressar na equipe.') }}

@component('mail::button', ['url' => route('register')])
{{ __('Criar Conta') }}
@endcomponent

{{ __('Se você já tem uma conta, aceite este convite clicando no botão abaixo:') }}

@else
{{ __('Você pode aceitar este convite clicando no botão abaixo:') }}
@endif


@component('mail::button', ['url' => $acceptUrl])
{{ __('Aceitar Convite') }}
@endcomponent

{{ __('Se você não esperava receber um convite para esta equipe, pode descartar este e-mail.') }}
@endcomponent
