<?php

namespace GeoLV;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class GeocodingFile
 * @package GeoLV
 * @property int[] address_indexes
 * @property int[] locality_indexes
 * @property int[] postal_code_indexes
 * @property string path
 * @property string output_path
 * @property string email
 * @property boolean header
 * @property array indexes
 * @property array fields
 * @property User|Model user
 * @property integer user_id
 * @property int offset
 * @property int count
 * @property \Carbon\Carbon updated_at
 * @property \Carbon\Carbon created_at
 * @property int id
 * @property string delimiter
 * @property integer priority
 * @property bool done
 * @property \Carbon\Carbon canceled_at
 * @property string error_output_path
 * @property array providers
 * @property string name
 * @property string error_name
 * @method static GeocodingFile|Model create($data)
 * @method static Builder nextProcessable()
 */
class GeocodingFile extends Model
{
    protected $fillable = [
        'name',
        'path',
        'offset',
        'count',
        'delimiter',
        'done',
        'header',
        'indexes',
        'fields',
        'priority',
        'canceled_at',
        'providers'
    ];

    protected $casts = [
        'header'    => 'bool',
        'priority'  => 'int',
        'indexes'   => 'array',
        'fields'    => 'array',
        'providers' => 'array'
    ];

    protected $dates = [
        'canceled_at',
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getNameAttribute()
    {
        if (empty($this->attributes['name']))
            return str_replace('pre-processing/', '', $this->path);;

        return $this->attributes['name'];
    }

    public function getErrorNameAttribute()
    {
        return 'errors_' . $this->name;
    }

    public function scopeNextProcessable(Builder $query)
    {
        return $query
            ->where('done', false)
            ->whereNull('canceled_at')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'asc');
    }

    public function getOutputPathAttribute()
    {
        $hashCode = sha1($this->created_at->toDateTimeString());
        return "post-processing/{$hashCode}.csv";
    }

    public function getErrorOutputPathAttribute()
    {
        $hashCode = sha1($this->created_at->toDateTimeString());
        return "post-processing/{$hashCode}-error.csv";
    }

    public function getInitializingAttribute()
    {
        return $this->offset == 0;
    }

    public function getVelocityAttribute()
    {
        try {
            return $this->offset / $this->updated_at->diffInSeconds($this->created_at);
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function getProgressAttribute()
    {
        try {
            return ($this->offset / $this->count) * 100;
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function getInProcessAttribute()
    {
        $next = static::nextProcessable()->first();
        return $next && $this->id == $next->id;
    }

    public function toggleCancel()
    {
        if ($this->canceled_at) {
            $this->canceled_at = null;
        } else {
            $this->canceled_at = Carbon::now();
        }

        $this->save();

        return $this->canceled_at;
    }

}
