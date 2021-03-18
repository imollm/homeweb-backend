<?php


namespace App\Services\User;


interface IUserService
{
    public function existsThisUser(string $id): bool;
    public function existsThisCustomer(string $id): bool;
    public function existsThisEmployee(string $id): bool;
}
