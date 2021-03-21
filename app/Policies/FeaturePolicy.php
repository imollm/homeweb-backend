<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FeaturePolicy
{
    use HandlesAuthorization;

    public function store(User $user): bool
    {

    }

    public function update(User $user): bool
    {

    }

    public function show(User $user): bool
    {

    }

    public function destroy(User $user): bool
    {

    }
}
