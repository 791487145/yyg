<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Admin\BaseController;
use App\Models\GoodsSpec;
use App\Models\OrderReturn;
use App\Models\GuideBase;
use App\Models\OrderLog;
use App\Models\OrderWx;
use App\Models\PlatformBilling;
use App\Models\OrderReturnLog;
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
use App\Http\Controllers\GenController;


class PayNoSignController extends GenController{

    public function callBack(Request $request)
    {
        $all = $request->input();
        Log::alert('PaycallBack:' . print_r($all, true));

        $action = isset($all['type']) ? $all['type']:'';
        $order_no = isset($all['data']['object']['order_no']) ? $all['data']['object']['order_no'] : '';
        $amount = isset($all['data']['object']['amount']) ? $all['data']['object']['amount'] : '';
        $out_trade_no = isset($all['data']['object']['transaction_no']) ? $all['data']['object']['transaction_no'] : '';

        $OrderWx = OrderWx::whereOrderSn($order_no)->first();
        if(!is_null($OrderWx)){
            $order_no = explode(',',$OrderWx['order_no']);
            $OrderBases = OrderBase::whereIn('order_no',$order_no)->get();
        }else{
            $OrderBases = OrderBase::whereOrderNo($order_no)->get();
        }

        if($OrderBases->isEmpty()){
            $OrderBases = OrderBase::whereOrderNo($order_no.'1')->get();
        }

        if($action == 'charge.succeeded' && count($OrderBases) > 0){

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
                $SupplierBilling->coupon_amount = $OrderBase->amount_coupon;
                $SupplierBilling->express_amount = $OrderBase->amount_express;
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
                    $ip = $request->getClientIp();
                    $type = PlatformSm::guideAmount;
                    $OrderUser = UserBase::whereId($OrderBase['uid'])->first();
                    $OrderUserMobile = substr($OrderUser['mobile'],0,3).'****'.substr($OrderUser['mobile'],7);
                    $tpl_value = "【易游购】恭喜，".$OrderUserMobile."用户已购买成功，返利入账".$OrderBase['guide_amount']."元，请注意查收！";

                    $GuideBase = GuideBase::whereId($OrderBase['guide_id'])->first();
                    $UserBase = UserBase::whereId($GuideBase['uid'])->first();
                    if(isset($UserBase['mobile']) && strlen($UserBase['mobile']) == 11 && $OrderBase['guide_amount'] > 0)
                    {
                        self::getGuideAmountSendSms($UserBase['mobile'],$ip,$type,$tpl_value,$tpl_value);
                    }
                }

                //加优惠券
                //self::sendCouponToUser($OrderBase['supplier_id'],$OrderBase['uid'],$OrderBase['order_no']);

            }
        }

        if($action == 'refund.succeeded'){
            $all['content'] = urlencode('退款成功，收到退款金额'.$amount/100);
            $refundId = isset($all['data']['object']['id']) ? $all['data']['object']['id'] : '';
            $orderReturnSn = OrderReturnLog::where('content','like','%'.$refundId.'%')->pluck('return_no');
            $orderReturnOrderNo = substr($orderReturnSn,1);
            OrderReturn::whereOrderNo($orderReturnOrderNo)->update(['state' => OrderReturn::STATE_SUCCESS, 'amount' => $amount/100]);
            $orderReturn = OrderReturn::whereOrderNo($orderReturnOrderNo)->first();
            $userInfos = UserBase::whereId($orderReturn->uid)->first();
            $mobile = $userInfos->mobile;
            Log::alert($mobile);
            $ip = ip2long($request->getClientIp());
            $type = OrderReturn::STATE_PAIED;
            $text = "【易游购】您好， 您的订单$orderReturnOrderNo  退款金额：" . $amount/100 . "，已成功退回至您的支付账户中，具体到账时间以银行到账时间为准，请及时注意查收！";
            $content = ['content' => "退款成功，收到退款金额$amount", 'sms' => $text];
            $content['content'] = urlencode($content['content']);
            $content['sms'] = urlencode($content['sms']);
            $content = urldecode(json_encode($content));
            $orderReturnLog = new OrderReturnLog();
            $orderReturnLog->return_no = $orderReturnSn;
            $orderReturnLog->uid = $orderReturn->uid;
            $orderReturnLog->action = '成功退款';
            $orderReturnLog->content = urldecode(json_encode($all));
            $orderReturnLog->save();
            BaseController::platformSendSms($mobile, $ip, $type, $text);
        }

        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' =>strval(1));
        return response()->json($result);
    }

}

