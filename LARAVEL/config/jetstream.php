<?php

use Laravel\Jetstream\Features;
use Laravel\Jetstream\Http\Middleware\AuthenticateSession;

return [

    'stack' => 'livewire',

    'middleware' => ['web'],

    'auth_session' => AuthenticateSession::class,

    'guard' => 'sanctum',

    'features' => [
        // Features::teams(['invitations' => true]),  // Times desativado
        Features::accountDeletion(),
    ],

    'profile_photo_disk' => 'public',

];
