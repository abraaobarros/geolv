<?php

namespace GeoLV;

use Illuminate\Database\Eloquent\Model;

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
 * @method static GeocodingFile|Model create($data)
 */
class GeocodingFile extends Model
{
    protected $fillable = [
        'path',
        'email',
        'offset',
        'count',
        'delimiter',
        'done',
        'header',
        'indexes',
        'fields'
    ];

    protected $casts = [
        'header' => 'bool',
        'indexes' => 'array',
        'fields' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getOutputPathAttribute()
    {
        $hashCode = sha1($this->created_at->toDateTimeString());
        return "post-processing/{$hashCode}.csv";
    }

    public function getInitializingAttribute()
    {
        return $this->offset == 0;
    }

    public function getVelocityAttribute()
    {
        try {
            return $this->offset / $this->updated_at->diffInSeconds($this->created_at);
        } catch (\ErrorException $e) {
            return 0;
        }
    }

    public function getFileNameAttribute()
    {
        return str_replace('pre-processing/', '', $this->path);
    }

    public function getProgressAttribute()
    {
        return ($this->offset / $this->count) * 100;
    }

}
