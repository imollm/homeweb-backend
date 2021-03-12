<?php

namespace App\Policies;

use App\Models\Country;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\DB;

/**
 * Class CountryPolicy
 * @package App\Policies
 */
class CountryPolicy extends Policy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @return bool
     */
    public function store(User $user): bool
    {
        $authRoles = ['admin'];

        return $this->isAuthorizedToDoThisAction($user->role->name, $authRoles);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function update(User $user): bool
    {
        $authRoles = ['admin'];

        return $this->isAuthorizedToDoThisAction($user->role->name, $authRoles);
    }

    /**
     * @param User $user
     * @param Country $country
     * @return bool
     */
    public function destroy(User $user, Country $country): bool
    {
        $authRoles = ['admin'];

        $isAuthThisRole = $this->isAuthorizedToDoThisAction($user->role->name, $authRoles);

        $country = DB::table('countries')
                        ->leftJoin('cities', 'countries.id', '=', 'cities.country_id')
                        ->whereNull('cities.country_id')
                        ->where('countries.id', '=', $country->id)
                        ->get();

        $hasThisCountryAnyRelationWithCity = $country->count() === 0;

        return $isAuthThisRole && $hasThisCountryAnyRelationWithCity;
    }
}
