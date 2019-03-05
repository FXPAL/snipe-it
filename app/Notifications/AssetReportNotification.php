<?php

namespace App\Notifications;

use App\Models\Settings;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AssetReportNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($params)
    {
        $this->assets = $params['assets'];
        $this->start_date = $params['start_date'];
        $this->end_date = $params['end_date'];
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $message = (new MailMessage)->markdown('notifications.markdown.asset-report',
            [
                'assets'        => $this->assets,
                'start_date'    => $this->start_date,
                'end_date'      => $this->end_date,
            ])
            ->subject(sprintf("%s %s - %s", "Disposed Asset Report: ", $this->start_date, $this->end_date));


        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
