<?php

namespace GeoLV\Notifications;

use GeoLV\GeocodingFile;
use GeoLV\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class FailedGeocodingFile extends Notification
{
    use Queueable;
    /**
     * @var GeocodingFile
     */
    private $file;

    private $error;

    /**
     * Create a new notification instance.
     *
     * @param GeocodingFile $file
     * @param $error
     */
    public function __construct(GeocodingFile $file, $error)
    {
        $this->file = $file;
        $this->error = $error;
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
            ->level('warning')
            ->subject(__('Process canceled'))
            ->markdown('emails.canceled', [
                'file' => $this->file,
                'receiver' => $notifiable,
                'error' => $this->error
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
