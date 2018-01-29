<?php

namespace GeoLV\Jobs;

use GeoLV\Address;
use GeoLV\AddressCollection;
use GeoLV\Geocode\Dictionary;
use GeoLV\GeocodingFile;
use GeoLV\Mail\DoneGeocodingFile;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use League\Csv\Statement;
use League\Csv\Writer;

class ProcessGeocodingFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $file;
    public $chunkSize;

    public function __construct(GeocodingFile $file, $chunkSize = 50)
    {
        $this->file = $file;
        $this->chunkSize = $chunkSize;
    }

    /**
     * @return \League\Csv\ResultSet|array
     */
    private function records()
    {
        /** @var \League\Flysystem\AwsS3v3\AwsS3Adapter $adapter */
        $adapter = Storage::disk('s3')->getAdapter();
        $response = $adapter->readStream($this->file->path);
        $reader = Reader::createFromStream($response['stream']);
        $statement = (new Statement())->offset($this->file->offset)->limit($this->chunkSize);
        return $statement->process($reader);
    }

    public function handle()
    {
        $records = $this->records();
        $output = Writer::createFromFileObject(new \SplTempFileObject());

        foreach ($records as $i => $record) {
            if ($i == 0 && $this->file->offset == 0 && $this->file->header)
                $output->insertOne($this->processHeader($record));
            else
                $output->insertOne($this->processRow($record));
        }

        $this->updateFileOffset(count($records));
        $this->appendOutput($output);

        if (count($records) > 0)
            dispatch(new ProcessGeocodingFile($this->file));
        else
            $this->notifyUser();
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

        /** @var AddressCollection $results */
        $results = app('geocoder')->geocode($text, $locality, $postalCode);
        /** @var Address $result */
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

    private function appendOutput(Writer $output)
    {
        Storage::disk('s3')->append($this->file->output_path, $output->getContent());
    }

    private function notifyUser()
    {
        $this->file->update(['done' => true]);
        \Mail::to($this->file->user->email)
            ->send(new DoneGeocodingFile($this->file));
    }
}
