<?php

namespace App\Http\Controllers\Wx;

use App\Http\Controllers\Admin\CouponController;
use App\Models\CouponUser;
use App\Models\GoodsBase;
use App\Models\GoodsGift;
use App\Models\GuideBase;
use App\Models\GuideBilling;
use App\Models\OrderBase;
use App\Models\OrderGood;
use App\Models\OrderLog;
use App\Models\OrderWx;
use App\Models\PlatformBilling;
use App\Models\PlatformSm;
use App\Models\SupplierBase;
use App\Models\SupplierBilling;
use App\Models\SupplierExpress;
use App\Models\TaBase;
use App\Models\TaBilling;
use App\Models\TaGroup;
use App\Models\UserAddress;
use App\Models\UserBase;
use App\Models\UserCart;
use App\Models\GoodsSpec;
use App\Models\UserWx;
use App\Models\WxGuide;
use Illuminate\Http\Request;
use Log;
use Cookie;
use App\Http\Requests;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Payment\Order;



class OrderController extends WxController
{
    public function getCartGoods($id = 0)
    {
        $open_id = Cookie::get('openid');
        $UserCart = UserCart::whereOpenId($open_id)->whereIsSelected(1)->get();
        $data = self::formatCartData($UserCart);
        //地址
        $uid = UserWx::whereOpenId($open_id)->pluck('uid');
        //dd($uid);
        $UserAddress = '';
        if($uid != 0){
            $UserAddress = UserAddress::whereUid($uid)->whereIsDefault(1)->first();
            if(!empty($UserAddress)){
                $UserAddress->province = self::getCityName($UserAddress->province_id);
                $UserAddress->city = self::getCityName($UserAddress->city_id);
                $UserAddress->district = self::getCityName($UserAddress->district_id);
            }
        }
        //dd($data);
        return view('wx.order.cart',compact('data','UserAddress','id'));
    }

    //判断商品数量
    public function getCartNumGoods()
    {
        $open_id = Cookie::get('openid');
        $UserCart = UserCart::whereOpenId($open_id)->whereIsSelected(1)->get();
        //判断数量与总量关系
        foreach($UserCart as $usercar){
            $spec_num = GoodsSpec::whereId($usercar->spec_id)->first();
            $good_name =GoodsBase::whereId($spec_num->goods_id)->first();
            if($spec_num->num_limit != 0 && ($spec_num->num_limit < $usercar->num)){
                $tmp = $good_name->title.'的商品数量限购'.$spec_num->num_limit.'件';
                return response()->json(['ret' => 'no','info' => $tmp]);
            }
            if($spec_num->num < $usercar->num){
                $tmp = $good_name->title.'的商品数量仅剩'.$spec_num->num.'件';
                return response()->json(['ret' => 'no','info' => $tmp]);
            }
        }

        return response()->json(['ret' => 'order']);
    }

    public function postOrder(Request $request)
    {
        $open_id = Cookie::get('openid');
        //补充逻辑
        if(is_null($open_id)){
            if(env('APP_ENV') == 'local'){
                $open_id = 'o1-zuw6uMAPVZB5Oc-uQUcBiQw-Q';
            }else{
                $open_id = self::setWxAuth($request,$_SERVER['REQUEST_URI']);
            }
            if($open_id != ''){
                Cookie::queue('openid',$open_id);
            }
        }
        $data = $request->all();
        //dd($data);
        $returnWay = $request->input('returnWay',0);
        $supplier_id = $request->input('supplier_id',array());
        $receiver_id = $request->input('receiver_id',array());

        $UserWx = UserWx::whereOpenId($open_id)->first();
        $UserAddress = UserAddress::whereId($receiver_id)->whereUid($UserWx['uid'])->first();

        //补充逻辑地址信息是否存在
        if(empty($UserAddress)){
            return redirect('/cart');
        }

        $receiver_info = array();
        $receiver_info['id'] = $UserAddress['id'];
        $receiver_info['name'] = urlencode($UserAddress['name']);
        $receiver_info['mobile'] = $UserAddress['mobile'];
        $receiver_info['is_default'] = $UserAddress['is_default'];
        $receiver_info['province_id'] = $UserAddress['province_id'];
        $receiver_info['city_id'] = $UserAddress['city_id'];
        $receiver_info['district_id'] = $UserAddress['district_id'];
        $receiver_info['province'] = urlencode(self::getCityName($UserAddress['province_id']));
        $receiver_info['city'] = urlencode(self::getCityName($UserAddress['city_id']));
        $receiver_info['district'] = urlencode(self::getCityName($UserAddress['district_id']));
        $receiver_info['address'] = urlencode($UserAddress['address']);

        $order_sn = '';
        $order_no_array = array();
        $receiver_info = urldecode(json_encode($receiver_info));
        foreach($supplier_id as $k => $v){
            $buyer_message = $request->input('buyer_message_'.$v,array());
            $express_type = $request->input('express_type_'.$v,array());
            $amount_express_array = $request->input('express_amount',array());
            $couponId = $request->input('couponId',array());
            $coupon_amount = $request->input('coupon_amount',array());
            if($express_type == 2){
                $express_type = 0;
            }
            $UserCart = UserCart::whereSupplierId($v)->whereOpenId($open_id)->whereIsSelected(1)->orderBy('id','asc')->get();
            //补充逻辑返回再次提交订单时
            $amount_goods = 0;
            $ta_id = 0 ;
            $guide_id = 0 ;

            //v2.0
            foreach($UserCart as $vv){
                $GoodsSpec = GoodsSpec::whereId($vv['spec_id'])->first();
                $amount_goods = $amount_goods + ($GoodsSpec['price'] * $vv['num']);
            }

            $OrderBase = new OrderBase();
            $OrderBase->uid = $UserWx['uid'];
            $OrderBase->supplier_id = $v;
            $OrderBase->coupon_user_id = empty($couponId[$k]) ? 0: $couponId[$k];
            $OrderBase->amount_coupon = empty($coupon_amount[$k]) ? 0 : $coupon_amount[$k];
            $OrderBase->amount_express = $amount_express_array[$k];
            $OrderBase->amount_goods = $amount_goods;
            $OrderBase->amount_goods_origin = $amount_goods;
            $OrderBase->receiver_info = $receiver_info;
            $OrderBase->receiver_name = $UserAddress['name'];
            $OrderBase->receiver_mobile = $UserAddress['mobile'];
            $OrderBase->state = OrderBase::STATE_NO_PAY;
            $OrderBase->buyer_message = $buyer_message;
            $OrderBase->express_type = $express_type;
            $OrderBase->pay_type = OrderBase::Pay_TYPE_WX_JS;
            $OrderBase->save();
            $order_no = date("ymdHis") . sprintf("%03d", substr($OrderBase->id, -3));

            //以下逻辑是测试改版
            $guideInfo = WxGuide::whereOpenId($open_id)->whereState(WxGuide::STATE_YES)->first();
            if(is_null($guideInfo)){
                $guideInfo = UserWx::whereOpenId($open_id)->orderBy('id','desc')->first();
            }
            $guide_id = isset($guideInfo['guide_id']) ? $guideInfo['guide_id'] : 0;

            //ta_id
            if($guide_id > 0){
                $TaGroup = TaGroup::whereGuideId($guide_id)->whereState(TaGroup::STATE_START)->first();
                if(!is_null($TaGroup)){
                    $ta_id = $TaGroup['ta_id'];
                }
            }
            
            //补充逻辑
            if($ta_id == 0 && $guide_id > 0){
                $GuideBase = GuideBase::whereId($guide_id)->first();
                $ta_id = $GuideBase['ta_id'];
                Log::alert('补充逻辑ta_id='.$ta_id);
            }

            OrderBase::whereId($OrderBase->id)->update(array('order_no' => $order_no,'guide_id'=>$guide_id,'ta_id'=>$ta_id,'group_id'=>isset($TaGroup['id']) ? $TaGroup['id'] : 0));

            //更新订单号进入bind微信表
            WxGuide::whereOpenId($open_id)->whereState(WxGuide::STATE_YES)->update(array('order_no'=>$order_no));

            $order_sn = $order_no;
            $order_no_array[] = $order_no;
            self::reduceGoodsNum($order_no,$UserCart);
        }

        $OrderWx = new OrderWx();
        $OrderWx->uid = $UserWx['uid'];
        if(count($order_no_array) > 1){
            $order_sn = $order_sn.'000';
        }
        $OrderWx->order_sn = $order_sn;
        $OrderWx->order_no = implode(',',$order_no_array);
        $OrderWx->save();

        return redirect('/order/pay?ordersn='.$OrderWx->order_sn.'&id='.$returnWay.'&');
    }


    public function payOrder(Request $request){

        $orderSn = $request->input('ordersn','');
        $orderNo = $request->input('orderno','');
        $returnWay = $request->input('id',0);

        $data = array();
        $data['ordersn'] = $orderSn;
        if($orderSn == ''){
            $data['ordersn'] = $orderNo;
        }

        if($orderNo != ''){
            $orderNo = array($orderNo);
        }else if($orderSn != ''){
            $OrderWx = OrderWx::whereOrderSn($orderSn)->first();
            $orderNo = explode(',',$OrderWx['order_no']);
        }

        $OrderBase = OrderBase::whereIn('order_no',$orderNo)->get();

        $data['order_no'] = array();
        $data['amount'] = 0;
        foreach($OrderBase as $v){
            $data['order_no'][] = $v['order_no'];
            $data['amount'] = $data['amount'] + $v->amount_goods + $v->amount_express - $v->amount_coupon;
            $receiver_info = $v['receiver_info'];
            $UserWx = UserWx::whereUid($v['uid'])->orderBy('id','desc')->first();
        }

        $data['receiver_info'] = json_decode($receiver_info,true);

        $options = [
            'app_id'     => env('WX_APPID'),
            'app_secret' => env('WX_APPSECRET'),

            // payment
            'payment' => [
                'merchant_id' => env('WECHAT_PAYMENT_MERCHANT_ID'),
                'key'         => env('WECHAT_PAYMENT_KEY'),
            ],
        ];
        $app = new Application($options);
        $payment = $app->payment;

        //测试环境
        if(env('APP_ENV') == 'dev'){
            $data['amount'] = 0.01;
        }

        $open_id = Cookie::get('openid');
        //补充逻辑
        if(is_null($open_id)){
            $open_id = $UserWx['open_id'];
        }

        $attributes = [
            'trade_type'       => 'JSAPI', // JSAPI，NATIVE，APP...
            'body'             => '易游购订单:'.$data['ordersn'],
            'attach'           => $data['ordersn'],
            'out_trade_no'     => $data['ordersn'],
            'total_fee'        => $data['amount']*100, // 单位：分
            'notify_url'       => 'http://'.env('H5_DOMAIN').'/order/notify', // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            'openid'           => $open_id, // trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识，
            'time_start'       => date("YmdHis"),
            'time_expire'      => date("YmdHis",time() + 600),
        ];

        $order = new Order($attributes);

        $result = $payment->prepare($order);
        Log::alert('$payment->prepare'.print_r($result,true));
        $jsApiParameters = '';
        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
            $jsApiParameters = $payment->configForPayment($result->prepay_id);
        }
        Log::alert('$jsApiParameters'.print_r($jsApiParameters,true));


        return view('wx.order.pay',compact('data','returnWay','jsApiParameters'));
    }


    public function notifyPayOrder(){
        $options = [
            'app_id'     => env('WX_APPID'),
            'app_secret' => env('WX_APPSECRET'),

            // payment
            'payment' => [
                'merchant_id' => env('WECHAT_PAYMENT_MERCHANT_ID'),
                'key'         => env('WECHAT_PAYMENT_KEY'),
            ],
        ];

        $app = new Application($options);
        $coupon = array();
        $response = $app->payment->handleNotify(function($notify, $successful) use ($coupon){

            Log::alert('$notify'.print_r($notify,true));

            $order_sn = $notify->out_trade_no;

            $OrderWx = OrderWx::whereOrderSn($order_sn)->first();
            if(!is_null($OrderWx)){
                $order_no = explode(',',$OrderWx['order_no']);
                $OrderBase = OrderBase::whereIn('order_no',$order_no)->get();
            }else{
                $OrderBase = OrderBase::whereOrderNo($order_sn)->get();
            }

            foreach($OrderBase as $v){
                $OrderBase = OrderBase::whereId($v['id'])->first();

                if($OrderBase['state'] == OrderBase::STATE_NO_PAY){
                    //更新订单状态
                    OrderBase::whereId($v['id'])->update(array('state'=>OrderBase::STATE_PAYED,'amount_real'=>$notify->cash_fee/100));

                    //优惠券
                    if($v['coupon_user_id'] > 0){
                        CouponUser::whereId($v['coupon_user_id'])->update(['state' => 1,'used_time'=>date("Y-m-d H:i:s")]);
                    }

                    //log
                    $OrderLog = new OrderLog();
                    $OrderLog->order_no = $v['order_no'];
                    $OrderLog->action = '支付成功';
                    $OrderLog->content = json_encode(array('before_state'=>OrderBase::STATE_NO_PAY,'after_state'=>OrderBase::STATE_PAYED));
                    $OrderLog->save();


                    //记导游账单
                    $GuideBase = GuideBase::whereId($v['guide_id'])->first();
                    $UserBase = UserBase::whereId($GuideBase['uid'])->first();
                    $GuideBilling = new GuideBilling();
                    $GuideBilling->guide_id = $v['guide_id'];
                    $GuideBilling->uid = $v['uid'];
                    $GuideBilling->order_no = $v['order_no'];
                    $GuideBilling->trade_no = $notify->transaction_id;
                    $GuideBilling->in_out = GuideBilling::in_income;
                    if($v['guide_amount'] < 0){
                        $v['guide_amount'] = 0;
                    }
                    $GuideBilling->amount = $v['guide_amount'];
                    $GuideBilling->balance = $UserBase['amount'];
                    $GuideBilling->content = '返利收入';
                    $GuideBilling->state = GuideBilling::state_nofund;
                    $GuideBilling->group_id = $v['group_id'];
                    $GuideBilling->save();

                    //记旅行社账单
                    $TaBase = TaBase::whereId($v['ta_id'])->first();
                    $TaBilling = new TaBilling();
                    $TaBilling->ta_id = $v['ta_id'];
                    $TaBilling->order_no = $v['order_no'];
                    $TaBilling->trade_no = $notify->transaction_id;
                    $TaBilling->in_out = TaBilling::in_income;
                    $TaBilling->amount = $v['ta_amount'];
                    $TaBilling->balance = $TaBase['amount'];
                    $TaBilling->content = '返利收入';
                    $TaBilling->state = TaBilling::state_nofund;
                    $TaBilling->save();

                    //供应商账单
                    //计算进价
                    $OrderGood = OrderGood::whereOrderNo($v['order_no'])->whereIsGift(OrderGood::is_gift_no)->get();
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

                    $SupplierBase = SupplierBase::whereId($v['supplier_id'])->first();
                    $SupplierBilling = new SupplierBilling();
                    $SupplierBilling->supplier_id = $v['supplier_id'];
                    $SupplierBilling->order_no = $v['order_no'];
                    $SupplierBilling->trade_no = $notify->transaction_id;
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
                    $PlatformBilling->uid = $v['uid'];
                    $PlatformBilling->order_no = $v['order_no'];
                    $PlatformBilling->trade_no = $notify->transaction_id;
                    $PlatformBilling->in_out = TaBilling::in_income;;
                    $PlatformBilling->amount = $v['amount_goods'] - $v['ta_amount'] - $v['guide_amount'] - $total_price_buying;
                    $PlatformBilling->content = '平台利润';
                    $PlatformBilling->state = TaBilling::state_nofund;;
                    $PlatformBilling->save();

                    //删除购物车
                    $OrderGoods = OrderGood::whereOrderNo($v['order_no'])->get();
                    $UserWx = UserWx::whereUid($v['uid'])->first();
                    foreach($OrderGoods as $OrderGood){
                        UserCart::whereOpenId($UserWx['open_id'])->whereGoodsId($OrderGood['goods_id'])->whereSpecId($OrderGood['spec_id'])->delete();
                    }

                    //删除bind关联
                    WxGuide::whereOrderNo($v['order_no'])->update(array('state'=>WxGuide::STATE_DEL));

                    //给咨询者发送信息
                    if($OrderBase['guide_id'] > 0){
                        $ip = 0;
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

            return true; // 返回处理完成
        });
        return $response;
    }



    private function judgeGoodsState($goods_arr){
        foreach ($goods_arr as $v) {
            $goods = GoodsBase::whereId($v['goods_id'])->first();
            if($goods['state'] != GoodsBase::state_online){
                return strval($v['spec_id']);
            }
        }
        return '0';
    }

    private function judgeGoodsNum($goods_arr){
        foreach ($goods_arr as $v) {
            $GoodsSpec = GoodsSpec::whereId($v['spec_id'])->first();
            if($GoodsSpec['num'] < $v['num']){
                return strval($v['spec_id']);
            }
        }
        return '0';
    }

    private function reduceGoodsNum($order_no, $goods_arr){
        $taAmount = 0;
        $guideAmount = 0;
        $hasGift = 0;
        foreach($goods_arr as $goods){
            $OrderGood = new OrderGood();
            $OrderGood->order_no = $order_no;
            $OrderGood->goods_id = $goods['goods_id'];
            $OrderGood->spec_id = $goods['spec_id'];
            $GoodsTitle = GoodsBase::whereId($goods['goods_id'])->first();
            $GoodsSpec = GoodsSpec::whereId($goods['spec_id'])->first();
            $OrderGood->price = isset($GoodsSpec['price']) ? $GoodsSpec['price'] : 0;
            $OrderGood->num = $goods['num'];
            $OrderGood->goods_title = $GoodsTitle['title'];
            $OrderGood->spec_name = $GoodsSpec['name'];
            $OrderGood->price_buying = $GoodsSpec['price_buying'];
            $OrderGood->price_market = $GoodsSpec['price_market'];
            $OrderGood->platform_fee = $GoodsSpec['platform_fee'];
            $OrderGood->guide_rate = $GoodsSpec['guide_rate'];
            $OrderGood->travel_agency_rate = $GoodsSpec['travel_agency_rate'];

            $OrderGood->save();

            $GoodsSpec = GoodsSpec::whereId($goods['spec_id'])->first();
            $GoodsSpec->num = $GoodsSpec->num - $goods['num'];
            $GoodsSpec->num_sold = $GoodsSpec->num_sold + $goods['num'];
            $GoodsSpec->save();

            //赠品逻辑
            $GoodsGift = GoodsGift::whereGoodsId($goods['goods_id'])->get();

            foreach($GoodsGift as $gift){
                $GoodsSpecGift = GoodsSpec::whereId($gift['spec_id'])->first();

                $OrderGoodGift = new OrderGood();
                $OrderGoodGift->order_no = $order_no;
                $OrderGoodGift->goods_id = $gift['gift_id'];
                $OrderGoodGift->spec_id = $gift['spec_id'];
                $OrderGoodGift->price = 0;
                $OrderGoodGift->num = 1;
                $OrderGoodGift->is_gift = 1;
                $OrderGoodGift->price_buying = $GoodsSpecGift['price_buying'];
                $OrderGoodGift->price_market = $GoodsSpecGift['price_market'];
                $OrderGoodGift->platform_fee = $GoodsSpecGift['platform_fee'];
                $OrderGoodGift->guide_rate = $GoodsSpecGift['guide_rate'];
                $OrderGoodGift->travel_agency_rate = $GoodsSpecGift['travel_agency_rate'];
                $OrderGoodGift->save();

                $GoodsSpecGift->num = $GoodsSpecGift->num - 1;
                $GoodsSpecGift->num_sold = $GoodsSpecGift->num_sold + 1;
                $GoodsSpecGift->save();

                $GoodsBaseGift = GoodsBase::whereId($gift['gift_id'])->first();
                $GoodsBaseGift->num = $GoodsBaseGift->num - 1;
                $GoodsBaseGift->num_sold = $GoodsBaseGift->num_sold + 1;
                $GoodsBaseGift->save();

                OrderGood::whereId($OrderGoodGift->id)->update(array('goods_title'=>$GoodsBaseGift['title'],'spec_name'=>$GoodsSpecGift['name']));

                $hasGift = 1;
            }

            //更新数量
            $GoodsBase = GoodsBase::whereId($goods['goods_id'])->first();
            $GoodsBase->num = $GoodsBase->num - $goods['num'];
            $GoodsBase->num_sold = $GoodsBase->num_sold + $goods['num'];
            $GoodsBase->save();

            //返利
            $taAmount = $taAmount + (($OrderGood->price * $GoodsSpec['travel_agency_rate'] /100) * $goods['num']);
            $guideAmount = $guideAmount + (($OrderGood->price * $GoodsSpec['guide_rate'] /100) * $goods['num']);

        }

        OrderBase::whereOrderNo($order_no)->update(array('ta_amount' => $taAmount,'guide_amount' => $guideAmount,'has_gift'=>$hasGift));
    }

    public function OrderCoupon(Request $request)
    {
        $ordersn = $request->input('ordersn');
        $orderNos = OrderWx::whereOrderSn($ordersn)->pluck('order_no');
        if(empty($orderNos)){
            $count = 1;
        }else{
            $orderNos = explode(",",$orderNos);
            $count = CouponUser::whereIn('send_source',$orderNos)->count();
        }
        return $count;
    }

}
