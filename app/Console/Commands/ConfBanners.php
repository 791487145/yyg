<?php

namespace App\Console\Commands;

use App\Models\ConfBanner;
use Illuminate\Console\Command;

class ConfBanners extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:banner';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        //更新地方馆banner
        self::updateConfBannerState();
    }

    static function updateConfBannerState(){
        $now = date('Y-m-d H:i:s');
        $confBanners = ConfBanner::whereIn('state',[ConfBanner::WAIT_ONLINE,ConfBanner::state_online])->get();
        foreach($confBanners as $confBanner){
            if($confBanner->start_time <= $now && $confBanner->end_time >= $now){
                $confBanner->update(['state'=>ConfBanner::state_online]);
            }
            if($confBanner->end_time < $now){
                $confBanner->update(['state'=>ConfBanner::STATE_OFFLINE]);
            }
        }
    }
}
