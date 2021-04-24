<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CityPolicy extends Policy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        $authRoles = ['admin'];

        return $this->isAuthorizedToDoThisAction($user->role->name, $authRoles);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function update(User $user): bool
    {
        $authRoles = ['admin'];

        return $this->isAuthorizedToDoThisAction($user->role->name, $authRoles);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function destroy(User $user): bool
    {
        $authRoles = ['admin'];

        return $this->isAuthorizedToDoThisAction($user->role->name, $authRoles);
    }
}
