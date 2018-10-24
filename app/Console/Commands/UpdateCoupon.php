<?php
namespace App\Console\Commands;

use App\Models\CouponBase;
use App\Models\CouponGood;
use App\Models\CouponUser;
use App\Models\OrderBase;
use App\Models\UserWx;
use App\Models\GuideTum;
use Illuminate\Console\Command;
class UpdateCoupon extends Command{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:coupon';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update conponbase couponuser and coupongoods of state';
    
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
        self::updateCoupon();
    
    }
    
    
    //用来定时更新数据表state字段
    static function updateCoupon(){
        CouponBase::whereState(CouponBase::state_normal)->where('end_time','<',date('Y-m-d H:i:s'))->update(array('state'=>CouponBase::state_pause));
        CouponUser::whereState(CouponUser::state_unused)->where('end_time','<',date('Y-m-d H:i:s'))->update(array('state'=>CouponUser::state_expired));
    }

}