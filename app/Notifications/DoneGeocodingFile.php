<?php

namespace GeoLV\Notifications;

use GeoLV\GeocodingFile;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class DoneGeocodingFile extends Notification
{
    use Queueable;

    /**
     * @var GeocodingFile
     */
    private $file;

    /**
     * Create a new notification instance.
     *
     * @param GeocodingFile $file
     */
    public function __construct(GeocodingFile $file)
    {
        $this->file = $file;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->level('success')
            ->subject(__('Your file has been successfully processed!'))
            ->markdown('emails.done', [
                'file' => $this->file,
                'receiver' => $notifiable,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'file' => $this->file->toArray()
        ];
    }
}
