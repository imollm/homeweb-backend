<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SalePolicy extends Policy
{
    use HandlesAuthorization;

    public function create(User $user): bool
    {
        $authRoles = ['admin', 'employee'];

        return $this->isAuthorizedToDoThisAction($user->role->name, $authRoles);
    }

    public function index(User $user): bool
    {
        $authRoles = ['admin', 'employee', 'customer', 'owner'];

        return $this->isAuthorizedToDoThisAction($user->role->name, $authRoles);
    }

    public function showByHashId(User $user): bool
    {
        $authRoles = ['admin', 'employee', 'customer', 'owner'];

        return $this->isAuthorizedToDoThisAction($user->role->name, $authRoles);
    }

    public function getSalesOfActualYear(User $user): bool
    {
        $authRoles = ['admin', 'employee'];

        return $this->isAuthorizedToDoThisAction($user->role->name, $authRoles);
    }

    public function salesBy(User $user): bool
    {
        $authRoles = ['admin', 'employee'];

        return $this->isAuthorizedToDoThisAction($user->role->name, $authRoles);
    }

    public function update(User $user): bool
    {
        $authRoles = ['admin', 'employee'];

        return $this->isAuthorizedToDoThisAction($user->role->name, $authRoles);
    }

    public function getSalesBySeller(User $user): bool
    {
        $authRoles = ['employee'];

        return $this->isAuthorizedToDoThisAction($user->role->name, $authRoles);
    }
}
