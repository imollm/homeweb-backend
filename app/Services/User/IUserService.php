<?php


namespace App\Services\User;


use App\Models\User;
use Illuminate\Http\Request;

interface IUserService
{
    public function existsThisUser(string $id): bool;
    public function existsThisCustomer(string $id): bool;
    public function existsThisEmployee(string $id): bool;
    public function existsThisOwner(string $id): bool;
    public function update(Request $request): bool;
    public function getUserById(string $id): User;
}
