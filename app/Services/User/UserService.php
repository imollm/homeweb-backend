<?php


namespace App\Services\User;


use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * Class UserService
 * @package App\Services\User
 */
class UserService implements IUserService
{
    /**
     * @var User
     */
    private User $user;

    /**
     * @var Role
     */
    private Role $role;

    /**
     * UserService constructor.
     * @param User $user
     * @param Role $role
     */
    public function __construct(User $user, Role $role)
    {
        $this->user = $user;
        $this->role = $role;
    }

    /**
     * @param string $id
     * @return bool
     */
    public function existsThisUser(string $id): bool
    {
        $existsUser = $this->user->find($id);

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
            $this->user->join('roles', 'users.role_id', '=', 'roles.id')
                ->where('roles.name', '=',$role)
                ->where('users.id', '=', $id)
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
            $this->user->join('roles', 'users.role_id', '=', 'roles.id')
                ->where('roles.name', '=', $role)
                ->where('users.id', '=', $id)
                ->pluck('users.id')->first();

        return !is_null($existsCustomer);
    }

    /**
     * @param string $id
     * @return bool
     */
    public function existsThisOwner(string $id): bool
    {
        $role = 'owner';

        $existsOwner =
            $this->user->join('roles', 'users.role_id', '=', 'roles.id')
                ->where('roles.name', '=', $role)
                ->where('users.id', '=', $id)
                ->pluck('users.id')->first();

        return !is_null($existsOwner);
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function update(Request $request): bool
    {
        $user = $this->user->find($request->input('id'));

        return  $user->update($request->all());
    }

    /**
     * @param string $id
     * @return User
     */
    public function getUserById(string $id): User
    {
        return $this->user->find($id);
    }

    /**
     * @return array
     */
    public function getOwners(): array
    {
        return $this->role->with('users')->whereName('owner')->get()->toArray();
    }
}
