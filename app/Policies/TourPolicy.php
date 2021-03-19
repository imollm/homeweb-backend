<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TourPolicy extends Policy
{
    use HandlesAuthorization;

    public function index(User $user): bool
    {
        $authRoles = ['admin'];

        return $this->isAuthorizedToDoThisAction($user->role->name, $authRoles);
    }

    public function store(User $user): bool
    {
        $authRoles = ['admin', 'employee', 'customer'];

        return $this->isAuthorizedToDoThisAction($user->role->name, $authRoles);
    }

    public function show(User $user): bool
    {
        $authRoles = ['admin', 'employee', 'owner', 'customer'];

        return $this->isAuthorizedToDoThisAction($user->role->name, $authRoles);
    }

    public function showByPropertyId(User $user): bool
    {
        $authRoles = ['admin', 'employee', 'customer', 'owner'];

        return $this->isAuthorizedToDoThisAction($user->role->name, $authRoles);
    }

    public function showByHashId(User $user): bool
    {
        $authRoles = ['admin', 'employee', 'customer', 'owner'];

        return $this->isAuthorizedToDoThisAction($user->role->name, $authRoles);
    }

    public function update(User $user): bool
    {
        $authRoles = ['admin', 'employee', 'customer'];

        return $this->isAuthorizedToDoThisAction($user->role->name, $authRoles);
    }

    public function destroy(User $user): bool
    {
        $authRoles = ['admin', 'employee', 'customer'];

        return $this->isAuthorizedToDoThisAction($user->role->name, $authRoles);
    }
}
