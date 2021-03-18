<?php


namespace App\Services\User;


use App\Models\User;

/**
 * Class UserService
 * @package App\Services\User
 */
class UserService implements IUserService
{
    /**
     * @param string $id
     * @return bool
     */
    public function existsThisUser(string $id): bool
    {
        $existsUser = User::find($id);

        return !is_null($existsUser);
    }

    /**
     * @param string $id
     * @return bool
     */
    public function existsThisCustomer(string $id): bool
    {
        $role = 'customer';

        $existsCustomer =
            User::join('roles', 'users.role_id', '=', 'roles.id')
                ->where('roles.name', $role)
                ->where('users.id', $id)
                ->pluck('users.id')->first();

        return !is_null($existsCustomer);
    }

    /**
     * @param string $id
     * @return bool
     */
    public function existsThisEmployee(string $id): bool
    {
        $role = 'employee';

        $existsCustomer =
            User::join('roles', 'users.role_id', '=', 'roles.id')
                ->where('roles.name', $role)
                ->where('users.id', $id)
                ->pluck('users.id')->first();

        return !is_null($existsCustomer);
    }
}
