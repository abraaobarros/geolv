<?php

namespace GeoLV\Jobs;

use Carbon\Carbon;
use Exception;
use GeoLV\Geocode\GeocodingFileProcessor;
use GeoLV\GeocodingFile;
use GeoLV\Notifications\DoneGeocodingFile;
use GeoLV\Notifications\FailedGeocodingFile;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use TypeError;

class GeocodeNextFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    const CHUNK_SIZE = 100;

    public function handle(GeocodingFileProcessor $processor)
    {
        /** @var GeocodingFile $file **/
        $file = GeocodingFile::processable()->first();

        if (empty($file))
            return;

        $lockName = implode('_', [class_basename($file), $file->id]);
        Cache::lock($lockName)->get(function () use ($processor, $file) {
            try {
                if ($processor->process($file, static::CHUNK_SIZE) == 0) {
                    $this->notify($file);
                }
            } catch (Exception $e) {
                report($e);
                $this->notify($file, $e);
            } catch (\ErrorException $e) {
                report($e);
                $this->notify($file,$e);
            }

            if (GeocodingFile::processable()->first() != null)
                GeocodeNextFile::dispatch();
        });
    }

    private function notify(GeocodingFile $file, Exception $exception = null)
    {
        $file->fresh();

        if (empty($exception)) {
            if (!$file->done) {
                $file->update(['done' => true]);
                $file->user->notify(new DoneGeocodingFile($file));
                ProcessFilePoints::dispatch($file);
            }
        } else {
            if (!$file->canceled_at) {
                $file->update(['canceled_at' => Carbon::now()]);
                $file->user->notify(new FailedGeocodingFile($file, $exception->getMessage()));
            }
        }
    }

}
