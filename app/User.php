<?php

namespace GeoLV;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Class User
 * @package GeoLV
 * @property string name
 * @property string email
 * @property string password
 * @property \Illuminate\Support\Collection|GeocodingFile[] files
 */
class User extends Authenticatable
{
    use Notifiable;

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

    public function files()
    {
        return $this->hasMany(GeocodingFile::class);
    }
}
