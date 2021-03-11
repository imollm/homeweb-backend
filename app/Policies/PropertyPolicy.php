<?php

namespace App\Policies;

use App\Models\Property;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class PropertyPolicy
 * @package App\Policies
 */
class PropertyPolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        $authorizedRoles =  ['admin', 'employee', 'owner'];
        $userRole =         $user->role->name;

        return in_array($userRole, $authorizedRoles);
    }

    /**
     * @param User $user
     * @param Property $property
     * @return bool
     */
    public function update(User $user, Property $property): bool
    {
        $authorizedRoles =      ['admin', 'employee'];
        $userRole =             $user->role->name;

        if (in_array($userRole, $authorizedRoles)) {
            return true;
        }
        elseif ($user->id === $property->user_id && $user->role->name === 'owner') {
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * @param User $user
     * @param Property $property
     * @return bool
     */
    public function setActive(User $user, Property $property): bool
    {
        $authorizedRoles =  ['admin', 'employee'];
        $userRole =         $user->role->name;

        if (in_array($userRole, $authorizedRoles)) {
            return true;
        }
        elseif ($userRole === 'owner' && $user->id === $property->user_id) {
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * @param User $user
     * @return bool
     */
    public function delete(User $user): bool
    {
        return $user->role->name === 'admin';
    }

}
