<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;
use App\Models\Asset;
use App\Models\Statuslabel;
use App\Notifications\AssetReportNotification;
use DateTime;
use DateInterval;

class SendReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fxpal:send-report {--email=support@fxpal.com} {--start-date=} {--end-date=} {--status=Disposed}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email report.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $email = $this->option('email');
        $this->info(sprintf('Sending report to the following emails: %s', $email));

        // parse start/end dates
        $start_date = $this->option('start-date');
        $first_day = new DateTime('first day of this month');
        if (!$start_date) {
            $start_date = $first_day->format('Y-m-d');
        }
        $end_date = $this->option('end-date');
        if (!$end_date) {
            $start = new DateTime($start_date);
            $end_date = $start->add(new DateInterval('P1M'))->format('Y-m-d');
        }
        $status = $this->option('status');

        $this->info(sprintf('email=%s, start=%s, end=%s, status=%s', $email, $start_date, $end_date, $status));

        // find the status label for "Disposed"
        $status = Statuslabel::where([
            ['name', '=', $status]
        ])->get();
        // $json_string = json_encode($status, JSON_PRETTY_PRINT);
        // $this->info($json_string);

        if (sizeof($status) == 1) {
            $assets = Asset::where([
                ['status_id', '=', $status[0]->id],
                ['updated_at', '>=', $start_date],
                ['updated_at', '<', $end_date],
            ])->get();

            foreach ($assets as $asset) {
                $this->info(sprintf("%d: %s - %s", $asset->id, $asset->name, $asset->updated_at));
            }

            // $json_string = json_encode($assets, JSON_PRETTY_PRINT);
            // $this->info($json_string);
        }

        $fixed_assets = array();
        $nonfixed_assets = array();
        foreach ($assets as $asset) {
            if (strtolower(substr($asset->asset_tag, 0, 2)) == 'hw') {
                $nonfixed_assets[] = $asset;
            } else {
                $fixed_assets[] = $asset;
            }
        }

        $params = [
            'assets' => $assets,
            'fixed_assets' => $fixed_assets,
            'nonfixed_assets' => $nonfixed_assets,
            'start_date' => $start_date,
            'end_date' => $end_date,
        ];

        // Send email

        $recipients = collect(explode(',', $email))->map(function ($item, $key) {
            return new \App\Models\Recipients\AlertRecipient($item);
        });

        \Notification::send($recipients, new AssetReportNotification($params));
    }
}
