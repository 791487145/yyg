<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admin\BaseController;
use App\Models\CouponUser;
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
use GuzzleHttp\Psr7\Request;
use Log;
use Illuminate\Console\Command;

class Order extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'order state change';

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

        //将过期的验证码状态变为-1
        self::toDisabledSmsCode();

        //关闭订单
        self::closeOrder();

        //系统时间到改成已完成订单
        self::finishOrder();

        //更新团状态 字段
        self::updateTaGroupState();

        //未付款订单发送提醒付款短信
        self::orderNoPaySendSms();
    }


    Static function closeOrder()
    {
        $time = date("Y-m-d H:i:s", time()-30*60);
        $OrderBase = OrderBase::whereState(OrderBase::STATE_NO_PAY)->where('created_at', '<', $time)->get();

        foreach($OrderBase as $v){
            $v->update(['state'=>OrderBase::STATE_CANCEL_SYSTEM]);
            $OrderGood = OrderGood::whereOrderNo($v->order_no)->get();
            foreach($OrderGood as $vv){
                $GoodsSpec = GoodsSpec::whereId($vv['spec_id'])->first();
                $GoodsSpec->num = $GoodsSpec->num + $vv['num'];
                $GoodsSpec->num_sold = $GoodsSpec->num_sold - $vv['num'];
                $GoodsSpec->save();

                $GoodsBase = GoodsBase::whereId($vv['goods_id'])->first();
                $GoodsBase->num = $GoodsSpec->num + $vv['num'];
                $GoodsBase->num_sold = $GoodsSpec->num_sold - $vv['num'];
                $GoodsBase->save();
            }
            if($v->coupon_user_id > 0){
                CouponUser::whereId($v->coupon_user_id)->update(array('state'=>CouponUser::state_unused));
            }

        }
        return true;
    }

    static function orderNoPaySendSms(){
        $time = date("Y-m-d H:i:s", time()-15*60);
        $OrderBase = OrderBase::whereState(OrderBase::STATE_NO_PAY)->where('created_at', '<', $time)->get();
        foreach($OrderBase as $v){
            $userInfo = UserBase::whereId($v->uid)->first();
            $mobile = $userInfo->mobile;
            $platformSms = PlatformSm::whereType(PlatformSm::ORDER_NOPAY_SMS)->whereMobile($mobile)->where('created_at','>',$time)->first();
            if(is_null($platformSms)){
                $ip = '0.0.0.0';
                $type = PlatformSm::ORDER_NOPAY_SMS;
                $content = '【易游购】'.$userInfo->nick_name.'用户，您好。您提交的订单（编号'.$v->order_no.')将在15分钟之后超时取消关闭，请及时在“我的-买入订单-待付款”完成付款。';
                BaseController::platformSendSms($mobile,$ip,$type,$content);
            }
        }
        return true;
    }

    Static function finishOrder()
    {
        $time = date("Y-m-d H:i:s", time()-7*24*60*60);
        $OrderBase = OrderBase::whereState(OrderBase::STATE_SEND)->where('express_time', '>', '0000-00-00 00:00:00')->where('express_time', '<', $time)->get();

        foreach($OrderBase as $v){
            Log::alert('console.log'.$v['id'].'||'.$v['order_no']);
            $num = OrderReturn::whereOrderNo($v['order_no'])->whereIn('state',array(OrderReturn::STATE_NO_CHECK,OrderReturn::STATE_NO_REFUND))->count();
            //有售后就直接跳掉
            if($num > 0){continue; }

            //更新订单状态
            OrderBase::whereId($v['id'])->update(['state'=>OrderBase::STATE_FINISHED]);

            //导游分成到账
            $GuideBilling = GuideBilling::whereOrderNo($v['order_no'])->first();
            $GuideBilling->state = GuideBilling::state_fund;
            $GuideBilling->save();

            $GuideBase = GuideBase::whereId($GuideBilling['guide_id'])->first();
            $UserBase = UserBase::whereId($GuideBase['uid'])->first();
            $amount = $GuideBilling->amount - $GuideBilling->return_amount;
            if($amount > 0){
                $UserBase->amount = $UserBase->amount + $amount;
                $UserBase->save();
            }

            //旅行社分成到账
            $TaBilling = TaBilling::whereOrderNo($v['order_no'])->first();
            if(!empty($TaBilling) && $TaBilling->ta_id !== 0) {
                $TaBilling->state = TaBilling::state_fund;
                $TaBilling->save();

                $TaBase = TaBase::whereId($TaBilling['ta_id'])->first();
                $amount = $TaBilling->amount - $TaBilling->return_amount;
                if ($amount > 0) {
                    $TaBase->amount = $TaBase->amount + $amount;
                    $TaBase->save();
                }
            }

            //供应商到账
            $SupplierBilling = SupplierBilling::whereOrderNo($v['order_no'])->first();
            $SupplierBilling->state = TaBilling::state_fund;
            $SupplierBilling->save();

            $SupplierBase = SupplierBase::whereId($SupplierBilling['supplier_id'])->first();
            $amount = $SupplierBilling->amount - $SupplierBilling->return_amount;
            if($amount > 0) {
                $SupplierBase->amount = $SupplierBase->amount + $amount;
                $SupplierBase->save();
            }

            //平台到账

            $PlatformBilling = PlatformBilling::whereOrderNo($v['order_no'])->first();
            $PlatformBilling->state = TaBilling::state_fund;
            $PlatformBilling->save();


            $OrderLog = new OrderLog();
            $OrderLog->action = '系统完成订单';
            $OrderLog->content  = json_encode(array('before_state'=>OrderBase::STATE_SEND,'after_state'=>OrderBase::STATE_FINISHED));
            $OrderLog->save();
        }
        return true;
    }

    static function toDisabledSmsCode()
    {
        $time = date("Y-m-d H:i:s", time()-5*60);
        SmsVerificationCode::where('created_at', '<', $time)->whereIsValid(SmsVerificationCode::IS_VALID_YES)->update(array('is_valid'=>SmsVerificationCode::IS_VALID_NO));
        TaSm::where('created_at', '<', $time)->whereIsValid(SmsVerificationCode::IS_VALID_YES)->update(array('is_valid'=>SmsVerificationCode::IS_VALID_NO));
        SupplierSm::where('created_at', '<', $time)->whereIsValid(SmsVerificationCode::IS_VALID_YES)->update(array('is_valid'=>SmsVerificationCode::IS_VALID_NO));
        PlatformSm::where('created_at', '<', $time)->whereIsValid(SmsVerificationCode::IS_VALID_YES)->update(array('is_valid'=>SmsVerificationCode::IS_VALID_NO));
        return true;
    }

    static function updateTaGroupState(){
        $time = date('Y-m-d H:i:s');
        $TaGroup = TaGroup::whereState(0)->where('start_time','<',$time)->where('end_time','>',$time)->get();
        foreach($TaGroup as $v){
            TaGroup::whereId($v['id'])->update(array('state'=>TaGroup::STATE_YES_START));
        }

        $TaGroup = TaGroup::where('end_time','<',$time)->get();
        foreach($TaGroup as $v){
            TaGroup::whereId($v['id'])->update(array('state'=>TaGroup::STATE_END));
        }

    }


    static function updateTestOrder(){
        $OrderBase = OrderBase::whereState(OrderBase::STATE_TEST)->get();
        foreach($OrderBase as $order){
            GuideBilling::whereOrderNo($order['order_no'])->whereState(GuideBilling::state_nofund)->update(array('state'=>GuideBilling::state_del));
            SupplierBilling::whereOrderNo($order['order_no'])->whereState(GuideBilling::state_nofund)->update(array('state'=>GuideBilling::state_del));
            TaBilling::whereOrderNo($order['order_no'])->whereState(GuideBilling::state_nofund)->update(array('state'=>GuideBilling::state_del));
            PlatformBilling::whereOrderNo($order['order_no'])->whereState(GuideBilling::state_nofund)->update(array('state'=>GuideBilling::state_del));
        }
    }




}
