<?php

namespace GeoLV\Geocode;


use Aws\S3\S3Client;
use GeoLV\GeocodingFile;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use League\Csv\CannotInsertRecord;
use League\Csv\Exception;
use League\Csv\Reader;
use League\Csv\ResultSet;
use League\Csv\Statement;
use League\Csv\Writer;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use SplTempFileObject;

class GeocodingFileProcessor
{
    /**
     * @var \League\Csv\AbstractCsv|Writer
     */
    private $output;

    /**
     * @var \League\Csv\AbstractCsv|Writer
     */
    private $errorOutput;

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
     */
    public function __construct()
    {
        $this->geocoder = app('geocoder');
        $this->storage = Storage::disk('s3');
        $this->output = Writer::createFromFileObject(new SplTempFileObject());
        $this->errorOutput = Writer::createFromFileObject(new SplTempFileObject());
    }

    /**
     * @param GeocodingFile $file
     * @param $chunk
     * @return int
     */
    public function process(GeocodingFile $file, $chunk): int
    {
        $reader = new GeocodingFileReader($file);
        $records = $reader->read(GeocodingFileReader::PREPROCESSED_FILE, $chunk, $file->offset);
        $size = count($records);
        $this->geocoder->setProviders(GeocoderProvider::LOW_COST_STRATEGY, $file->providers, $file->user);

        info("[GEOCODE: {$file->id}] {$size} records read");

        if ($size == 0)
            return 0;

        foreach ($records as $i => $record) {
            try {
                if ($i == 0 && $file->offset == 0 && $file->header)
                    $this->processHeader($file, $record);
                else
                    $this->processRow($reader, $record);
            } catch (CannotInsertRecord $exception) {
                report($exception);
            }
        }

        $this->updateFileOffset($file, $size);
        $this->uploadOutput($file);

        return $size;
    }

    /**
     * @param $file
     * @param $row
     * @throws CannotInsertRecord
     */
    private function processHeader($file, $row)
    {
        $this->output->insertOne(array_merge($row, $file->fields));
        $this->errorOutput->insertOne($row);
    }

    /**
     * @param GeocodingFileReader $reader
     * @param array $row
     * @throws CannotInsertRecord
     */
    private function processRow(GeocodingFileReader $reader, array $row)
    {
        $text = Dictionary::address($reader->getField($row, 'text'));
        $locality = $reader->getField($row, 'locality');
        $state = $reader->getField($row, 'state');
        $postalCode = $reader->getField($row, 'postal_code');
        $locality = empty($state) ? $locality : "$locality - $state";
        $emptyRow = empty($postalCode) ? (empty($text) && empty($locality)) : false;

        if (!$emptyRow) {
            $results = $this->geocoder->geocode($text, $locality, $postalCode);
            $result = filled($locality) ? $results->insideLocality()->first() : $results->first();

            if (filled($result)) {
                $mainCluster = $results->inMainCluster();

                foreach ($reader->getFile()->fields as $field) {
                    if ($field == 'dispersion')
                        $value = $mainCluster->calculateDispersion();
                    else if ($field == 'providers_count')
                        $value = $mainCluster->getProvidersCount();
                    else if ($field == 'precision')
                        $value = $mainCluster->calculatePrecision();
                    else if ($field == 'clusters_count')
                        $value = $results->getClustersCount();
                    else if ($field == 'confidence')
                        $value = $results->calculateConfidence();
                    else
                        $value = $result->{$field};

                    array_push($row, $value);
                }

                $this->output->insertOne($row);
            } else {
                $this->errorOutput->insertOne($row);
            }
        }
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
        info("[GEOCODE: {$file->id}] uploading output");

        $outputContent = substr($this->output->getContent(), 0, -1); // removes the last \n
        $this->storage->append($file->output_path, $outputContent);

        $errorOutputContent = substr($this->errorOutput->getContent(), 0, -1); // removes the last \n
        $this->storage->append($file->error_output_path, $errorOutputContent);
    }

    public function __destruct()
    {
        $this->output = null;
        $this->errorOutput = null;
    }

}