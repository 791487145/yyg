<?php namespace App\Http\Controllers\Api;

use App\Models\ConfExpress;
use App\Models\CouponBase;
use App\Models\CouponUser;
use App\Models\GoodsBase;
use App\Models\GoodsImage;
use App\Models\GoodsSpec;
use App\Models\GuideBase;
use App\Models\GuideBilling;
use App\Models\GuideUser;
use App\Models\OrderBase;
use App\Models\OrderExpress;
use App\Models\OrderReturn;
use App\Models\OrderReturnLog;
use App\Models\SmsVerificationCode;
use App\Models\TaGroup;
use App\Models\UserAddress;
use App\Models\UserBase;
use App\Models\UserCart;
use App\Models\UserFavorite;
use App\Models\UserNews;
use Log;
use Lang;
use App\Models\OrderReturnImage;
use App\Models\StoreUser;
use Illuminate\Http\Request;
use App\Http\Controllers\SignController;
use zgldh\QiniuStorage\QiniuStorage;
use App\Models\CommentGood;
use App\Models\CommentGoodsImage;
use App\Models\OrderGood;
use App\Models\SupplierBase;
use Qiniu\json_decode;


class MyController extends SignController
{


    /**
     * @SWG\Get(path="/v1/my/favorite",
     *   tags={"my"},
     *   summary="获取我的收藏",
     *   description="",
     *   operationId="getFavorite",
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
     *     name="page_num",
     *     in="query",
     *     description="页码",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="page_num")
     *   ),
     *   @SWG\Parameter(
     *     name="limit",
     *     in="query",
     *     description="一页显示多少条",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="limit")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function getFavorite(Request $request){
        $uid = $request->input('uid');
        $page_num = intval($request->input('page_num', 0));
        $limit = intval($request->input('limit', 20));
        $offset = $page_num * $limit;

        $result = array();
        $UserFavorite = UserFavorite::whereUid($uid)->orderBy('id','desc')->offset($offset)->limit($limit)->get();
        foreach($UserFavorite as $favorite){
            $tmp = array();
            $v = GoodsBase::whereId($favorite['goods_id'])->first();
            $tmp['id'] = strval($favorite['id']);
            $tmp['goods_id'] = strval($favorite['goods_id']);
            $tmp['title'] = strval($v['title']);
            $GoodsSpec = GoodsSpec::whereGoodsId($v['id'])->first();

            $tmp['price'] = strval($GoodsSpec['price']);
            $tmp['rebate'] = strval(number_format(($GoodsSpec['price'] * $GoodsSpec['guide_rate'] / 100),2));
            $tmp['num'] = strval($v['num']);
            $tmp['num_sold'] = strval($v['num_sold']);
            $tmp['cover'] = env('IMAGE_DISPLAY_DOMAIN').$v['first_image'];
            if($v['state'] != 1){
                $tmp['state'] = '2';
            }else{
                $tmp['state'] = '1';
            }
            $result[] = $tmp;
        }
        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' =>$result );
        return response()->json($result);

    }

    /**
     * @SWG\Post(path="/v1/my/favorite",
     *   tags={"my"},
     *   summary="添加收藏商品",
     *   description="",
     *   operationId="addFavorite",
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
     *     name="goods_id",
     *     in="query",
     *     description="商品id",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="#/definitions/string")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function addFavorite(Request $request){
        $uid = $request->input('uid',0);
        $goods_id = $request->input('goods_id',0);

        $UserFavorite = UserFavorite::whereGoodsId($goods_id)->whereUid($uid)->first();
        if(is_null($UserFavorite)){
            $UserFavorite = new UserFavorite();
            $UserFavorite->uid = $uid;
            $UserFavorite->goods_id = $goods_id;
            $UserFavorite->save();
        }
        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => array('id'=>strval($UserFavorite->id)));

        return response()->json($result);
    }


    /**
     * @SWG\Post(path="/v1/my/favorite/{id}",
     *   tags={"my"},
     *   summary="删除收藏商品",
     *   description="",
     *   operationId="deleteFavorite",
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
     *     name="id",
     *     in="path",
     *     description="收藏列表返回的ID",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="#/definitions/string")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function deleteFavorite($id,Request $request){
        $uid = $request->input('uid',0);
        $ret = UserFavorite::whereId($id)->whereUid($uid)->delete();
        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => array('id'=>strval($id)));
        if($ret == 0){
            $result = array('ret' => self::RET_FAIL, 'msg' => '移除失败', 'data' => array('id'=>''));
        }
        return response()->json($result);
    }

    /**
     * @SWG\Get(path="/v1/my/orders",
     *   tags={"my"},
     *   summary="获取我的订单",
     *   description="",
     *   operationId="getOrders",
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
     *     name="state",
     *     in="query",
     *     description="0.待付款,1.待发货,2.待收货,5.已完成,99.全部",
     *     required=false,
     *     type="string",
     *     @SWG\Schema(ref="#/definitions/string")
     *   ),
     *   @SWG\Parameter(
     *     name="user_id",
     *     in="query",
     *     description="用户id（可选字段，用于导游查某用户的订单列表）",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="page_num")
     *   ),
     *   @SWG\Parameter(
     *     name="page_num",
     *     in="query",
     *     description="页码",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="page_num")
     *   ),
     *   @SWG\Parameter(
     *     name="limit",
     *     in="query",
     *     description="一页显示多少条",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="limit")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function getOrders(Request $request){
        $uid = $request->input('uid');
        $user_id = $request->input('user_id',0);

        $state = $request->input('state',0);
        $page_num = intval($request->input('page_num', 0));
        $limit = intval($request->input('limit', 20));
        $offset = $page_num * $limit;

        $orders = OrderBase::whereUid($uid);
        if($state != 99){ $orders = $orders->whereState($state); }
        $orders = $orders->orderBy('id','desc')->offset($offset)->limit($limit)->get();
        if($user_id > 0){
            $orders = OrderBase::whereUid($user_id)->whereIn('state',array(OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED))->orderBy('id','desc')->offset($offset)->limit($limit)->get();
        }

        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' =>self::formatOrderData($orders) );
        return response()->json($result);
    }

    /**
     * @SWG\Get(path="/v1/my/order/{id}",
     *   tags={"my"},
     *   summary="获取我的订单",
     *   description="",
     *   operationId="getOrders",
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
     *     name="id",
     *     in="path",
     *     description="订单id",
     *     required=false,
     *     type="string",
     *     @SWG\Schema(ref="#/definitions/string")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function getOrderDetailById($id,Request $request){
        $uid = $request->input('uid');

        //去掉了whereUid 这个接口店铺要复用
        $orderBase = OrderBase::whereId($id)->get();

        $order = self::formatOrderData($orderBase);
        $order = isset($order[0]) ? $order[0] : array();

        $receiver_info = json_decode($orderBase[0]['receiver_info'],true);
        $order['receiver']['name'] = isset($receiver_info['name']) ? $receiver_info['name'] : '';
        $order['receiver']['mobile'] = isset($receiver_info['mobile']) ? $receiver_info['mobile'] : '';
        $order['receiver']['address'] = $receiver_info['province'].$receiver_info['city'].$receiver_info['district'].$receiver_info['address'];

        $order['express']['name'] = isset($orderBase[0]['express_name']) ? $orderBase[0]['express_name'] : '';
        $order['express']['no'] = isset($orderBase[0]['express_no']) ? $orderBase[0]['express_no'] : '';
        $ConfExpress = ConfExpress::whereName($order['express']['name'])->first();
        $order['express']['tel'] = !is_null($ConfExpress) ? $ConfExpress['tel'] : '';

        //multi express
        $OrderExpresses = OrderExpress::whereOrderNo($orderBase[0]['order_no'])->get();
        $order['express_multi'] = array();
        if(count($OrderExpresses) > 0){
            foreach($OrderExpresses as $OrderExpress){
                $tmp = array();
                $tmp['express_name'] = $OrderExpress['express_name'];
                $tmp['express_no'] = $OrderExpress['express_no'];
                $order['express_multi'][] = $tmp;
            }
        }

        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' =>$order );
        return response()->json($result);
    }

    //todo售后


    /**
     * @SWG\Post(path="/v1/my/bank_card",
     *   tags={"my"},
     *   summary="邦定银行卡",
     *   description="",
     *   operationId="bindBankCard",
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
     *     name="name",
     *     in="query",
     *     description="开户姓名",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="sign")
     *   ),
     *  @SWG\Parameter(
     *     name="card_number",
     *     in="query",
     *     description="开户卡号",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="sign")
     *   ),
     *  @SWG\Parameter(
     *     name="bank_name",
     *     in="query",
     *     description="开户银行名称",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="sign")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function bindBankCard(Request $request){
        $uid = $request->input('uid',0);
        $name = $request->input('name','');
        $card_number = $request->input('card_number','');
        $bank_name = $request->input('bank_name','');

        $GuideBase = GuideBase::whereUid($uid)->first();
        $result = array('ret' => self::RET_FAIL, 'msg' => '邦定失败', 'data' => array('uid'=>'0'));
        if($name != $GuideBase['real_name']){
            $result = array('ret' => self::RET_FAIL, 'msg' => '银行卡名称与注册实名不一致', 'data' => array('uid'=>'0'));
        }else if(!is_null($GuideBase)){
            $GuideBase->withdraw_name = $name;
            $GuideBase->withdraw_card_number = $card_number;
            $GuideBase->withdraw_bank = $bank_name;
            $GuideBase->save();
            $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => array('uid'=>strval($uid)));
        }
        return response()->json($result);
    }


    /**
     * @SWG\Delete(path="/v1/my/bank_card",
     *   tags={"my"},
     *   summary="删除邦定的银行卡",
     *   description="",
     *   operationId="deleteWithdraw",
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
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function deleteBankCard(Request $request){
        $uid = $request->input('uid',0);

        $param = array();
        $param['withdraw_name'] = '';
        $param['withdraw_card_number'] = '';
        $param['withdraw_bank'] = '';

        GuideBase::whereUid($uid)->update($param);

        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => array('uid'=>strval($uid)));
        return response()->json($result);
    }


    /**
     * @SWG\get(path="/v1/my/bank_card/check",
     *   tags={"my"},
     *   summary="检查是否已邦定银行卡",
     *   description="",
     *   operationId="checkBankCard",
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
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function checkBankCard(Request $request){
        $uid = $request->input('uid',0);

        $GuideBase = GuideBase::whereUid($uid)->first();
        $UserBase = UserBase::whereId($uid)->first();
        $result = array();
        $result['name'] = strval($GuideBase['real_name']);
        $result['bank'] = strval($GuideBase['withdraw_bank']);
        $result['card_number'] = strval($GuideBase['withdraw_card_number']);


        if($UserBase['state'] == UserBase::state_check){
            $result['is_audit'] = strval(1);
        }else{
            $result['is_audit'] = strval(0);
        }

        //审核中的状态
        if($UserBase['state'] == UserBase::state_upload_2cert){
            $result['is_audit'] = strval(UserBase::state_upload_2cert);
        }

        if(!is_null($GuideBase) && $GuideBase->withdraw_name != '' && $GuideBase->withdraw_card_number != '' && $GuideBase->withdraw_bank != ''){
            $result['is_bind_bank'] = strval(1);
        }else{
            $result['is_bind_bank'] = strval(0);
        }


        if($result['is_audit'] == 0){
            $result = array('ret' => self::RET_FAIL, 'msg' => '系统检测到你还未进行实名认证，请先实名认证!', 'data' => $result);

        }else if($result['is_audit'] == UserBase::state_upload_2cert){
            $result = array('ret' => self::RET_FAIL, 'msg' => '资料审核中，请稍后', 'data' => $result);

        }else if($result['is_bind_bank'] == 0){
            $result = array('ret' => self::RET_FAIL, 'msg' => '请邦定您的银行信息', 'data' => $result);

        }else{
            $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => $result);
        }

        return response()->json($result);
    }


    /**
     * @SWG\get(path="/v1/my/address",
     *   tags={"my"},
     *   summary="我的收货地址",
     *   description="",
     *   operationId="getAddress",
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
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function getAddress(Request $request)
    {
        $uid = $request->input('uid', 0);
        $result = array();
        $data = UserAddress::whereUid($uid)->get();
        foreach ($data as $v) {
            $tmp = array();
            $tmp['id'] = strval($v['id']);
            $tmp['name'] = $v['name'];
            $tmp['mobile'] = $v['mobile'];
            $tmp['province_id'] = strval($v['province_id']);
            $tmp['city_id'] = strval($v['city_id']);
            $tmp['district_id'] = strval($v['district_id']);
            $tmp['province'] = self::getCityName($v['province_id']);
            $tmp['city'] = self::getCityName($v['city_id']);
            $tmp['district'] = self::getCityName($v['district_id']);
            $tmp['address'] = $v['address'];
            $tmp['is_default'] = strval($v['is_default']);
            $result[] = $tmp;
        }
        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => $result);
        return response()->json($result);
    }

    /**
     * @SWG\Post(path="/v1/my/address",
     *   tags={"my"},
     *   summary="添加收货地址",
     *   description="",
     *   operationId="addAddress",
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
     *     name="name",
     *     in="query",
     *     description="收货人姓名",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="sign")
     *   ),
     *  @SWG\Parameter(
     *     name="mobile",
     *     in="query",
     *     description="收货人手机",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="sign")
     *   ),
     *  @SWG\Parameter(
     *     name="province_id",
     *     in="query",
     *     description="省id",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="sign")
     *   ),
     *  @SWG\Parameter(
     *     name="city_id",
     *     in="query",
     *     description="市id",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="sign")
     *   ),
     *  @SWG\Parameter(
     *     name="district_id",
     *     in="query",
     *     description="区id",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="sign")
     *   ),
     *  @SWG\Parameter(
     *     name="address",
     *     in="query",
     *     description="地址",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="sign")
     *   ),
     *  @SWG\Parameter(
     *     name="is_default",
     *     in="query",
     *     description="是否默认地址(1表示是，0表示不是)",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="sign")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function addAddress(Request $request){
        $uid = $request->input('uid', 0);
        $name = $request->input('name', '');
        $mobile = $request->input('mobile', '');
        $province_id = $request->input('province_id', 0);
        $city_id = $request->input('city_id', 0);
        $district_id = $request->input('district_id', 0);
        $address = $request->input('address', '');
        $is_default = $request->input('is_default', 0);
        if ($uid == 0 ) {
            return response()->json(array('ret' => self::RET_FAIL, 'msg' => '参数不正确', 'data' => (object)array()));
        }


        $UserAddress = UserAddress::whereUid($uid)->whereName($name)->whereMobile($mobile)->whereProvinceId($province_id)->whereCityId($city_id)->whereDistrictId($district_id)->whereAddress($address)->first();

        $ret = 0;
        if (is_null($UserAddress)) {
            $UserAddress = new UserAddress();
            $UserAddress->uid = $uid;
            $UserAddress->name = $name;
            $UserAddress->mobile = $mobile;
            $UserAddress->province_id = $province_id;
            $UserAddress->city_id = $city_id;
            $UserAddress->district_id = $district_id;
            $UserAddress->address = $address;
            $ret = $UserAddress->save();
        }

        if ($ret == 1 && $is_default == 1) {
            UserAddress::whereUid($uid)->update(array('is_default' => 0));
            UserAddress::whereId($UserAddress->id)->update(array('is_default' => 1));
        }

        $num = UserAddress::whereUid($uid)->count();
        if($ret == 1 && $num == 0){
            UserAddress::whereId($UserAddress->id)->update(array('is_default' => 1));
        }

        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => array('id' => strval($UserAddress->id)));
        return response()->json($result);
    }


    /**
     * @SWG\Post(path="/v1/my/address/{id}",
     *   tags={"my"},
     *   summary="修改收货地址",
     *   description="",
     *   operationId="modifyAddress",
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
     *     name="name",
     *     in="query",
     *     description="收货人姓名",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="sign")
     *   ),
     *  @SWG\Parameter(
     *     name="mobile",
     *     in="query",
     *     description="收货人手机",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="sign")
     *   ),
     *  @SWG\Parameter(
     *     name="province_id",
     *     in="query",
     *     description="省id",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="sign")
     *   ),
     *  @SWG\Parameter(
     *     name="city_id",
     *     in="query",
     *     description="市id",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="sign")
     *   ),
     *  @SWG\Parameter(
     *     name="district_id",
     *     in="query",
     *     description="区id",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="sign")
     *   ),
     *  @SWG\Parameter(
     *     name="address",
     *     in="query",
     *     description="地址",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="sign")
     *   ),
     *  @SWG\Parameter(
     *     name="is_default",
     *     in="query",
     *     description="是否默认地址(1表示是，0表示不是)",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="sign")
     *   ),
     *   @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     description="收货地址id",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="sign")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function modifyAddress(Request $request,$id){
        $uid = $request->input('uid', 0);
        $name = $request->input('name', '');
        $mobile = $request->input('mobile', '');
        $province_id = $request->input('province_id', 0);
        $city_id = $request->input('city_id', 0);
        $district_id = $request->input('district_id', 0);
        $address = $request->input('address', '');
        $is_default = $request->input('is_default', 0);

        if ($uid == 0) {
            return response()->json(array('ret' => self::RET_FAIL, 'msg' => '参数不正确', 'data' => (object)array()));
        }

        $UserAddress = UserAddress::whereId($id)->whereUid($uid)->first();
        if (!is_null($UserAddress)) {
            $UserAddress->uid = $uid;
            $UserAddress->name = $name;
            $UserAddress->mobile = $mobile;
            $UserAddress->province_id = $province_id;
            $UserAddress->city_id = $city_id;
            $UserAddress->district_id = $district_id;
            $UserAddress->address = $address;
            $ret = $UserAddress->save();
            if ($ret == 1 && $is_default == 1) {
                UserAddress::whereUid($uid)->update(array('is_default' => 0));
                UserAddress::whereId($UserAddress->id)->update(array('is_default' => 1));
            }
            $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => array('id' => strval($UserAddress->id)));
        }else{
            $result = array('ret' => self::RET_FAIL, 'msg' => '修改收货地址失败', 'data' => (object)array());
        }

        return response()->json($result);
    }


    /**
     * @SWG\Post(path="/v1/my/address/{id}/delete",
     *   tags={"my"},
     *   summary="删除收货地址",
     *   description="",
     *   operationId="deleteAddress",
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
     *     name="id",
     *     in="path",
     *     description="收货地址id",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="sign")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function deleteAddress(Request $request,$id){
        $id = intval($id);
        $uid = intval($request->input('uid', 0));
        if ($uid == 0 || $id == 0) {
            return response()->json(array('ret' => self::RET_FAIL, 'msg' => '参数不正确', 'data' => (object)array()));
        }
        $ret = UserAddress::whereUid($uid)->whereId($id)->delete();
        if($ret == 1){
            $UserAddress = UserAddress::whereUid($uid)->whereIsDefault(1)->first();
            if(is_null($UserAddress)){
                UserAddress::whereUid($uid)->orderBy('id','desc')->limit(1)->update(array('is_default'=>1));
            }
            $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => array('id' => strval($id)));
        }else{
            $result = array('ret' => self::RET_FAIL, 'msg' => '删除失败', 'data' => (object)array());
        }
        return response()->json($result);
    }


    /**
     * @SWG\Get(path="/v1/my/badge",
     *   tags={"my"},
     *   summary="获取角标",
     *   description="",
     *   operationId="getBadge",
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
     *
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function getBadge(Request $request){
        $uid = $request->input('uid',0);

        $news_num = UserNews::whereUid($uid)->whereIsRead(0)->count();
        $result['news_num'] = strval($news_num);

        $GuideBase = GuideBase::whereUid($uid)->first();
        $group_num = TaGroup::whereGuideId($GuideBase['id'])->whereState(TaGroup::STATE_YES_START)->count();
        $result['group_num'] = strval($group_num);

        $order_wait_pay_num = OrderBase::whereGuideId($GuideBase['id'])->whereState(OrderBase::STATE_NO_PAY)->count();
        $result['order_wait_pay_num'] = strval($order_wait_pay_num);

        $cart_num = UserCart::whereUid($uid)->count();
        $result['cart_num'] = strval($cart_num);

        $coupon_num = CouponUser::whereState(CouponUser::state_unused)->whereUid($uid)->count();
        $result['coupon_num'] = strval($coupon_num);

        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' =>$result );
        return response()->json($result);

    }


    /**
     * @SWG\Post(path="/v1/my/withdraw",
     *   tags={"my"},
     *   summary="添加提现",
     *   description="",
     *   operationId="addWithdraw",
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
     *     name="amount",
     *     in="query",
     *     description="提现金额",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="#/definitions/string")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function addWithdraw(Request $request){
        $uid = $request->input('uid',0);
        $amount = $request->input('amount',0);

        $UserBase = UserBase::whereId($uid)->first();

        if($amount < 100){
            //return response()->json(array('ret' => self::RET_FAIL, 'msg' => '提现不能少于100元', 'data' => (object)array()));
        }

        if($UserBase['amount'] < $amount){
            return response()->json(array('ret' => self::RET_FAIL, 'msg' => '余额不足', 'data' => (object)array()));
        }


        $UserBase->freeze_amount = $UserBase->freeze_amount +  $amount;
        $UserBase->amount = $UserBase->amount -  $amount;
        $ret = $UserBase->save();

        if($ret  == 1){
            $GuideBase = GuideBase::whereUid($uid)->first();
            $UserBase = UserBase::whereId($uid)->first();
            $withdraw = array('withdraw_name'=>urlencode($GuideBase['withdraw_name']),'withdraw_bank'=>urlencode($GuideBase['withdraw_bank']),'withdraw_card_number'=>urlencode($GuideBase['withdraw_card_number']));

            $GuideBilling = new GuideBilling();
            $GuideBilling->guide_id = $GuideBase['id'];
            $GuideBilling->uid = $uid;
            $GuideBilling->in_out = GuideBilling::in_out;
            $GuideBilling->amount = $amount;
            $GuideBilling->balance = $UserBase['amount'];
            $GuideBilling->content = '提现';
            $GuideBilling->state = GuideBilling::state_withdraw_wait_audit;
            $GuideBilling->remark = urldecode(json_encode($withdraw));
            $GuideBilling->save();
            GuideBilling::where('id','<',$GuideBilling->id)->whereGuideId($GuideBilling->guide_id)->whereState(GuideBilling::state_fund)->whereWithdrawId(GuideBilling::no_apply_withdraw_id)->update(['withdraw_id'=>$GuideBilling->id]);
        }
        $id = isset($GuideBilling->id) ? $GuideBilling->id : 0;
        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => array('id'=>strval($id)));

        return response()->json($result);
    }


    /**
     * @SWG\Get(path="/v1/my/withdraw",
     *   tags={"my"},
     *   summary="获取我账单",
     *   description="",
     *   operationId="getWithdraw",
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
     *     name="state",
     *     in="query",
     *     description="1.累计收益  2.待入账 3.可用余额明细 4.提现记录",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="sign")
     *   ),
     *   @SWG\Parameter(
     *     name="page_num",
     *     in="query",
     *     description="页码",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="page_num")
     *   ),
     *   @SWG\Parameter(
     *     name="limit",
     *     in="query",
     *     description="一页显示多少条",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="limit")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function getWithdraw(Request $request){
        $uid = $request->input('uid');
        $state = $request->input('state',1);

        $page_num = intval($request->input('page_num', 0));
        $limit = intval($request->input('limit', 20));
        $offset = $page_num * $limit;

        $result = array();
        $GuideBase = GuideBase::whereUid($uid)->first();

        $GuideBilling = GuideBilling::whereGuideId($GuideBase['id']);
        if($state == 1){
            $GuideBilling = $GuideBilling->whereInOut(GuideBilling::in_income);
        }
        if($state == 2){
            $GuideBilling = $GuideBilling->whereInOut(GuideBilling::in_income)->whereState(GuideBilling::state_nofund);
        }
        if($state == 3){
            $GuideBilling = $GuideBilling->whereState(GuideBilling::state_fund);
        }
        if($state == 4){
            $GuideBilling = $GuideBilling->whereInOut(GuideBilling::in_out);
        }

        $GuideBilling = $GuideBilling->orderBy('id','desc')->offset($offset)->limit($limit)->get();
        foreach($GuideBilling as $v){
            $tmp = array();
            $tmp['in_out'] = $v['in_out'] == 1 ? '+' : '-';
            $tmp['amount'] = $v['amount'];
            $tmp['return_amount'] = strval($v['return_amount']) ;
            $tmp['balance'] = strval($v['balance']);
            $tmp['content'] = $v['content'];
            $tmp['state_cn'] = GuideBilling::getStateCN($v['state']);
            $tmp['created_at'] = date('Y.m.d H:i',strtotime($v['created_at']));

            $result[] = $tmp;
        }
        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' =>$result );
        return response()->json($result);

    }


    /**
     * @SWG\Get(path="/v1/my/withdraw/info",
     *   tags={"my"},
     *   summary="获取我的提现信息",
     *   description="",
     *   operationId="getWithdrawInfo",
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
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function getWithdrawInfo(Request $request){
        $uid = $request->input('uid',0);

        $GuideBase = GuideBase::whereUid($uid)->first();
        $result = array();
        $result['name'] = strval($GuideBase['real_name']);
        $result['bank'] = strval($GuideBase['withdraw_bank']);
        $result['card_number'] = strval($GuideBase['withdraw_card_number']);

        $UserBase = UserBase::whereId($uid)->first();
        $result['balance'] = strval($UserBase['amount']);

        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => $result);

        return response()->json($result);
    }


    /**
     * @SWG\Get(path="/v1/my/return_orders",
     *   tags={"my"},
     *   summary="获取我的售后订单",
     *   description="",
     *   operationId="getReturnOrders",
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
     *     name="page_num",
     *     in="query",
     *     description="页码",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="page_num")
     *   ),
     *   @SWG\Parameter(
     *     name="limit",
     *     in="query",
     *     description="一页显示多少条",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="limit")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function getReturnOrders(Request $request){
        $uid = $request->input('uid');
        $page_num = intval($request->input('page_num', 0));
        $limit = intval($request->input('limit', 20));
        $offset = $page_num * $limit;

        $OrderReturn = OrderReturn::whereUid($uid)->orderBy('id','desc')->offset($offset)->limit($limit)->get();
        $result = array();
        foreach($OrderReturn as $v){
            $orders = OrderBase::whereOrderNo($v['order_no'])->get();
            $orders = self::formatOrderData($orders);
            $tmp = $orders[0];
            $tmp['return_no'] = $v['return_no'];
            $tmp['state_cn'] = OrderReturn::getStateCN($v['state']);
            $result[] = $tmp;
        }
        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' =>$result);
        return response()->json($result);
    }

    /**
     * @SWG\Get(path="/v1/my/return_order/{return_no}",
     *   tags={"my"},
     *   summary="获取我的售后订单详情",
     *   description="",
     *   operationId="getReturnOrderDetail",
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
     *     name="return_no",
     *     in="path",
     *     description="退单号",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="string")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function getReturnOrderDetail($return_no,Request $request){
        $uid = $request->input('uid');
        $OrderReturnLog = OrderReturnLog::whereReturnNo($return_no)->where('action','!=','自动退款')->get();
        $result = array();
        foreach($OrderReturnLog as $v){
            $content = json_decode($v['content'],true);
            $tmp = array();
            $OrderReturn = OrderReturn::whereReturnNo($v['return_no'])->where('created_at','<=',$v->created_at)->first();
            $tmp['action'] = $v['action'];
            $tmp['created_at'] = date('Y.m.d H:i',strtotime($v['created_at']));
            if($v['action'] == '退款申请'){
                $tmp['action'] = '买家发起'.$v['action'];
                $tmp['content'] = isset($content['return_content']) ? strval($content['return_content']) : strval($OrderReturn->return_content);
                $return_id = isset($content['return_id']) ? $content['return_id'] : $OrderReturn->id;
                $OrderReturnImage = OrderReturnImage::whereReturnId($return_id)->get();
                $tmp['images'] = array();
                foreach($OrderReturnImage as $vv){
                    $tmp['images'][] =  env('IMAGE_DISPLAY_DOMAIN').$vv['name'];
                }
            }else{
                $tmp['content'] = isset($content['content']) ? strval($content['content']) : '';
            }
            $result[] = $tmp;
        }


        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' =>$result);
        return response()->json($result);
    }


    /**
     * @SWG\Post(path="/v1/my/return_order",
     *   tags={"my"},
     *   summary="售后订单生成",
     *   description="",
     *   operationId="return_order",
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
     *     in="query",
     *     description="订单编号",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="order_no")
     *   ),
     *   @SWG\Parameter(
     *     name="content",
     *     in="query",
     *     description="退款原因",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="content")
     *   ),
     *   @SWG\Parameter(
     *     name="images",
     *     in="query",
     *     description="图片",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="['http://xxxx.com/image_name1.jpg','http://xxxx.com/image_name2.jpg',...]")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function addReturnOrder(Request $request)
    {
        $uid = $request->input('uid', 0);
        $order_no = $request->input('order_no', '');
        $content = $request->input('content', '');
        $images = $request->input('images', '');
        $images_arr = json_decode($images, true);
        //$images_arr = ["http://xxxx.com/image_name1.jpg","http://xxxx.com/image_name2.jpg"];


        $OrderBase = OrderBase::whereOrderNo($order_no)->first();
        //参数不正确
        if (is_null($OrderBase)){
            return response()->json(array('ret' => self::RET_FAIL, 'msg' => '订单编号不正确', 'data' => (object)array()));
        }

        /*$OrderReturn = OrderReturn::whereOrderNo($order_no)->first();
        if(is_null($OrderReturn)){*/
            $OrderReturn = new OrderReturn();
            $OrderReturn->uid = $uid;
            $OrderReturn->supplier_id = $OrderBase['supplier_id'];
            $OrderReturn->receiver_name = $OrderBase['receiver_name'];
            $OrderReturn->receiver_mobile = $OrderBase['receiver_mobile'];
            $OrderReturn->order_no = $order_no;
            $OrderReturn->return_no = 'T'.$order_no;
            $OrderReturn->return_content = $content;

            $OrderReturn->state = OrderReturn::STATE_NO_CHECK;
            $OrderReturn->save();
            foreach($images_arr as $v){
                $OrderReturnImage = new OrderReturnImage();
                $OrderReturnImage->return_id = $OrderReturn->id;
                $OrderReturnImage->name = str_replace('http://'.env("IMAGE_DOMAIN").'/', '', $v);
                $OrderReturnImage->save();
            }
            $returnLogContent['return_content'] = urlencode($content);
            $returnLogContent['return_id'] = $OrderReturn->id;
            $OrderReturnLog = new OrderReturnLog();
            $OrderReturnLog->action = '退款申请';
            $OrderReturnLog->uid = $uid;
            $OrderReturnLog->return_no = $OrderReturn->return_no;
            $OrderReturnLog->content = urldecode(json_encode($returnLogContent));
            $OrderReturnLog->save();
        /*}*/
        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => array('id' => strval($OrderReturn->id),'return_no'=>strval($OrderReturn->return_no),'tel'=>strval('400-9158-971'),'wx'=>strval('yyougo-com'),));
        return response()->json($result);
    }


    /**
     * @SWG\Get(path="/v1/my/coupon",
     *   tags={"my"},
     *   summary="获取我的优惠券",
     *   description="",
     *   operationId="getCoupon",
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
     *     name="state",
     *     in="query",
     *     description="优惠券状态(0未使用 1已使用 2过期)",
     *     required=true,
     *     type="integer",
     *     @SWG\Schema(ref="state")
     *   ),
     *   @SWG\Parameter(
     *     name="page_num",
     *     in="query",
     *     description="页码",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="page_num")
     *   ),
     *   @SWG\Parameter(
     *     name="limit",
     *     in="query",
     *     description="一页显示多少条",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="limit")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function getCoupon(Request $request){
        $uid = $request->input('uid');
        $state = $request->input('state',0);
        $page_num = intval($request->input('page_num', 0));
        $limit = intval($request->input('limit', 20));
        $offset = $page_num * $limit;

        $result = array();
        $result['list'] = array();
        $result['num'] = array(
            'unused' => CouponUser::whereUid($uid)->whereState(CouponUser::state_unused)->count(),
            'used' => CouponUser::whereUid($uid)->whereState(CouponUser::state_used)->count(),
            'expired' => CouponUser::whereUid($uid)->whereState(CouponUser::state_expired)->count(),
        );
        if($state == 0 || $state == 2){
            $CouponUsers = CouponUser::whereUid($uid)->whereState($state)->offset($offset)->limit($limit)->orderBy('coupon_id','desc')->get();
        }else{
            $CouponUsers = CouponUser::whereUid($uid)->whereState($state)->offset($offset)->limit($limit)->orderBy('used_time','desc')->get();
        }

        foreach($CouponUsers as $couponUser){
            $tmp = array();
            $tmp['coupon_user_id'] = strval($couponUser->id);
            $tmp['coupon_id'] = strval($couponUser->coupon_id);
            $tmp['amount_coupon'] = strval($couponUser->amount_coupon);
            $tmp['amount_order'] = strval($couponUser->amount_order);
            $tmp['start_time'] = strval($couponUser->start_time);
            $tmp['end_time'] = strval($couponUser->end_time);
            $tmp['state'] = strval($couponUser->state);
            $result['list'][] = $tmp;
        }

        $result = array('ret' => self::RET_SUCCESS, 'msg' => '','data' =>$result);
        return response()->json($result);
    }


    /**
     * @SWG\get(path="/v1/my/wx_qrcode",
     *   tags={"my"},
     *   summary="我的微信公众号二维码",
     *   description="",
     *   operationId="getWxQrCode",
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
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function getWxQrCode(Request $request)
    {
        $uid = $request->input('uid', 0);
        $GuideBase = GuideBase::whereUid($uid)->first();
        $UserBase = UserBase::whereId($uid)->first();
        $data['nick_name'] = $UserBase['nick_name'] == '' ? '昵称未设置' : $UserBase['nick_name'];
        $data['wx_qrcode'] = $GuideBase['qrcode'] == '' ? '' : 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$GuideBase['wx_qrcode'];
        return view('api.wx_qrcode',compact('data'));
    }
    
    
    /**
     * @SWG\post(path="/v1/my/comment",
     *   tags={"my"},
     *   summary="提交商品评论",
     *   description="",
     *   operationId="saveComments",
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
     *     in="query",
     *     description="订单号",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="order_no")
     *   ),
     *  @SWG\Parameter(
     *     name="comments",
     *     in="query",
     *     description="商品评价的内容",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="comments")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function saveComments(Request $request){
        //接参数
        Log::alert($request->all());
        $uid = intval($request->input('uid',0));
        $order_no = $request->input('order_no');
        $comments = $request->input('comments');
        $comments = json_decode($comments,true);
        //预判
        if($uid == 0){
            return response()->json(['ret'=>self::RET_FAIL,'msg'=>'请传递用户的uid','data' => '']);
        }
        
        if(empty($order_no)){
            return response()->json(['ret'=>self::RET_FAIL,'msg'=>'请传递订单号','data' => '']);
        }
        
        foreach($comments as $val){
            if(empty($val['spec_id'])){
                return response()->json(['ret'=>self::RET_FAIL,'msg'=>'请传递商品规格的id','data' => '']);
            }
            
            if(empty($val['content'])){
                return response()->json(['ret'=>self::RET_FAIL,'msg'=>'请填写评论内容','data' =>'']);
            }
        } 

        foreach($comments as $val){
            
            $CommentGood = new CommentGood();
            $CommentGood->uid      = $uid;
            $CommentGood->order_no = $order_no;
            $CommentGood->goods_id = $val['goods_id'];
            $CommentGood->spec_id  = $val['spec_id'];
            $CommentGood->comment  = $val['content'];
            $CommentGood->save();
            $CommentGoodId = $CommentGood->id;
            if(!empty($val['image'])){
                foreach($val['image'] as $img){
                    $CommentGoodsImage = new CommentGoodsImage();
                    $CommentGoodsImage->comment_id = $CommentGoodId;
                    $CommentGoodsImage->image_name = basename($img);//取出图片的名字
                    $CommentGoodsImage->save();
                }
            }
        }
        
        return response()->json(['ret'=>self::RET_SUCCESS,'msg'=>'评论添加成功','data' => '']);
    }
    
    
    /**
     * @SWG\Get(path="/v1/my/orderlist",
     *   tags={"my"},
     *   summary="获取我的已完成订单列表",
     *   description="",
     *   operationId="getCompletedOrderList",
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
     *     name="page_num",
     *     in="query",
     *     description="页码",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="page_num")
     *   ),
     *   @SWG\Parameter(
     *     name="limit",
     *     in="query",
     *     description="一页显示多少条",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="limit")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function getCompletedOrderList(Request $request){
        $uid      = intval($request->input('uid',0));
        $page_num = intval($request->input('page_num', 0));
        $limit    = intval($request->input('limit', 20));
        $offset   = $page_num * $limit;
        if(empty($uid)){
            return response()->json(['ret'=>self::RET_FAIL,'msg'=>'请传递用户的id','data' => '']);
        }
        $orders   = OrderBase::whereUid($uid)->whereState(OrderBase::STATE_FINISHED)->orderBy('id','desc')->offset($offset)->limit($limit)->get();
        //进行是否评价的判断
        foreach($orders as $order){
            $isComment = CommentGood::whereOrderNo($order->order_no)->get()->toArray();
            if(empty($isComment)){
                //无评论
                $order->iscomment = 0;
            }else{
                //有评论
                $order->iscomment = 1;
            }
        }
        //dd($orders);
        $result   = array('ret' => self::RET_SUCCESS, 'msg' => '已完成订单列表获取成功', 'data' =>self::formatOrderData($orders) );
        return response()->json($result);
    }
    
    
    /**
     * @SWG\get(path="/v1/my/order/{order_no}/comments",
     *   tags={"my"},
     *   summary="查询商品评论",
     *   description="",
     *   operationId="getOrderComment",
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
     *     description="订单号",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="order_no")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function getOrderComment(Request $request){
        $order_no = $request->input('order_no');
        if(empty($order_no)){
            return response()->json(['ret'=>self::RET_FAIL,'msg'=>'请传递订单号','data' => '']);
        }
        $ordergoods = OrderGood::whereOrderNo($order_no)->where('is_gift','<>',1)->get();
        foreach($ordergoods as $good){
            $goodsData = GoodsBase::whereId($good->goods_id)->first();
            $good->cover_image = env('IMAGE_DISPLAY_DOMAIN').$goodsData['first_image'];
            $comments  = CommentGood::whereOrderNo($good->order_no)->whereGoodsId($good->goods_id)->whereSpecId($good->spec_id)->first();
            $good->comments = $comments;
            $commentimage   = CommentGoodsImage::whereCommentId($comments->id)->select('image_name')->get()->toArray();
            $images = array() ;
            if(!empty($commentimage)){
                foreach($commentimage as $img){
                    $images[] = env('IMAGE_DISPLAY_DOMAIN').$img['image_name'];
                }
            }
            $good->images  = $images;
        }
        return response()->json(['ret'=>self::RET_SUCCESS,'msg'=>'评论加载成功','data'=>$ordergoods]);
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    

}

