<?php

namespace GeoLV\Mail;

use GeoLV\GeocodingFile;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class DoneGeocodingFile extends Mailable
{
    use Queueable, SerializesModels;
    /**
     * @var GeocodingFile
     */
    private $file;

    /**
     * Create a new message instance.
     *
     * @param GeocodingFile $file
     */
    public function __construct(GeocodingFile $file)
    {
        $this->file = $file;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->markdown('emails.done')
            ->attach(storage_path($this->file->path), [
                'as' => 'result.csv',
                'mime' => 'text/csv',
            ]);
    }
}
