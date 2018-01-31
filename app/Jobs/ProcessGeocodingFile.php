<?php

namespace GeoLV\Jobs;

use GeoLV\Address;
use GeoLV\AddressCollection;
use GeoLV\Geocode\Dictionary;
use GeoLV\Geocode\GeocodingFileProcessor;
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

    public function __construct(GeocodingFile $file)
    {
        $this->file = $file;
    }

    public function handle()
    {
        $processor = new GeocodingFileProcessor($this->file);

        if ($processor->process() > 0)
            dispatch(new ProcessGeocodingFile($this->file));
        else
            $this->notifyUser();
    }

    private function notifyUser()
    {
        $this->file->update(['done' => true]);
        \Mail::to($this->file->user->email)
            ->send(new DoneGeocodingFile($this->file));
    }
}
