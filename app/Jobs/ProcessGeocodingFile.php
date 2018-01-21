<?php

namespace GeoLV\Jobs;

use GeoLV\Geocode\Dictionary;
use GeoLV\Geocode\GeocoderProvider;
use GeoLV\GeocodingFile;
use GeoLV\Mail\DoneGeocodingFile;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;
use League\Csv\Statement;
use League\Csv\Writer;

class ProcessGeocodingFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var GeocoderProvider */
    private $geocoder;
    /** @var GeocodingFile */
    private $file;
    /** @var \League\Csv\Reader */
    private $input;
    /** @var \League\Csv\Writer */
    private $output;

    public function __construct(GeocodingFile $file)
    {
        $this->file = $file;
        $this->geocoder = app('geocoder');
        $this->input = Reader::createFromPath(storage_path($this->file->path), 'r');
        $this->output = Writer::createFromPath(storage_path("post-processing/{$this->file->id}.csv"));
    }

    public function handle()
    {
        $offset = $this->file->offset;
        $limit = 100;
        $statement = (new Statement())->offset($offset)->limit($limit);
        $records = $statement->process($this->input);

        DB::transaction(function () use ($records, $offset, $limit) {

            for ($i = $offset; $i < $limit; $i++)
                $this->processRow($records[$i]);

            $this->file->update(['offset' => $offset + $limit]);

        });

        if (count($records) > 0)
            $this->chain([new ProcessGeocodingFile($this->file)])->dispatch();
        else
            \Mail::to($this->file->email)
                ->send(new DoneGeocodingFile($this->file));
    }

    private function processRow($row)
    {
        $text = Dictionary::address($this->get($row, 'address'));
        $locality = $this->get($row, 'locality');
        $postalCode = $this->get($row, 'postal_code');
        $results = $this->geocoder->geocode($text, $locality, $postalCode);

        $this->output->insertOne(array_values($results->first()->fields));
    }

    private function get($row, $type)
    {
        $value = [];
        foreach ($this->file->{"{$type}_indexes"} as $index)
            array_push($value, $row[$index]);

        return trim(implode(" ", $value));
    }
}
