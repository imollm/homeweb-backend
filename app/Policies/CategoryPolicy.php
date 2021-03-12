<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class CategoryPolicy
 * @package App\Policies
 */
class CategoryPolicy extends Policy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $this->isAuthorizedToDoThisAction($user->role->name, ['admin', 'employee']);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function update(User $user): bool
    {
        return $this->isAuthorizedToDoThisAction($user->role->name, ['admin', 'employee']);
    }

    /**
     * @param User $user
     * @param Category $category
     * @return bool
     */
    public function delete(User $user, Category $category): bool
    {
        return $this->isAuthorizedToDoThisAction($user->role->name, ['admin']);
    }
}
