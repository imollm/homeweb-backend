<?php

namespace App\Policies;

use App\Models\Property;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PriceHistoryPolicy extends Policy
{
    use HandlesAuthorization;

    public function index(User $user): bool
    {
        $authRoles = ['admin', 'employee'];

        return $this->isAuthorizedToDoThisAction($user->role->name, $authRoles);
    }

    public function store(User $user): bool
    {
        $authRoles = ['admin', 'employee', 'owner'];

        $userRole = $user->role->name;

        if ($this->isAuthorizedToDoThisAction($userRole, $authRoles)) {

            return true;

        }  else {

            return false;

        }
    }

    public function show(User $user): bool
    {
        $authRoles = ['admin', 'employee', 'owner'];

        $userRole = $user->role->name;

        if ($this->isAuthorizedToDoThisAction($userRole, $authRoles)) {

            return true;

        } else {

            return false;

        }
    }

    public function update(User $user, Property $property): bool
    {
        $authRoles = ['admin', 'employee'];
        $userRole = $user->role->name;

        if ($this->isAuthorizedToDoThisAction($userRole, $authRoles)) {

            return true;

        } elseif ($this->isAuthorizedToDoThisAction($userRole, ['employee'])
            && $property->owner->id === $user->id) {

            return true;

        } else {

            return false;

        }
    }

}
