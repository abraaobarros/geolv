<?php

namespace GeoLV\Policies;

use GeoLV\User;
use GeoLV\GeocodingFile;
use Illuminate\Auth\Access\HandlesAuthorization;

class GeocodingFilePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the geocoding file.
     *
     * @param  \GeoLV\User  $user
     * @param  \GeoLV\GeocodingFile  $geocodingFile
     * @return mixed
     */
    public function view(User $user, GeocodingFile $geocodingFile)
    {
        return $geocodingFile->user_id == $user->id || $user->isAdmin();
    }

    public function prioritize(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the geocoding file.
     *
     * @param  \GeoLV\User  $user
     * @param  \GeoLV\GeocodingFile  $geocodingFile
     * @return mixed
     */
    public function delete(User $user, GeocodingFile $geocodingFile)
    {
        return $geocodingFile->user_id == $user->id || $user->isAdmin();
    }

}
