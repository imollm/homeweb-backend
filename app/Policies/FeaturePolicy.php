<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FeaturePolicy extends Policy
{
    use HandlesAuthorization;

    public function store(User $user): bool
    {
        $authRoles = ['admin'];

        return $this->isAuthorizedToDoThisAction($user->role->name, $authRoles);
    }

    public function update(User $user): bool
    {
        $authRoles = ['admin'];

        return $this->isAuthorizedToDoThisAction($user->role->name, $authRoles);
    }

    public function show(User $user): bool
    {
        $authRoles = ['admin'];

        return $this->isAuthorizedToDoThisAction($user->role->name, $authRoles);
    }

    public function destroy(User $user): bool
    {
        $authRoles = ['admin'];

        return $this->isAuthorizedToDoThisAction($user->role->name, $authRoles);
    }
}
