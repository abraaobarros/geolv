<?php

namespace GeoLV\Jobs;

use GeoLV\Geocode\GeoLVPythonService;
use GeoLV\GeocodingFile;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Cache;

class ProcessFilePoints implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var GeocodingFile
     */
    public $file;

    /**
     * Create a new job instance.
     *
     * @param GeocodingFile $file
     */
    public function __construct(GeocodingFile $file)
    {
        $this->file = $file;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $results_key = "files.{$this->file->id}.results";
        if (Cache::has($results_key))
            return;

        $callbackResults = function () {
            $results = $this->getFileResults();
            return $results;
        };

        if ($this->file->done) {
            Cache::rememberForever($results_key, $callbackResults);
        } else {
            Cache::remember($results_key, 500, $callbackResults);
        }
    }

    private function getFileResults()
    {
        $results = collect();
        $python = new GeoLVPythonService();
        $data = $python->getFilePoints($this->file);

        foreach ($data as $row) {
            $results->add((object)$row);
        }

        return $results;
    }
}
