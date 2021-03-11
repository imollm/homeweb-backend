<?php


namespace App\Policies;


use JetBrains\PhpStorm\Pure;

/**
 * Class Policy
 * @package App\Policies
 */
class Policy
{
    /**
     * @param string $authUserRole
     * @param array $rolesAuthorized
     * @return bool
     */
    protected function isAuthorizedToDoThisAction(string $authUserRole, array $rolesAuthorized): bool
    {
        return in_array($authUserRole, $rolesAuthorized);
    }
}
