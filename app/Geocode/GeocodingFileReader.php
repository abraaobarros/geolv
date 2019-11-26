<?php namespace GeoLV\Geocode;

use Aws\S3\S3Client;
use GeoLV\GeocodingFile;
use Illuminate\Support\Facades\Storage;
use League\Csv\Exception;
use League\Csv\Reader;
use League\Csv\Statement;
use League\Flysystem\AwsS3v3\AwsS3Adapter;

class GeocodingFileReader
{
    private $storage;
    private $file;

    const PREPROCESSED_FILE = 0;
    const POST_PROCESSED_FILE = 1;

    /**
     * GeocodingFileReader constructor.
     * @param $file
     */
    public function __construct(GeocodingFile $file)
    {
        $this->storage = Storage::disk('s3');
        $this->file = $file;
    }

    /**
     * @return GeocodingFile
     */
    public function getFile(): GeocodingFile
    {
        return $this->file;
    }

    public function read($type, $size = -1, $offset = 0)
    {
        /** @var AwsS3Adapter $adapter */
        /** @var S3Client $client */
        $adapter = $this->storage->getAdapter();
        $client = $adapter->getClient();
        $client->registerStreamWrapper();

        if ($type == static::PREPROCESSED_FILE)
            $response = $adapter->readStream($this->file->path);
        elseif ($type == static::POST_PROCESSED_FILE)
            $response = $adapter->readStream($this->file->output_path);

        $stream = !empty($response) ? $response['stream'] : null;
        if (empty($stream)) return [];

        try {
            $reader = Reader::createFromStream($stream)->setDelimiter($this->file->delimiter);

            if ($size >= 0) {
                return (new Statement())->offset($offset)->limit($size)->process($reader);
            } else if ($offset > 0) {
                return (new Statement())->offset($offset)->process($reader);
            } else {
                return $reader;
            }
        } catch (Exception $e) {
            return [];
        }
    }

    public function getField($row, $field)
    {
        if (isset($this->file->indexes[$field])) {
            $value = [];
            foreach ($this->file->indexes[$field] as $index)
                array_push($value, $row[$index]);

            return trim(implode(" ", $value));
        } else {
            return null;
        }
    }
}