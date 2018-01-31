<?php

namespace GeoLV\Geocode;


use GeoLV\GeocodingFile;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use League\Csv\ResultSet;
use League\Csv\Statement;
use League\Csv\Writer;

class GeocodingFileProcessor
{
    /**
     * @var GeocodingFile
     */
    private $file;

    /**
     * @var int
     */
    private $chunk;

    /**
     * @var \League\Csv\AbstractCsv|Writer
     */
    private $output;

    /**
     * @var GeocoderProvider
     */
    private $geocoder;

    /**
     * @var Filesystem|\League\Flysystem\Filesystem
     */
    private $storage;

    /**
     * GeocodingFileProcessor constructor.
     * @param GeocodingFile $file
     * @param int $chunk
     */
    public function __construct(GeocodingFile $file, int $chunk = 20)
    {
        $this->file = $file;
        $this->chunk = $chunk;
        $this->geocoder = app('geocoder');
        $this->storage = Storage::disk('s3');
        $this->output = Writer::createFromFileObject(new \SplTempFileObject());
    }

    private function readRecords(): ResultSet
    {
        /** @var \League\Flysystem\AwsS3v3\AwsS3Adapter $adapter */
        $adapter = $this->storage->getAdapter();
        $response = $adapter->readStream($this->file->path);
        $reader = Reader::createFromStream($response['stream'])->setDelimiter($this->file->delimiter);
        $statement = (new Statement())->offset($this->file->offset)->limit($this->chunk);
        return $statement->process($reader);
    }

    public function process(): int
    {
        $records = $this->readRecords();
        $size = count($records);

        foreach ($records as $i => $record) {
            if ($i == 0 && $this->file->offset == 0 && $this->file->header)
                $this->output->insertOne($this->processHeader($record));
            else
                $this->output->insertOne($this->processRow($record));
        }

        $this->updateFileOffset($size);
        $this->uploadOutput();

        return $size;
    }

    private function processHeader($row): array
    {
        return array_merge($row, $this->file->fields);
    }

    private function processRow($row): array
    {
        $text = Dictionary::address($this->get($row, 'text'));
        $locality = $this->get($row, 'locality');
        $postalCode = $this->get($row, 'postal_code');
        $results = $this->geocoder->geocode($text, $locality, $postalCode)->insideLocality();
        $result = $results->first();
        $data = $row;

        if ($result) {
            foreach ($this->file->fields as $field) {
                if ($field == 'dispersion')
                    $value = $results->calculateDispersion();
                else
                    $value = $result->{$field};

                array_push($data, $value);
            }
        }

        return $data;
    }

    private function get($row, $type)
    {
        $value = [];
        foreach ($this->file->indexes[$type] as $index)
            array_push($value, $row[$index]);

        return trim(implode(" ", $value));
    }

    private function updateFileOffset($count)
    {
        $this->file->update(['offset' => $this->file->offset + $count]);
    }

    private function uploadOutput()
    {
        $output = substr($this->output->getContent(), 0, -1); // removes the last \n
        $this->storage->append($this->file->output_path, $output);
    }

    public function __destruct()
    {
        $this->output = null;
    }

}