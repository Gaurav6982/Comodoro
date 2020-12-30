<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\Reminders;
use Carbon\Carbon;
use DateTime;
class sendNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:notify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify The User.';

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
     * @return int
     */
    public function handle()
    {
        $var=Carbon::parse("2020-12-30 22:55:00")->toDateTimeString()."\n";
        echo $var;
        $rms=Reminders::whereDate('date',"=",Carbon::parse("2020-12-30 22:55:00"))->get();
        foreach($rms as $r)
        {
            // echo "eell\n";
            echo $r->event->user->name."\n";
        }
        echo Carbon::now()."\n";
    }
}
