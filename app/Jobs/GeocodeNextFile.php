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

    const CHUNK_SIZE = 5;

    public function handle(GeocodingFileProcessor $processor)
    {
        /** @var \GeoLV\GeocodingFile **/
        $file = GeocodingFile::nextProcessable()->first();

        if (empty($file))
            return;

        try {
            if ($processor->process($file, static::CHUNK_SIZE) == 0) {
                $this->notify($file,true);
            }
        } catch (\Exception $e) {
            report($e);
            $this->notify($file, false, $e->getMessage());
        } catch (\TypeError $e) {
            report($e);
            $this->notify($file,false, $e->getMessage());
        }

        if (GeocodingFile::nextProcessable()->first() != null)
            dispatch(new GeocodeNextFile());
    }

    private function notify(GeocodingFile $file, $success, $message = null)
    {
        if ($success) {
            if (!$file->done) {
                $file->update(['done' => true]);
                $file->user->notify(new DoneGeocodingFile($file));
            }
        } else {
            if (!$file->canceled_at) {
                $file->update(['canceled_at' => Carbon::now()]);
                $file->user->notify(new FailedGeocodingFile($file, $message));
            }
        }
    }

}
