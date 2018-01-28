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
 * @property array indexes
 * @property User|Model user
 * @method static GeocodingFile|Model create($data)
 */
class GeocodingFile extends Model
{
    protected $fillable = [
        'path',
        'email',
        'offset',
        'indexes',
        'done',
        'stopped'
    ];

    protected $casts = [
        'indexes' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getAddressIndexesAttribute()
    {
        return $this->indexes['text'];
    }

    public function getLocalityIndexesAttribute()
    {
        return $this->indexes['locality'];
    }

    public function getPostalCodeIndexesAttribute()
    {
        return $this->indexes['postal_code'];
    }

    public function getOutputPathAttribute()
    {
        $hashCode = sha1($this->created_at->toDateTimeString());
        return "post-processing/{$hashCode}.csv";
    }

    public function getInitializingAttribute()
    {
        return !\Storage::disk('s3')->exists($this->output_path);
    }

    public function getVelocityAttribute()
    {
        try {
            return $this->offset / $file->updated_at->diffInSeconds($file->created_at);
        } catch (\DivisionByZeroError $e) {
            return 0;
        }
    }

}
