<?php

namespace App\Console\Commands;

use App\Models\ConfBanner;
use App\Models\GoodsBase;
use App\Models\GoodsSpec;
use App\Models\GuideBase;
use App\Models\GuideBilling;
use App\Models\OrderBase;
use App\Models\OrderGood;
use App\Models\OrderLog;
use App\Models\OrderReturn;
use App\Models\PlatformBilling;
use App\Models\PlatformSm;
use App\Models\SmsVerificationCode;
use App\Models\SupplierBase;
use App\Models\SupplierBilling;
use App\Models\SupplierSm;
use App\Models\TaBase;
use App\Models\TaBilling;
use App\Models\TaGroup;
use App\Models\TaSm;
use App\Models\UBase;
use App\Models\UserBase;
use Log;
use Illuminate\Console\Command;

class Goods extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:goods';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'goods change';

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

        //更新GoodsBase guide_amount 字段
        self::updateGoodsGuideAmount();


        //更新GoodsBase guide_amount 字段
        self::updateGoodsSoldNum();

    }


    static function updateGoodsGuideAmount(){
        $GoodsBase = GoodsBase::get();
        foreach($GoodsBase as $v){
            $GoodsSpec = GoodsSpec::whereGoodsId($v['id'])->first();
            $guide_amount = $GoodsSpec['price'] * $GoodsSpec['guide_rate'] / 100;
            if($guide_amount > 0){
                GoodsBase::whereId($v['id'])->update(array('guide_amount'=>$guide_amount));
            }
        }
    }


    static function updateGoodsSoldNum(){
        $GoodsBase = GoodsBase::get();
        foreach($GoodsBase as $v){
            $spec = GoodsSpec::whereGoodsId($v['id'])->get();
            $num = 0 ;
            $num_sold = 0 ;
            $num_water = 0;
            foreach($spec as $vv){
                $num = $num + $vv['num'];
                $num_sold = $num_sold + $vv['num_sold'];
                if(is_null($vv['num_water'])){
                    $vv['num_water'] = 0;
                }
                $num_water = $num_water + $vv['num_water'];
            }
            GoodsBase::whereId($v['id'])->update(array('num'=>$num,'num_sold'=>$num_sold,'num_water'=>$num_water));
        }

    }

}
