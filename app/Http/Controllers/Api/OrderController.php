<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Admin\CouponController;
use App\Models\CouponUser;
use App\Models\GoodsGift;
use App\Models\OrderWx;
use App\Models\PlatformBilling;
use App\Models\UserCart;
use Log;
use Pingpp\Charge;
use Pingpp\Pingpp;
use App\Models\GoodsBase;
use App\Models\GoodsSpec;
use App\Models\GuideBase;
use App\Models\OrderBase;
use App\Models\OrderGood;
use App\Models\OrderLog;
use App\Models\OrderPay;
use App\Models\OrderReturn;
use App\Models\OrderReturnImage;
use App\Models\TaGroup;
use App\Models\GuideBilling;
use App\Models\SmsVerificationCode;
use App\Models\SupplierBase;
use App\Models\SupplierBilling;
use App\Models\TaBase;
use App\Models\TaBilling;
use App\Models\UBase;
use App\Models\UserBase;
use Illuminate\Http\Request;
use App\Http\Controllers\SignController;


class OrderController extends SignController
{

    /**
     * @SWG\Post(path="/v1/order",
     *   tags={"order"},
     *   summary="订单生成",
     *   description="",
     *   operationId="order",
     *  produces={"application/json"},
     *   @SWG\Parameter(
     *     name="uid",
     *     in="query",
     *     description="(签名参数)用户id",
     *     required=true,
     *     type="integer",
     *     @SWG\Schema(ref="uid")
     *   ),
     *   @SWG\Parameter(
     *     name="timestamp",
     *     in="query",
     *     description="(签名参数)时间戳",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="timestamp")
     *   ),
     *   @SWG\Parameter(
     *     name="sign",
     *     in="query",
     *     description="(签名参数)签名",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="sign")
     *   ),
     *   @SWG\Parameter(
     *     name="amount_goods",
     *     in="query",
     *     description="商品金额",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="amount_goods")
     *   ),
     *   @SWG\Parameter(
     *     name="amount_express",
     *     in="query",
     *     description="快递金额",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="amount_express")
     *   ),
     *   @SWG\Parameter(
     *     name="express_type",
     *     in="query",
     *     description="提货方式",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="0.包邮，1.自提")
     *   ),
     *  @SWG\Parameter(
     *     name="param_sign",
     *     in="query",
     *     description="参数签名",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="param_sign")
     *   ),
     *   @SWG\Parameter(
     *     name="buyer_message",
     *     in="query",
     *     description="买家留言",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="buyer_message")
     *   ),
     *  @SWG\Parameter(
     *     name="coupon_user_id",
     *     in="query",
     *     description="优惠券id",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="coupon_user_id")
     *   ),
     *   @SWG\Parameter(
     *     name="goods",
     *     in="query",
     *     description="订单商品",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="[{'goods_id':'2','spec_id':'2','price':'10.00','num':'10'},...]")
     *   ),
     *  @SWG\Parameter(
     *     name="receiver_info",
     *     in="query",
     *     description="收货人地址",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="{'id':'137','district_id':'22','mobile':'111347256625','province':'天津','city_id':'21','address':'测试00000001','city':'天津市','district':'和平区','is_default':'1','name':'张三','province_id':'20'}")
     *   ),
     *
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function addOrder(Request $request)
    {
        $uid = $request->input('uid', 0);
        $amount_goods = $request->input('amount_goods', 0);
        $amount_express = $request->input('amount_express', 0);
        $coupon_user_id = $request->input('coupon_user_id', 0);
        $express_type = $request->input('express_type', 0);
        $receiver_info = $request->input('receiver_info', '');
        $goods = $request->input('goods', '');
        $param_sign = $request->input('param_sign', '');
        $buyer_message = $request->input('buyer_message', '');

        $param_md5 = md5($uid.$amount_goods.$amount_express.$receiver_info.$goods);

        Log::alert('$param:' . $uid.$amount_goods.$amount_express.$receiver_info.$goods);

        Log::alert('$param_md5:' . $param_md5);

        Log::alert('orderAdd request:' . print_r($request->input(), true));

        $goods_arr = json_decode($goods, true);
        $receiver_arr = json_decode($receiver_info, true);


        //参数不正确
        if ( $param_md5 != $param_sign){
            return response()->json(array('ret' => self::RET_FAIL, 'msg' => self::PARAMETER_ERROR, 'data' => (object)array()));
        }

        if ($amount_goods == 0 || $goods == '' || empty($goods_arr)) {
            return response()->json(array('ret' => self::RET_FAIL, 'msg' => self::PARAMETER_ERROR, 'data' => (object)array()));
        }


        //预判商品状态
        $spec_id = self::judgeGoodsState($goods_arr);
        if(intval($spec_id) > 0){
            return response()->json(array('ret' => self::RET_FAIL, 'msg' => self::ORDER_GOODS_STATE_ERROR, 'data' =>array('spec_id'=>$spec_id)));
        }

        //预判商品库存
        $spec_id = self::judgeGoodsNum($goods_arr);
        if(intval($spec_id) > 0){
            return response()->json(array('ret' => self::RET_FAIL, 'msg' => self::ORDER_GOODS_NUM_ERROR, 'data' => array('spec_id'=>$spec_id)));
        }

        //预判超出限够
        $spec_id = self::overGoodsLimitNum($goods_arr);
        if(intval($spec_id) > 0){
            return response()->json(array('ret' => self::RET_FAIL, 'msg' => self::ORDER_GOODS_LIMIT_NUM_OVER, 'data' => array('spec_id'=>$spec_id)));
        }

        $amount_coupon = 0;
        if($coupon_user_id > 0){
            $CouponUser = CouponUser::whereId($coupon_user_id)->first();
            $amount_coupon = $CouponUser['amount_coupon'];
            //优惠券超出
            if($CouponUser['amount_order'] > $amount_goods){
                return response()->json(array('ret' => self::RET_FAIL, 'msg' => '优惠商品金额不足，不能使用优惠券', 'data' =>(object)array()));
            }
        }


        $OrderBase = new OrderBase();
        $OrderBase->uid = $uid;
        $OrderBase->amount_goods = $amount_goods;
        $OrderBase->amount_goods_origin = $amount_goods;
        $OrderBase->amount_express = $amount_express;
        $OrderBase->amount_coupon = $amount_coupon;
        $OrderBase->coupon_user_id = $coupon_user_id;
        $OrderBase->express_type = $express_type;
        $OrderBase->receiver_info = $receiver_info;
        $OrderBase->receiver_name = $receiver_arr['name'];
        $OrderBase->receiver_mobile = $receiver_arr['mobile'];
        $OrderBase->buyer_message = $buyer_message;
        $OrderBase->state = OrderBase::STATE_NO_PAY;
        $OrderBase->save();
        $GuideTaId = self::getGuideTaId($uid);
        $order_no = date("ymdHis") . sprintf("%03d", substr($OrderBase->id, -3));
        OrderBase::whereId($OrderBase->id)->update(array('order_no' => $order_no,'guide_id'=>$GuideTaId['guide_id'],'ta_id'=>$GuideTaId['ta_id'],'group_id'=>$GuideTaId['group_id']));

        //减优惠券
        if($OrderBase->coupon_user_id > 0){
            CouponUser::whereId($OrderBase->coupon_user_id)->whereUid($uid)->update(array('state'=>CouponUser::state_used));
        }

        //减库存
        self::reduceGoodsNum($order_no, $goods_arr,$uid);

        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => array('id' => strval($OrderBase->id),'order_no'=>strval($order_no)));
        return response()->json($result);
    }

    /**
     * @SWG\Post(path="/v1/order/multi",
     *   tags={"order"},
     *   summary="购物车订单生成",
     *   description="",
     *   operationId="order",
     *  produces={"application/json"},
     *   @SWG\Parameter(
     *     name="uid",
     *     in="query",
     *     description="(签名参数)用户id",
     *     required=true,
     *     type="integer",
     *     @SWG\Schema(ref="uid")
     *   ),
     *   @SWG\Parameter(
     *     name="timestamp",
     *     in="query",
     *     description="(签名参数)时间戳",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="timestamp")
     *   ),
     *   @SWG\Parameter(
     *     name="sign",
     *     in="query",
     *     description="(签名参数)签名",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="sign")
     *   ),
     *   @SWG\Parameter(
     *     name="orders",
     *     in="body",
     *     description="多个订单所有信息 json格式",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="orders")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function addMultiOrder(Request $request)
    {
        $uid = $request->input('uid', 0);
        $supplier_info = $request->input('orders', 0);
        $supplier_arr = json_decode($supplier_info, true);
        Log::alert('orderAdd request:' . print_r($request->input(), true));
        //预判断
        foreach($supplier_arr as $supplier){
            $amount_goods = $supplier['amount_goods'];
            $amount_express = $supplier['amount_express'];
            $receiver_info = $supplier['receiver_info'];
            $goods = $supplier['goods'];
            $coupon_user_id = isset($supplier['coupon_user_id']) ? $supplier['coupon_user_id'] : 0;
            $param_sign = $supplier['param_sign'];

            $receiver_info['name'] = urlencode($receiver_info['name']);
            $receiver_info['province'] = urlencode($receiver_info['province']);
            $receiver_info['city'] = urlencode($receiver_info['city']);
            $receiver_info['district'] = urlencode($receiver_info['district']);
            $receiver_info['address'] = urlencode($receiver_info['address']);

            Log::alert('md5:' . $uid.$amount_goods.$amount_express.urldecode(json_encode($receiver_info)).json_encode($goods));

            $param_md5 = md5($uid.$amount_goods.$amount_express.urldecode(json_encode($receiver_info)).json_encode($goods));

            //参数不正确
            if ( $param_md5 != $param_sign){
                return response()->json(array('ret' => self::RET_FAIL, 'msg' => self::PARAMETER_ERROR, 'data' => (object)array()));
            }

            if ($amount_goods == 0 || empty($goods)) {
                return response()->json(array('ret' => self::RET_FAIL, 'msg' => self::PARAMETER_ERROR, 'data' => (object)array()));
            }

            //预判商品状态
            $spec_id = self::judgeGoodsState($goods);
            if(intval($spec_id) > 0){
                return response()->json(array('ret' => self::RET_FAIL, 'msg' => self::ORDER_GOODS_STATE_ERROR, 'data' =>array('spec_id'=>$spec_id)));
            }

            //预判商品库存
            $spec_id = self::judgeGoodsNum($goods);
            if(intval($spec_id) > 0){
                return response()->json(array('ret' => self::RET_FAIL, 'msg' => self::ORDER_GOODS_NUM_ERROR, 'data' => array('spec_id'=>$spec_id)));
            }

            //预判超出限够
            $spec_id = self::overGoodsLimitNum($goods);
            if(intval($spec_id) > 0){
                return response()->json(array('ret' => self::RET_FAIL, 'msg' => self::ORDER_GOODS_LIMIT_NUM_OVER, 'data' => array('spec_id'=>$spec_id)));
            }

            //优惠券超出
            if($coupon_user_id > 0){
                $CouponUser = CouponUser::whereId($coupon_user_id)->first();
                if($CouponUser['amount_order'] > $amount_goods){
                    return response()->json(array('ret' => self::RET_FAIL, 'msg' => '优惠商品金额不足，不能使用优惠券', 'data' =>(object)array()));
                }
            }

        }

        $order_no_arr = array();
        foreach($supplier_arr as $supplier){
            $amount_goods = $supplier['amount_goods'];
            $amount_express = $supplier['amount_express'];
            $coupon_user_id = isset($supplier['coupon_user_id']) ? $supplier['coupon_user_id'] : 0;
            $express_type = $supplier['express_type'];
            $receiver_info = $supplier['receiver_info'];
            $goods = $supplier['goods'];
            $buyer_message = $supplier['buyer_message'];

            $receiver_info['name'] = urlencode($receiver_info['name']);
            $receiver_info['province'] = urlencode($receiver_info['province']);
            $receiver_info['city'] = urlencode($receiver_info['city']);
            $receiver_info['district'] = urlencode($receiver_info['district']);
            $receiver_info['address'] = urlencode($receiver_info['address']);

            $amount_coupon = 0;
            if($coupon_user_id > 0){
                $CouponUser = CouponUser::whereId($coupon_user_id)->first();
                $amount_coupon = $CouponUser['amount_coupon'];
            }

            $OrderBase = new OrderBase();
            $OrderBase->uid = $uid;
            $OrderBase->amount_goods = $amount_goods;
            $OrderBase->amount_goods_origin = $amount_goods;
            $OrderBase->amount_express = $amount_express;
            $OrderBase->amount_coupon = $amount_coupon;
            $OrderBase->coupon_user_id = $coupon_user_id;
            $OrderBase->express_type = $express_type;
            $OrderBase->receiver_info = urldecode(json_encode($receiver_info));
            $OrderBase->receiver_name = urldecode($receiver_info['name']);
            $OrderBase->receiver_mobile = $receiver_info['mobile'];
            $OrderBase->buyer_message = $buyer_message;
            $OrderBase->state = OrderBase::STATE_NO_PAY;
            $OrderBase->save();
            $GuideTaId = self::getGuideTaId($uid);
            $order_no = date("ymdHis") . sprintf("%03d", substr($OrderBase->id, -3));
            OrderBase::whereId($OrderBase->id)->update(array('order_no' => $order_no,'guide_id'=>$GuideTaId['guide_id'],'ta_id'=>$GuideTaId['ta_id'],'group_id'=>$GuideTaId['group_id']));

            //减优惠券
            if($OrderBase->coupon_user_id > 0){
                CouponUser::whereId($OrderBase->coupon_user_id)->whereUid($uid)->update(array('state'=>CouponUser::state_used));
            }

            //减库存
            self::reduceGoodsNum($order_no, $goods, $uid);
            $order_no_arr[] = $order_no;
        }
        $OrderWx = new OrderWx();
        $OrderWx->uid = $uid;
        if(count($order_no_arr) > 1){
            $order_no = $order_no.'000';
        }
        $OrderWx->order_sn = $order_no;
        $OrderWx->order_no = implode(',',$order_no_arr);
        $OrderWx->save();
        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => array('id' => strval($OrderBase->id),'order_no'=>strval($order_no)));
        return response()->json($result);
    }



    /**
     * @SWG\Post(path="/v1/order/{order_no}",
     *   tags={"order"},
     *   summary="订单支付",
     *   description="",
     *   operationId="payOrder",
     *  produces={"application/json"},
     *   @SWG\Parameter(
     *     name="uid",
     *     in="query",
     *     description="(签名参数)用户id",
     *     required=true,
     *     type="integer",
     *     @SWG\Schema(ref="uid")
     *   ),
     *   @SWG\Parameter(
     *     name="timestamp",
     *     in="query",
     *     description="(签名参数)时间戳",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="timestamp")
     *   ),
     *   @SWG\Parameter(
     *     name="sign",
     *     in="query",
     *     description="(签名参数)签名",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="sign")
     *   ),
     *   @SWG\Parameter(
     *     name="pay_type",
     *     in="query",
     *     description="支付方式（1支付宝，2微信支付）",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="pay_type")
     *   ),
     *   @SWG\Parameter(
     *     name="order_no",
     *     in="path",
     *     description="订单编号",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="order_no")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function payOrder(Request $request,$order_no){

        $uid = $request->input('uid',0);
        $pay_type = $request->input('pay_type',0);

        //支付
        $OrderBase = OrderBase::whereOrderNo($order_no)->whereUid($uid)->first();
        if(!is_null($OrderBase) && $OrderBase->state == OrderBase::STATE_NO_PAY){

            //二次微信支付 订单号去重
            $OrderWx = OrderWx::whereOrderSn($order_no)->first();
            $order_no_origin = $order_no;
            if($OrderBase['pay_type'] == 2 && $pay_type == 2){
                $order_no = $order_no.'1';
                $OrderBase->order_no = $order_no;
                if(isset($OrderWx->order_sn)){
                    $OrderWx->order_sn = $order_no;
                    $OrderWx->order_no = str_replace($order_no_origin,$order_no,$OrderWx->order_no);
                    $OrderWx->save();

                    CouponUser::whereSendSource($order_no_origin)->update(array('send_source'=>$order_no));
                }
                OrderGood::whereOrderNo($order_no_origin)->update(array('order_no'=>$order_no));
            }


            $OrderBase->pay_type = $pay_type;
            $OrderBase->save();

            $pay_info = self::pingPPChargeCreate($order_no,$OrderBase);
            if(!empty($pay_info)){
                $OrderPay = new OrderPay();
                $OrderPay->order_no = $order_no;
                $OrderPay->pay_info = json_encode($pay_info);
                $OrderPay->save();
            }

            if($OrderBase->coupon_user_id > 0){
                CouponUser::whereId($OrderBase->coupon_user_id)->whereUid($uid)->update(array('state'=>CouponUser::state_used));
            }

            $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => array('id' => strval($OrderBase->id),'order_no'=>strval($order_no),'pay_info'=>$pay_info));
        }else{
            $id = isset($OrderBase->id) ? $OrderBase->id : 0;
            $result = array('ret' => self::RET_FAIL, 'msg' => '支付失败', 'data' => array('id' => strval($id),'order_no'=>strval($order_no),'pay_info'=>(object)array()));
        }
        Log::alert('$result'.print_r($result,true));
        return response()->json($result);
    }

    /**
     * @SWG\Post(path="/v1/order/multi_pay",
     *   tags={"order"},
     *   summary="多订单支付",
     *   description="",
     *   operationId="payMultiOrder",
     *  produces={"application/json"},
     *   @SWG\Parameter(
     *     name="uid",
     *     in="query",
     *     description="(签名参数)用户id",
     *     required=true,
     *     type="integer",
     *     @SWG\Schema(ref="uid")
     *   ),
     *   @SWG\Parameter(
     *     name="timestamp",
     *     in="query",
     *     description="(签名参数)时间戳",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="timestamp")
     *   ),
     *   @SWG\Parameter(
     *     name="sign",
     *     in="query",
     *     description="(签名参数)签名",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="sign")
     *   ),
     *   @SWG\Parameter(
     *     name="pay_type",
     *     in="query",
     *     description="支付方式（1支付宝，2微信支付）",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="pay_type")
     *   ),
     *   @SWG\Parameter(
     *     name="order_no",
     *     in="query",
     *     description="订单编号",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="order_no")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function payMultiOrder(Request $request){

        $uid = $request->input('uid',0);
        $pay_type = $request->input('pay_type',0);
        $order_no = $request->input('order_no',0);

        $OrderWx = OrderWx::whereOrderSn($order_no)->first();

        $order_no_array = explode(',',$OrderWx['order_no']);

        $OrderBases = OrderBase::whereIn('order_no',$order_no_array)->whereUid($uid)->whereState(OrderBase::STATE_NO_PAY)->get();
        $amount = 0;
        foreach($OrderBases as $OrderBase){
            $OrderBase->pay_type = $pay_type;
            $OrderBase->save();
            $amount = $amount + ($OrderBase->amount_goods + $OrderBase->amount_express - $OrderBase->amount_coupon) * 100;
        }
        $pay_info = self::pingMutiPPChargeCreate($pay_type,$amount,$order_no,$uid);
        if(!empty($pay_info)){
            $OrderPay = new OrderPay();
            $OrderPay->order_no = $order_no;
            $OrderPay->pay_info = json_encode($pay_info);
            $OrderPay->save();

            $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => array('id' => strval($OrderBase->id),'order_no'=>strval($order_no),'pay_info'=>$pay_info));
        }else{
            $result = array('ret' => self::RET_FAIL, 'msg' => '参数错误', 'data' => array('id' => strval($OrderBase->id),'order_no'=>strval($order_no),'pay_info'=>(object)array()));
        }
        Log::alert('$result'.print_r($result,true));
        return response()->json($result);
    }

    /**
     * @SWG\Post(path="/v1/order/{order_no}/cancel",
     *   tags={"order"},
     *   summary="取消订单",
     *   description="",
     *   operationId="cancelOrder",
     *  produces={"application/json"},
     *   @SWG\Parameter(
     *     name="uid",
     *     in="query",
     *     description="(签名参数)用户id",
     *     required=true,
     *     type="integer",
     *     @SWG\Schema(ref="uid")
     *   ),
     *   @SWG\Parameter(
     *     name="timestamp",
     *     in="query",
     *     description="(签名参数)时间戳",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="timestamp")
     *   ),
     *   @SWG\Parameter(
     *     name="sign",
     *     in="query",
     *     description="(签名参数)签名",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="sign")
     *   ),
     *   @SWG\Parameter(
     *     name="order_no",
     *     in="path",
     *     description="订单编号",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="order_no")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function cancelOrder(Request $request,$order_no){

        $uid = $request->input('uid',0);
        $OrderBase = OrderBase::whereOrderNo($order_no)->whereUid($uid)->first();
        if(!is_null($OrderBase) && $OrderBase->state == OrderBase::STATE_NO_PAY){
            OrderBase::whereOrderNo($order_no)->whereUid($uid)->update(array('state'=>OrderBase::STATE_CANCEL_USER));
            $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => array('order_no'=>strval($order_no)));
            if($OrderBase->coupon_user_id > 0){
                CouponUser::whereId($OrderBase->coupon_user_id)->update(array('state'=>CouponUser::state_unused));
            }

        }else{
            $result = array('ret' => self::RET_FAIL, 'msg' => '取消失败', 'data' => array('order_no'=>strval($order_no)));
        }
        return response()->json($result);
    }


    /**
     * @SWG\Post(path="/v1/order/{order_no}/modify_amount",
     *   tags={"order"},
     *   summary="订单改价",
     *   description="",
     *   operationId="cancelOrder",
     *  produces={"application/json"},
     *   @SWG\Parameter(
     *     name="uid",
     *     in="query",
     *     description="(签名参数)用户id",
     *     required=true,
     *     type="integer",
     *     @SWG\Schema(ref="uid")
     *   ),
     *   @SWG\Parameter(
     *     name="timestamp",
     *     in="query",
     *     description="(签名参数)时间戳",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="timestamp")
     *   ),
     *   @SWG\Parameter(
     *     name="sign",
     *     in="query",
     *     description="(签名参数)签名",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="sign")
     *   ),
     *   @SWG\Parameter(
     *     name="order_no",
     *     in="path",
     *     description="订单编号",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="order_no")
     *   ),
     *   @SWG\Parameter(
     *     name="amount",
     *     in="query",
     *     description="订单金额",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="order_no")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function modifyOrderAmount(Request $request,$order_no){

        $uid = $request->input('uid',0);
        $amount = $request->input('amount',0);

        $OrderBase = OrderBase::whereOrderNo($order_no)->first();
        $realAmount = bcsub(($OrderBase->amount_goods+$OrderBase->amount_express-$OrderBase->amount_coupon),$amount,2);

        if( isset($OrderBase->state) && $OrderBase->state != OrderBase::STATE_NO_PAY){
            $result = array('ret' => self::RET_FAIL, 'msg' => '订单状态不正确', 'data' => array('order_no'=>strval($order_no)));
            return response()->json($result);
        }

        Log::alert('$realAmount'.$realAmount);
        Log::alert('$OrderBase->guide_amount'.$OrderBase->guide_amount);
        
        if( isset($OrderBase->guide_amount) && $realAmount > $OrderBase->guide_amount){
            $result = array('ret' => self::RET_FAIL, 'msg' => '改价超出指定范围', 'data' => array('order_no'=>strval($order_no)));
            return response()->json($result);
        }

        $OrderBase->guide_amount = $OrderBase->guide_amount - $realAmount;
        $OrderBase->amount_goods = $amount - $OrderBase->amount_express + $OrderBase->amount_coupon;
        $saveResult = $OrderBase->save();
        $result = array('ret' => self::RET_FAIL, 'msg' => '改价失败', 'data' => array('order_no'=>strval($order_no)));
        if($saveResult == 1 ){
            $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => array('order_no'=>strval($order_no)));
        }

        return response()->json($result);
    }

    /**
     * @SWG\Post(path="/v1/order/{order_no}/finish",
     *   tags={"order"},
     *   summary="确定完成订单",
     *   description="",
     *   operationId="finishOrder",
     *  produces={"application/json"},
     *   @SWG\Parameter(
     *     name="uid",
     *     in="query",
     *     description="(签名参数)用户id",
     *     required=true,
     *     type="integer",
     *     @SWG\Schema(ref="uid")
     *   ),
     *   @SWG\Parameter(
     *     name="timestamp",
     *     in="query",
     *     description="(签名参数)时间戳",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="timestamp")
     *   ),
     *   @SWG\Parameter(
     *     name="sign",
     *     in="query",
     *     description="(签名参数)签名",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="sign")
     *   ),
     *   @SWG\Parameter(
     *     name="order_no",
     *     in="path",
     *     description="订单编号",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="order_no")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function finishOrder(Request $request,$order_no){

        $uid = $request->input('uid',0);

        $OrderBase = OrderBase::whereOrderNo($order_no)->whereUid($uid)->first();
        $OrderReturnNum = OrderReturn::whereOrderNo($order_no)->whereIn('state',array(OrderReturn::STATE_NO_CHECK,OrderReturn::STATE_NO_REFUND))->count();

        if($OrderReturnNum > 0){
            $result = array('ret' => self::RET_FAIL, 'msg' => '订单售后正在处理中，暂不能确认收货', 'data' => array('order_no'=>strval($order_no)));
            return response()->json($result);
        }

        if(isset($OrderBase['state']) && $OrderBase['state'] != OrderBase::STATE_SEND){
            $result = array('ret' => self::RET_FAIL, 'msg' => '订单状态不正确', 'data' => array('order_no'=>strval($order_no)));
            return response()->json($result);
        }

        if(!is_null($OrderBase) && $OrderBase->state == OrderBase::STATE_SEND){

            //更新订单状态
            OrderBase::whereId($OrderBase['id'])->update(['state'=>OrderBase::STATE_FINISHED]);

            //导游分成到账
            $GuideBilling = GuideBilling::whereOrderNo($OrderBase['order_no'])->first();
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
            $TaBilling = TaBilling::whereOrderNo($OrderBase['order_no'])->first();
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
            $SupplierBilling = SupplierBilling::whereOrderNo($OrderBase['order_no'])->first();
            $SupplierBilling->state = TaBilling::state_fund;
            $SupplierBilling->save();

            $SupplierBase = SupplierBase::whereId($SupplierBilling['supplier_id'])->first();
            $amount = $SupplierBilling->amount - $SupplierBilling->return_amount;
            if($amount > 0) {
                $SupplierBase->amount = $SupplierBase->amount + $amount;
                $SupplierBase->save();
            }

            //平台到账
            $PlatformBilling = PlatformBilling::whereOrderNo($OrderBase['order_no'])->first();
            $PlatformBilling->state = TaBilling::state_fund;
            $PlatformBilling->save();


            $OrderLog = new OrderLog();
            $OrderLog->action = '用户完成订单';
            $OrderLog->order_no = $OrderBase['order_no'];
            $OrderLog->content  = json_encode(array('before_state'=>OrderBase::STATE_SEND,'after_state'=>OrderBase::STATE_FINISHED));
            $OrderLog->save();

            $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => array('order_no'=>strval($order_no)));
        }else{
            $result = array('ret' => self::RET_FAIL, 'msg' => '参数不正确', 'data' => array('order_no'=>strval($order_no)));
        }
        return response()->json($result);
    }

    /**
 * @SWG\Get(path="/v1/order/pre_coupon",
 *   tags={"order"},
 *   summary="获取订单的预用优惠券",
 *   description="",
 *   operationId="getOrderCoupon",
 *  produces={"application/json"},
 *   @SWG\Parameter(
 *     name="uid",
 *     in="query",
 *     description="(签名参数)用户id",
 *     required=true,
 *     type="integer",
 *     @SWG\Schema(ref="uid")
 *   ),
 *   @SWG\Parameter(
 *     name="timestamp",
 *     in="query",
 *     description="(签名参数)时间戳",
 *     required=true,
 *     type="string",
 *     @SWG\Schema(ref="timestamp")
 *   ),
 *   @SWG\Parameter(
 *     name="sign",
 *     in="query",
 *     description="(签名参数)签名",
 *     required=true,
 *     type="string",
 *     @SWG\Schema(ref="sign")
 *   ),
 *   @SWG\Parameter(
 *     name="store_id",
 *     in="query",
 *     description="供应商id",
 *     required=true,
 *     type="string",
 *     @SWG\Schema(ref="store_id")
 *   ),
 *   @SWG\Response(response=200,description="successful operation"),
 * )
 */
    public function getOrderPreCoupon(Request $request){

        $uid = $request->input('uid',0);
        $supplier_id = $request->input('store_id', 0);

        Log::alert('getCoupon request:' . print_r($request->input(), true));
        $CouponUser = CouponUser::whereSupplierId($supplier_id)->whereUid($uid)->whereState(CouponUser::state_unused)->orderBy('amount_order','desc')->get();
        $result = self::formatCoupon($CouponUser);

        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => $result);
        return response()->json($result);
    }


    /**
     * @SWG\Get(path="/v1/order/coupon",
     *   tags={"order"},
     *   summary="获取该订单的优惠券",
     *   description="",
     *   operationId="getCouponByOrder",
     *  produces={"application/json"},
     *   @SWG\Parameter(
     *     name="uid",
     *     in="query",
     *     description="(签名参数)用户id",
     *     required=true,
     *     type="integer",
     *     @SWG\Schema(ref="uid")
     *   ),
     *   @SWG\Parameter(
     *     name="timestamp",
     *     in="query",
     *     description="(签名参数)时间戳",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="timestamp")
     *   ),
     *   @SWG\Parameter(
     *     name="sign",
     *     in="query",
     *     description="(签名参数)签名",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="sign")
     *   ),
     *  @SWG\Parameter(
     *     name="order_no",
     *     in="query",
     *     description="订单编号",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="order_no")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function getCouponByOrder(Request $request){

        $uid = $request->input('uid',0);
        $orderNo = $request->input('order_no', 0);
        $OrderWx = OrderWx::whereOrderSn($orderNo)->first();
        if(!is_null($OrderWx)){
            $orderNo = explode(',',$OrderWx['order_no']);
        }else{
            $orderNo = array($orderNo);
        }

        $CouponUser = CouponUser::whereIn('send_source',$orderNo)->whereUid($uid)->get();
        $result = self::formatCoupon($CouponUser);

        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => $result);
        return response()->json($result);
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

    private function overGoodsLimitNum($goods_arr){
        foreach ($goods_arr as $v) {
            $GoodsSpec = GoodsSpec::whereId($v['spec_id'])->first();
            if($GoodsSpec['num_limit'] > 0 && $GoodsSpec['num_limit'] < $v['num']){
                return strval($v['spec_id']);
            }
        }
        return '0';
    }

    private function reduceGoodsNum($order_no, $goods_arr,$uid){
        $taAmount = 0;
        $hasGift = 0;
        $guideAmount = 0;
        $supplier_id = 0;
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
            $supplier_id = $GoodsBase['supplier_id'];

            //返利
            $taAmount = $taAmount + (($OrderGood->price * $GoodsSpec['travel_agency_rate'] /100) * $goods['num']);
            $guideAmount = $guideAmount + (($OrderGood->price * $GoodsSpec['guide_rate'] /100) * $goods['num']);

            //删除购物车
            if($uid > 0){
                UserCart::whereUid($uid)->whereGoodsId($goods['goods_id'])->whereSpecId($goods['spec_id'])->delete();
            }

         }

        OrderBase::whereOrderNo($order_no)->update(array('ta_amount' => $taAmount,'guide_amount' => $guideAmount,'supplier_id'=>$supplier_id,'has_gift'=>$hasGift));
    }


    private function pingPPChargeCreate($order_no,$data)
    {

        if ($data->pay_type == 1) {
            $channel = 'alipay';
        } elseif ($data->pay_type == 2) {
            $channel = 'wx';
        }

        Log::alert('订单原数据:' . print_r($data, true));

        $amount = ($data->amount_goods + $data->amount_express - $data->amount_coupon) * 100;

        //主人指定的补充逻辑，小于1分就付1元。
        if($amount < 1){
            $amount = 100;
        }

        if(env('APP_ENV') == 'dev'){
            $amount = 1;
        }

        Pingpp::setApiKey('sk_live_4ib5mDC44Ge9mjL8G4azXfr1');
        if(env('APP_ENV') == 'local'){
            Pingpp::setApiKey('sk_test_KKuTyPKerTeHDOufL4jLa9m5');
        }

        $subject = '订单'.$order_no;

        Log::alert('PAY数据:' . print_r(array(
                'order_no' => $order_no,
                'app' => array('id' => 'app_L0Wj1SDmvPSCLaXr'),
                'channel' => $channel,
                'amount' => $amount,
                'client_ip' => $_SERVER['REMOTE_ADDR'],
                'currency' => 'cny',
                'subject' =>  $order_no,
                'body' => 'UID' .$data->uid.'-'. $order_no
            ), true));

        $ch = Charge::create(
            array(
                'order_no' => $order_no,
                'app' => array('id' => 'app_L0Wj1SDmvPSCLaXr'),
                'channel' => $channel,
                'amount' => $amount,
                'client_ip' => $_SERVER['REMOTE_ADDR'],
                'currency' => 'cny',
                'subject' =>  $subject,
                'body' => $subject
            )
        );
        return json_decode($ch, true);
    }

    private function pingMutiPPChargeCreate($pay_type,$amount, $order_no,$uid)
    {

        if ($pay_type== 1) {
            $channel = 'alipay';
        } elseif ($pay_type == 2) {
            $channel = 'wx';
        }

        //主人指定的补充逻辑，小于1分就付1元。
        if($amount < 1){
            $amount = 100;
        }

        if(env('APP_ENV') == 'dev'){
            $amount = 1;
            $OrderWx = OrderWx::whereOrderSn($order_no)->first();
            $order_no_array = explode(',',$OrderWx['order_no']);
            $amount = $amount * count($order_no_array);
        }

        Pingpp::setApiKey('sk_live_4ib5mDC44Ge9mjL8G4azXfr1');
        if(env('APP_ENV') == 'local'){
            Pingpp::setApiKey('sk_test_KKuTyPKerTeHDOufL4jLa9m5');
        }

        $subject = '订单'.$order_no;

        Log::alert('PAY数据:' . print_r(array(
                'order_no' => $order_no,
                'app' => array('id' => 'app_L0Wj1SDmvPSCLaXr'),
                'channel' => $channel,
                'amount' => $amount,
                'client_ip' => $_SERVER['REMOTE_ADDR'],
                'currency' => 'cny',
                'subject' =>  $order_no,
                'body' => 'UID' .$uid.'-'. $order_no
            ), true));

        $ch = Charge::create(
            array(
                'order_no' => $order_no,
                'app' => array('id' => 'app_L0Wj1SDmvPSCLaXr'),
                'channel' => $channel,
                'amount' => $amount,
                'client_ip' => $_SERVER['REMOTE_ADDR'],
                'currency' => 'cny',
                'subject' =>  $subject,
                'body' => $subject
            )
        );
        return json_decode($ch, true);
    }


    protected function getGuideTaId($uid){
        $GuideBase = GuideBase::whereUid($uid)->first();
        $TaGroup = TaGroup::whereGuideId($GuideBase['id'])->whereState(TaGroup::STATE_START)->first();
        return array('guide_id'=>$GuideBase['id'],'ta_id'=>isset($TaGroup['ta_id']) ? $TaGroup['ta_id'] : $GuideBase['ta_id'],'group_id'=>isset($TaGroup['id']) ? $TaGroup['id'] : 0);
    }


    static public function formatCoupon($CouponUsers){
        $result = array();
        foreach($CouponUsers as $CouponUser){
            $tmp = array();
            $tmp['coupon_user_id'] = strval($CouponUser['id']);
            $tmp['coupon_id'] = strval($CouponUser['coupon_id']);
            $tmp['amount_order'] = strval($CouponUser['amount_order']);
            $tmp['amount_coupon'] = strval($CouponUser['amount_coupon']);
            $tmp['start_time'] = strval($CouponUser['start_time']);
            $tmp['end_time'] = strval($CouponUser['end_time']);
            $tmp['state'] = strval($CouponUser['state']);
            $result[] = $tmp;
        }
        return $result;
    }

}

