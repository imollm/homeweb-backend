<?php


namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

interface IPassportAuthService
{
    public function validateNewUserData(Request $request);

    public function registerNewUser(Request $request): User;

    public function login(array $credentials): ?Authenticatable;
}
