<?php

namespace App\Policies;

use App\Models\Property;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Auth;
use JetBrains\PhpStorm\Pure;

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
    public function all(User $user): bool
    {
        $authorizedRoles = ['admin', 'customer', 'employee'];

        return in_array($user->role->name, $authorizedRoles);
    }

    /**
     * @return bool
     */
    public function create(): bool
    {
        return Auth::user()->role->name == 'employee' || 'owner';
    }

    /**
     * @param User $user
     * @param Property $property
     * @return bool
     */
    public function show(User $user, Property $property): bool
    {
        $authorizedRoles = ['admin', 'customer', 'employee'];

        if (in_array($user->role->name, $authorizedRoles)) {
            return true;
        }
        elseif ($user->id === $property->user_id) {
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

}
