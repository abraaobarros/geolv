<?php

namespace GeoLV\Jobs;

use GeoLV\Geocode\GeocodingFileReader;
use GeoLV\Geocode\GeoLVPythonService;
use GeoLV\GeocodingFile;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Collection;
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
        $results = Cache::get($results_key);

        if (!empty($results))
            return;

        if ($results instanceof Collection && !$results->isEmpty())
            return;

        $callbackResults = function () {
            return $this->getFileResults();
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
        $reader = new GeocodingFileReader($this->file);
        $stream = $reader->read(GeocodingFileReader::POST_PROCESSED_FILE, -1, $this->file->header ? 1 : 0);
        $n_fields = count($this->file->fields);
        $lat_idx = array_search('latitude', $this->file->fields);
        $lng_idx = array_search('longitude', $this->file->fields);

        foreach ($stream as $row) {
            $n_cols = count($row) - $n_fields;

            try {
                $results->add((object)[
                    'text' => $reader->getField($row, 'text'),
                    'latitude' => $row[$n_cols + $lat_idx],
                    'longitude' => $row[$n_cols + $lng_idx],
                ]);
            } catch (\Exception $e) {
                report($e);
            }
        }

        return $results;
    }
}
