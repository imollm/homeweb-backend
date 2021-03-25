<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class UserPolicy extends Policy
{
    use HandlesAuthorization;

    public function update(User $user): bool
    {
        $authRoles = ['admin', 'customer', 'employee', 'owner'];

        $authorized = $this->isAuthorizedToDoThisAction($user->role->name, $authRoles);

        $mySelf = auth()->id() === $user->id;

        return $authorized && $mySelf;
    }
}
