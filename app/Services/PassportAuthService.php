<?php


namespace App\Services;

use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

/**
 * Class PassportAuthService
 * @package App\Services
 */
class PassportAuthService
{
    /**
     * Validate if request data is valid
     * Validate if user already exists
     *
     * @param Request $request
     */
    public function validateNewUserData(Request $request)
    {
        $request->validate([
            'name' => 'required|min:4',
            'email' => 'required|unique:users|email',
            'password' => 'required|min:8',
            'phone' => 'required|unique:users|min:9',
            'address' => 'required|max:255',
            'fiscal_id' => 'required|unique:users|max:25',
            'role' => 'required|string'
        ]);
    }

    /**
     *
     * @param Request $request
     * @return User
     */
    public function registerNewUser(Request $request): User
    {
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
            'fiscal_id' => $request->input('fiscal_id'),
            'role_id' => Role::where('name', '=', $request->input('role'))->first()->id,
        ]);

        $user['token'] = $user->createToken('LaravelAuthApp')->accessToken;

        return $user;
    }

    /**
     * @param array $credentials
     * @return Authenticatable|null
     */
    public function login(array $credentials): ?Authenticatable
    {
        if (auth()->attempt($credentials)) {
            $token = auth()->user()->createToken('LaravelAuthApp')->accessToken;
            $user = auth()->user();
            $user['token'] = $token;
            $user['role'] = User::find(auth()->id())->role->name;
            return $user;
        }
        return null;
    }
}
