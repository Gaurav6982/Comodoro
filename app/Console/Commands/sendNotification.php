<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\Reminders;
use Carbon\Carbon;
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
        $rms=Reminders::where('date',Carbon::now())->get();
        foreach($rms as $r)
        {
            echo $r->user."\n";
        }
        echo Carbon::now()."\n";
    }
}
