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
        $this->fixed_assets = $params['fixed_assets'];
        $this->nonfixed_assets = $params['nonfixed_assets'];
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
        //$csv = "1,2,3";
        $fixed_assets = $this->createCSV($this->fixed_assets);
        $nonfixed_assets = $this->createCSV($this->nonfixed_assets);
        $message = (new MailMessage)->markdown('notifications.markdown.asset-report',
            [
                'assets'          => $this->assets,
                'fixed_assets'    => $this->fixed_assets,
                'nonfixed_assets' => $this->nonfixed_assets,
                'start_date'      => $this->start_date,
                'end_date'        => $this->end_date,
            ])
            ->subject(sprintf("%s %s - %s", "Disposed Asset Report: ", $this->start_date, $this->end_date))
            ->attachData($nonfixed_assets, "nonfixed_assets.csv")
            ->attachData($fixed_assets, "fixed_assets.csv");


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

    private function createCSV($assets)
    {
        $csv = "Asset,Asset Tag,Serial,Price,Disposed Date,Url\r\n";
        foreach ($assets as $asset)
        {
            $disposed_date = \App\Helpers\Helper::getFormattedDateObject($asset->updated_at, 'date');
            $price = $asset->purchase_cost;
            if ($price) {
                $price = '$' . $price;
            }

            $csv .= $asset->present()->name . ",";
            $csv .= $asset->asset_tag . ",";
            $csv .= $asset->serial . ",";
            $csv .= $price . ",";
            $csv .= $disposed_date['formatted'] . ",";
            $csv .= route('hardware.show', ['assetId' => $asset->id]) . "\r\n";
        }
        return $csv;
    }

}
