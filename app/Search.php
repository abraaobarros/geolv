<?php

namespace GeoLV;

use Geocoder\Query\GeocodeQuery;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Search
 * @package GeoLV
 * @property int id
 * @property string text
 * @property string locality
 * @property string postal_code
 * @property string locale
 * @property float max_d
 * @property-read string address
 * @property-read string state
 * @method static Builder geocodeQuery(GeocodeQuery $geocodeQuery)
 * @method static Search|Model firstOrCreate(array $data)
 * @method static findOrFail($get)
 */
class Search extends Model
{
    const DEFAULT_MAX_D = 0.003;

    protected $fillable = [
        'text',
        'locality',
        'postal_code'
    ];

    protected $appends = [
        'state'
    ];

    private $locality_obj;

    public function addresses(): BelongsToMany
    {
        return $this->belongsToMany(Address::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getAddressAttribute()
    {
        return trim(implode(" ", [$this->text, $this->locality, $this->postal_code]));
    }

    public function getLocalityAttribute()
    {
        try {
            list($locality, ) = explode('-', $this->attributes['locality']);
            return trim($locality);
        } catch (\Exception $exception) {
            return null;
        }
    }

    public function getStateAttribute()
    {
        try {
            list(, $state) = explode('-', $this->attributes['locality']);
            return trim($state);
        } catch (\Exception $exception) {
            return null;
        }
    }

    public function getMaxDAttribute($value)
    {
        if (filled($value))
            return $value;
        else
            return static::DEFAULT_MAX_D;
    }

    /**
     * @return Locality|null
     */
    public function findLocality()
    {
        if (empty($this->locality_obj)) {
            $this->locality_obj = Locality::where(function (Builder $q) {
                $q->whereRaw('lower(name) = ?', [mb_strtolower($this->locality)]);

                if (filled($this->state))
                    $q->whereRaw('upper(state) = ?', [mb_strtoupper($this->state)]);

                return $q;
            })->first();
        }

        return $this->locality_obj;
    }

    public function toRequestFormat(array $providers = [])
    {
        return array_merge(array_only($this->toArray(), $this->fillable), compact('providers'));
    }

}
