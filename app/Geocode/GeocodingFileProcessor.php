<?php

namespace GeoLV\Geocode;


use GeoLV\GeocodingFile;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use League\Csv\CannotInsertRecord;
use League\Csv\Reader;
use League\Csv\ResultSet;
use League\Csv\Statement;
use League\Csv\Writer;

class GeocodingFileProcessor
{
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
     */
    public function __construct()
    {
        $this->geocoder = app('geocoder');
        $this->storage = Storage::disk('s3');
        $this->output = Writer::createFromFileObject(new \SplTempFileObject());
    }

    private function readRecords(GeocodingFile $file, $chunk): ResultSet
    {
        /** @var \League\Flysystem\AwsS3v3\AwsS3Adapter $adapter */
        $adapter = $this->storage->getAdapter();
        $response = $adapter->readStream($file->path);
        $reader = Reader::createFromStream($response['stream'])->setDelimiter($file->delimiter);
        $statement = (new Statement())->offset($file->offset)->limit($chunk);
        return $statement->process($reader);
    }

    /**
     * @param GeocodingFile $file
     * @param $chunk
     * @return int
     */
    public function process(GeocodingFile $file, $chunk): int
    {
        $records = $this->readRecords($file, $chunk);
        $size = count($records);

        foreach ($records as $i => $record) {
            try {
                if ($i == 0 && $file->offset == 0 && $file->header)
                    $this->output->insertOne($this->processHeader($file, $record));
                else
                    $this->output->insertOne($this->processRow($file, $record));
            } catch (CannotInsertRecord $exception) {}
        }

        $this->updateFileOffset($file, $size);
        $this->uploadOutput($file);

        return $size;
    }

    private function processHeader($file, $row): array
    {
        return array_merge($row, $file->fields);
    }

    private function processRow(GeocodingFile $file, array $row): array
    {
        $text = Dictionary::address($this->get($file, $row, 'text'));
        $locality = $this->get($file, $row, 'locality');
        $postalCode = $this->get($file, $row, 'postal_code');
        $results = $this->geocoder->geocode($text, $locality, $postalCode)->insideLocality();
        $result = $results->first();

        if ($result) {
            foreach ($file->fields as $field) {
                if ($field == 'dispersion')
                    $value = $results->inMainCluster()->calculateDispersion();
                else if ($field == 'clusters_count')
                    $value = $results->getClustersCount();
                else if ($field == 'providers_count')
                    $value = $results->inMainCluster()->getProvidersCount();
                else
                    $value = $result->{$field};

                array_push($row, $value);
            }
        }

        return $row;
    }

    private function get(GeocodingFile $file, $row, $type)
    {
        $value = [];
        foreach ($file->indexes[$type] as $index)
            array_push($value, $row[$index]);

        return trim(implode(" ", $value));
    }

    private function updateFileOffset(GeocodingFile $file, $count)
    {
        $file->offset = $file->offset + $count;

        if ($count == 0) {
            $file->done = true;
        }

        $file->save();
    }

    private function uploadOutput(GeocodingFile $file)
    {
        $output = substr($this->output->getContent(), 0, -1); // removes the last \n
        $this->storage->append($file->output_path, $output);
    }

    public function __destruct()
    {
        $this->output = null;
    }

}