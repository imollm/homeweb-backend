<?php


namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

interface PassportAuthServiceI
{
    public function validateNewUserData(Request $request);

    public function registerNewUser(Request $request): User;

    public function login(array $credentials): ?Authenticatable;
}
