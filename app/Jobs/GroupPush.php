<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Models\TaGroup;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;

class GroupPush extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        exec("php /home/www/yyg/app/Apns/umeng/Group.php {$this->id}");
        Log::alert('push command'."php /home/www/yyg/app/Apns/umeng/Group.php {$this->id}");
    }
}
