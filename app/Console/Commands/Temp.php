<?php

namespace App\Console\Commands;

use App\Http\Controllers\GenController;
use App\Models\GoodsBase;
use App\Models\GoodsImage;
use App\Models\GoodsSpec;
use App\Models\GuideBase;
use App\Models\GuideBilling;
use App\Models\OrderBase;
use App\Models\OrderGood;
use App\Models\OrderLog;
use App\Models\OrderPay;
use App\Models\OrderReturn;
use App\Models\PlatformBilling;
use App\Models\PlatformSm;
use App\Models\SmsVerificationCode;
use App\Models\SupplierBase;
use App\Models\SupplierBilling;
use App\Models\SupplierExpress;
use App\Models\SupplierSm;
use App\Models\TaBase;
use App\Models\TaBilling;
use App\Models\TaGroup;
use App\Models\TaSm;
use App\Models\UBase;
use App\Models\User;
use App\Models\UserBase;
use App\Models\UserWx;
use App\Models\GuideTum;
use Log;
use Illuminate\Console\Command;

class Temp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:temp';
    //protected $signature = 'temp';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

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

        //self::updateGoodsTitleAndSpecName();
        //self::updatePayType();
        //self::updateTaCode();
        //self::eqUserBaseWx();
        //self::updateInsertGuideTa();
        //self::updateSupplierExpress();
        //self::updateSupplierExpress();
        //self::eqUserBaseWx();
        //self::SupplierBilionCheck();
        self::orderWater();
    }

    Static function updateFirstImage()
    {
        $goods = GoodsBase::get();
        foreach ($goods as $good) {
            $GoodsImage = GoodsImage::whereGoodsId($good['id'])->first();
            if (!is_null($GoodsImage)) {
                $good->first_image = $GoodsImage['name'];
                $good->save();
            }
        }
        return true;
    }


    static function updateGoodsTitleAndSpecName()
    {
        $OrderGood = OrderGood::get();

        foreach ($OrderGood as $goods) {

            $GoodsBase = GoodsBase::whereId($goods['goods_id'])->first();

            $GoodsSpec = GoodsSpec::whereId($goods['spec_id'])->first();

            OrderGood::whereId($goods['id'])->update(array('goods_title' => $GoodsBase['title'], 'spec_name' => $GoodsSpec['name']));


        }
        return true;

    }


    static function updatePayType()
    {
        $OrderBase = OrderBase::get();
        foreach ($OrderBase as $v) {
            $num = OrderPay::whereOrderNo($v['order_no'])->count();
            if ($v['pay_type'] == 2 && $num == 0) {
                OrderBase::whereId($v['id'])->update(array('pay_type' => 3));
            }
        }

    }

    static function updateTaCode()
    {
        $TaBase = TaBase::get();
        foreach ($TaBase as $v) {
            TaBase::whereId($v['id'])->update(array('self_invite_code' => GenController::generateInviteCode()));
        }
    }

    static function updateOrderGoods()
    {
        $OrderGood = OrderGood::where('id', '<', 1345)->get();
        foreach ($OrderGood as $v) {
            $GoodsSpec = GoodsSpec::whereId($v['spec_id'])->first();
            OrderGood::whereId($v['id'])->update(array(
                'price_buying' => $GoodsSpec['price_buying']
            ));
        }
    }

    static function eqOrderGoodsSpec()
    {
        $SupplierBilling = SupplierBilling::where('id', '<', 456)->get();
        foreach ($SupplierBilling as $v) {
            $OrderGood = OrderGood::whereOrderNo($v['order_no'])->get();
            $amount = 0;
            foreach ($OrderGood as $vv) {
                $amount = $amount + ($vv['price_buying'] * $vv['num']);
            }
            if ($amount != $v['amount']) {
                echo $v['order_no'] . "||" . $amount . "||" . $v['amount'] . PHP_EOL;
            }
        }

    }

    static function eqUserBaseWx()
    {
        $ids = UserBase::whereIsGuide(1)->lists('id');
        $UserWx = UserWx::whereIn('uid', $ids)->get();
        foreach ($UserWx as $v) {
            $GuideBase = GuideBase::whereUid($v['uid'])->first();
            if ($GuideBase['id'] != $v['guide_id']) {
                echo $v['id'] . "||" . $v['guide_id'] . "||" . $GuideBase['id'] . PHP_EOL;
                UserWx::whereId($v['id'])->update(array('guide_id' => $GuideBase['id']));
            }
        }
    }

    static function updateInsertGuideTa()
    {
        $ta_id_array = array('4','15','31','32','33','34','35','36','37','38','39','40','41','42','43','44');

        foreach($ta_id_array as $ta_id){
            GuideTum::whereTaId($ta_id)->delete();
            $GuideBases = GuideBase::whereTaId($ta_id)->get();
            foreach ($GuideBases as $GuideBase) {
                $UserBase = UserBase::whereId($GuideBase['uid'])->first();
                $GuideTum = new GuideTum();
                $GuideTum->uid = $GuideBase['uid'];
                $GuideTum->guide_id = $GuideBase['id'];
                $GuideTum->ta_id = $GuideBase['ta_id'];
                $GuideTum->name = $UserBase['nick_name'];
                $GuideTum->mobile = $UserBase['mobile'];
                $GuideTum->save();
            }
        }
    }

    static function updateSupplierExpress()
    {
        $supplierBases = SupplierBase::get();
        foreach ($supplierBases as $supplierBase) {
            $supplierExpress = new SupplierExpress();
            $supplierExpress->title = "全国包邮";
            $supplierExpress->supplier_id = $supplierBase->id;
            $supplierExpress->total_amount = 0;
            $supplierExpress->express_amount = 0;
            $supplierExpress->state = 1;
            $supplierExpress->save();
        }
    }

    static function SupplierBilionCheck()
    {
        $SupplierBases = SupplierBase::whereState(SupplierBase::STATE_VALID)->get();
        $result = array();
        foreach($SupplierBases as $supplierBase){
            $SupplierBilions = SupplierBilling::whereSupplierId($supplierBase->id)->where('in_out',1)->get();
            foreach($SupplierBilions as $supplierBilion){
                $OrderGoods = OrderGood::whereOrderNo($supplierBilion->order_no)->get();
                $num = 0;
                $tmp = array();
                foreach($OrderGoods as $orderGood){
                    $num = $orderGood->num * $orderGood->price_buying + $num;
                }
                if($supplierBilion->amount != $num){
                    $tmp['order_no'] = $supplierBilion->order_no;
                    $tmp['num'] = $num;
                    $tmp['amount'] = $supplierBilion->amount;
                    $result[] = $tmp;
                }
            }
        }
        dd($result);
    }

    static function orderWater()
    {
        $GoodsBases = GoodsBase::get();
        $result = array();
        foreach($GoodsBases as $GoodsBase){
            $GoodsSpecs = GoodsSpec::whereGoodsId($GoodsBase->id)->get();
            $numSold = 0;
            $numWater = 0;
            foreach($GoodsSpecs as $GoodsSpec){
                if(is_null($GoodsSpec->num_sold)){
                    $GoodsSpec->num_sold = 0;
                }
                if(is_null($GoodsSpec->num_water)){
                    $GoodsSpec->num_water = 0;
                }
                $numSold = $numSold + $GoodsSpec->num_sold;
                $numWater = $numWater + $GoodsSpec->num_water;
            }
            if($numSold != $GoodsBase->num_sold || $numWater != $GoodsBase->num_water){
                $result[] = $GoodsBase->id;
            }
        }
        dd($result);
    }
}

