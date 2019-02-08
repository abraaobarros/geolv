<?php

namespace GeoLV\Jobs;

use Carbon\Carbon;
use GeoLV\Geocode\CannotProcessFileException;
use GeoLV\Geocode\GeocodingFileProcessor;
use GeoLV\GeocodingFile;
use GeoLV\Notifications\DoneGeocodingFile;
use GeoLV\Notifications\FailedGeocodingFile;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class GeocodeNextFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var GeocodingFile
     */
    public $file;

    const CHUNK_SIZE = 5;

    public function __construct()
    {
        $this->file = GeocodingFile::nextProcessable()->first();
    }

    public function handle(GeocodingFileProcessor $processor)
    {
        if (empty($this->file))
            return;

        try {
            if ($processor->process($this->file, static::CHUNK_SIZE) == 0) {
                $this->notify(true);
            }
        } catch (\Exception $e) {
            report($e);
            $this->notify(false, $e->getMessage());
        } catch (\TypeError $e) {
            report($e);
            $this->notify(false, $e->getMessage());
        }

        if (GeocodingFile::nextProcessable()->first() != null)
            dispatch(new GeocodeNextFile());
    }

    private function notify($success, $message = null)
    {
        if ($success) {
            $this->file->update(['done' => true]);
            $this->file->user->notify(new DoneGeocodingFile($this->file));
        } else {
            $this->file->update(['canceled_at' => Carbon::now()]);
            $this->file->user->notify(new FailedGeocodingFile($this->file, $message));
        }
    }

}
