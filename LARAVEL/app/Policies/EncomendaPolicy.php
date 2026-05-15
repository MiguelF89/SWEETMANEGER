<?php

namespace App\Policies;

use App\Models\Encomenda;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EncomendaPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Encomenda $encomenda)
    {
        return $user->id === $encomenda->user_id;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, Encomenda $encomenda)
    {
        return $user->id === $encomenda->user_id;
    }

    public function delete(User $user, Encomenda $encomenda)
    {
        return $user->id === $encomenda->user_id;
    }

    public function restore(User $user, Encomenda $encomenda)
    {
        return $user->id === $encomenda->user_id;
    }

    public function forceDelete(User $user, Encomenda $encomenda)
    {
        return $user->id === $encomenda->user_id;
    }
}
