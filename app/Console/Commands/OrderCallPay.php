<?php

namespace App\Console\Commands;

use App\Http\Controllers\GenController;
use App\Models\GoodsSpec;
use App\Models\GuideBase;
use App\Models\OrderLog;
use App\Models\OrderWx;
use App\Models\PlatformBilling;
use App\Models\PlatformSm;
use App\Models\User;
use Log;
use App\Models\UBase;
use Illuminate\Http\Request;
use App\Models\GuideBilling;
use App\Models\OrderBase;
use App\Models\OrderGood;
use App\Models\SupplierBase;
use App\Models\SupplierBilling;
use App\Models\TaBase;
use App\Models\TaBilling;
use App\Models\UserBase;
use Illuminate\Console\Command;

class OrderCallPay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:pay-callback';
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

        self::callPayBack();

    }

    Static function callPayBack()
    {
        $amount = 4290;
        $out_trade_no = '4002842001201708217462057048';
        $OrderBases = OrderBase::whereOrderNo('1708211708123941')->get();
        foreach($OrderBases as $OrderBase){
            //log
            $OrderLog = new OrderLog();
            $OrderLog->order_no = $OrderBase['order_no'];
            $OrderLog->action = '支付成功';
            $OrderLog->content = json_encode(array('before_state'=>$OrderBase['state'],'after_state'=>OrderBase::STATE_PAYED));
            $OrderLog->save();

            //更新订单状态
            $OrderBase->state = OrderBase::STATE_PAYED;
            $OrderBase->amount_real = $amount/100;
            $OrderBase->save();

            //记导游账单
            $UserBase = UserBase::whereId($OrderBase['uid'])->first();
            $GuideBilling = new GuideBilling();
            $GuideBilling->guide_id = $OrderBase['guide_id'];
            $GuideBilling->uid = $OrderBase['uid'];
            $GuideBilling->order_no = $OrderBase['order_no'];
            $GuideBilling->trade_no = $out_trade_no;
            $GuideBilling->in_out = GuideBilling::in_income;
            $GuideBilling->amount = $OrderBase['guide_amount'];
            $GuideBilling->balance = $UserBase['amount'];
            $GuideBilling->content = '返利收入';
            $GuideBilling->state = GuideBilling::state_nofund;
            $GuideBilling->group_id = $OrderBase['group_id'];
            $GuideBilling->save();

            //记旅行社账单
            $TaBase = TaBase::whereId($OrderBase['ta_id'])->first();
            $TaBilling = new TaBilling();
            $TaBilling->ta_id = $OrderBase['ta_id'];
            $TaBilling->order_no = $OrderBase['order_no'];
            $TaBilling->trade_no = $out_trade_no;
            $TaBilling->in_out = TaBilling::in_income;
            $TaBilling->amount = $OrderBase['ta_amount'];
            $TaBilling->balance = $TaBase['amount'];
            $TaBilling->content = '返利收入';
            $TaBilling->state = TaBilling::state_nofund;
            $TaBilling->save();


            //供应商账单
            //计算进价
            $OrderGood = OrderGood::whereOrderNo($OrderBase['order_no'])->whereIsGift(OrderGood::is_gift_no)->get();
            $total_price_buying = 0;
            foreach($OrderGood as $vv){
                $GoodsSpec = GoodsSpec::whereId($vv['spec_id'])->first();
                $total_price_buying = $total_price_buying + ($GoodsSpec['price_buying'] * $vv['num']);
            }


            //快递费
            if($OrderBase->amount_express > 0){
                $total_price_buying = $total_price_buying + $OrderBase->amount_express;
            }

            //减优惠券
            if($OrderBase->amount_coupon > 0){
                $total_price_buying = $total_price_buying - $OrderBase->amount_coupon;
            }

            $SupplierBase = SupplierBase::whereId($OrderBase['supplier_id'])->first();
            $SupplierBilling = new SupplierBilling();
            $SupplierBilling->supplier_id = $OrderBase['supplier_id'];
            $SupplierBilling->order_no = $OrderBase['order_no'];
            $SupplierBilling->trade_no = $out_trade_no;
            $SupplierBilling->in_out = TaBilling::in_income;
            $SupplierBilling->amount = $total_price_buying;
            $SupplierBilling->balance = $SupplierBase['amount'];
            $SupplierBilling->content = '售货进账';
            $SupplierBilling->state = TaBilling::state_nofund;
            $SupplierBilling->save();


            //平台利润
            $PlatformBilling = new PlatformBilling();
            $PlatformBilling->order_no = $OrderBase['order_no'];
            $PlatformBilling->trade_no = $out_trade_no;
            $PlatformBilling->in_out = TaBilling::in_income;;
            $PlatformBilling->amount = $OrderBase['amount_goods'] - $OrderBase['ta_amount'] - $OrderBase['guide_amount'] - $total_price_buying;
            $PlatformBilling->content = '平台利润';
            $PlatformBilling->state = TaBilling::state_nofund;;
            $PlatformBilling->save();

            //给咨询者发送信息
            if($OrderBase['guide_id'] > 0){
                $ip = '100.97.54.183';
                $type = PlatformSm::guideAmount;
                $OrderUser = UserBase::whereId($OrderBase['uid'])->first();
                $OrderUserMobile = substr($OrderUser['mobile'],0,3).'****'.substr($OrderUser['mobile'],7);
                $tpl_value = "【易游购】恭喜，".$OrderUserMobile."用户已购买成功，返利入账".$OrderBase['guide_amount']."元，请注意查收！";

                $GuideBase = GuideBase::whereId($OrderBase['guide_id'])->first();
                $UserBase = UserBase::whereId($GuideBase['uid'])->first();
                if(isset($UserBase['mobile']) && strlen($UserBase['mobile']) == 11 && $OrderBase['guide_amount'] > 0)
                {
                    GenController::getGuideAmountSendSms($UserBase['mobile'],$ip,$type,$tpl_value,$tpl_value);
                }
            }
        }
    }
}

