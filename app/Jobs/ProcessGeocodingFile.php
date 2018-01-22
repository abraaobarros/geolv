<?php

namespace GeoLV\Jobs;

use GeoLV\Address;
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

    /** @var GeocodingFile */
    private $file;

    private $limit;

    public function __construct(GeocodingFile $file)
    {
        $this->file = $file;
        $this->limit = 10;
    }

    /**
     * @return \Illuminate\Filesystem\Filesystem
     */
    private function storage()
    {
        return Storage::disk('local');
    }

    /**
     * @return \League\Csv\AbstractCsv|Reader
     */
    private function input()
    {
        return Reader::createFromPath(storage_path("app/{$this->file->path}"));
    }

    /**
     * @return \League\Csv\AbstractCsv|Writer
     */
    private function output()
    {
        return Writer::createFromPath(storage_path("app/{$this->file->output_path}"), 'a');
    }

    /**
     * @return \League\Csv\ResultSet|array
     */
    private function records()
    {
        $statement = (new Statement())->offset($this->file->offset)->limit($this->limit);
        return $statement->process($this->input());
    }

    public function handle()
    {
        $output = $this->output();
        $records = $this->records();

        foreach ($records as $record)
            $this->processRow($record, $output);

        $this->file->update(['offset' => $this->file->offset + $this->limit]);

        $output = null;

        if (count($records) > 0)
            dispatch(new ProcessGeocodingFile($this->file));
        else {
            \Mail::to($this->file->email)->send(new DoneGeocodingFile($this->file));
            $this->storage()->delete($this->file->path);
        }
    }

    private function processRow($row, Writer $output)
    {
        $text = Dictionary::address($this->get($row, 'address'));
        $locality = $this->get($row, 'locality');
        $postalCode = $this->get($row, 'postal_code');
        /** @var Address $result */
        $result = app('geocoder')->geocode($text, $locality, $postalCode)->first();

        $data = array_merge($row, [$result->latitude, $result->longitude]);
        $output->insertOne($data);
    }

    private function get($row, $type)
    {
        $value = [];
        foreach ($this->file->{"{$type}_indexes"} as $index)
            array_push($value, $row[$index]);

        return trim(implode(" ", $value));
    }
}
