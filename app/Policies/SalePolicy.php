<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SalePolicy extends Policy
{
    use HandlesAuthorization;

    public function store(User $user): bool
    {
        $authRoles = ['admin', 'employee'];

        return $this->isAuthorizedToDoThisAction($user->role->name, $authRoles);
    }
}
