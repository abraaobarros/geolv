<?php

namespace GeoLV\Policies;

use GeoLV\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can list all users.
     *
     * @param User $user
     * @return bool
     */
    public function view(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $auth
     * @param User $model
     * @return mixed
     */
    public function destroy(User $auth, User $model)
    {
        return $model->id == $auth->id || $auth->isAdmin();
    }
}
