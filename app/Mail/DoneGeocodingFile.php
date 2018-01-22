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

    private $file;

    public function __construct(GeocodingFile $file)
    {
        $this->file = $file;
    }

    public function build()
    {
        return $this
            ->markdown('emails.done')
            ->subject('CSV Pronto!')
            ->attach($this->file->output_path, [
                'as' => 'result.csv',
                'mime' => 'text/csv',
            ]);
    }
}
