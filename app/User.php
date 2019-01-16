<?php

namespace GeoLV;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Class User
 * @package GeoLV
 * @property string name
 * @property string email
 * @property string password
 * @property \Illuminate\Support\Collection|GeocodingFile[] files
 * @property string role
 * @property int id
 * @property \Carbon\Carbon updated_at
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    const ADMIN_ROLE = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getLastUpdateAttribute()
    {
        $last = $this->files()->orderBy('updated_at', 'desc')->first();
        if (!empty($last))
            return $last->updated_at;
        else
            return $this->updated_at;
    }

    public function files()
    {
        return $this->hasMany(GeocodingFile::class);
    }

    public function isAdmin()
    {
        return $this->role == static::ADMIN_ROLE;
    }
}
