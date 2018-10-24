<?php namespace App\Http\Controllers\Api;

use App\Models\CouponGood;
use App\Models\CouponUser;
use App\Models\GoodsGift;
use App\Models\PlatformBilling;
use App\Models\SupplierExpress;
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


class CartController extends SignController
{

        /**
     * @SWG\Post(path="/v1/cart",
     *   tags={"cart"},
     *   summary="添加购物车",
     *   description="",
     *   operationId="cart",
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
     *
     *   @SWG\Parameter(
     *     name="goods_id",
     *     in="query",
     *     description="商品id",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="goods_id")
     *   ),
     *   @SWG\Parameter(
     *     name="spec_id",
     *     in="query",
     *     description="规格id",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="spec_id")
     *   ),
     *  @SWG\Parameter(
     *     name="num",
     *     in="query",
     *     description="数量，默认是1",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="num")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function addCart(Request $request)
    {
        $uid = $request->input('uid', 0);
        $goods_id = $request->input('goods_id', 0);
        $spec_id = $request->input('spec_id', 0);
        $num = $request->input('num', 1);

        $GoodsBase = GoodsBase::whereId($goods_id)->first();
        $supplier_id = isset($GoodsBase['supplier_id']) ? $GoodsBase['supplier_id'] : '0';
        $GoodsSpecNum = GoodsSpec::whereId($spec_id)->whereGoodsId($goods_id)->count();

        //参数不正确
        if ( $GoodsSpecNum == 0){
            return response()->json(array('ret' => self::RET_FAIL, 'msg' => self::PARAMETER_ERROR, 'data' => (object)array()));
        }

        $UserCart = UserCart::whereUid($uid)->whereSupplierId($supplier_id)->whereGoodsId($goods_id)->whereSpecId($spec_id)->first();
        if(is_null($UserCart)){
            $UserCart = new UserCart();
            $UserCart->uid = $uid;
            $UserCart->supplier_id = $supplier_id;
            $UserCart->goods_id = $goods_id;
            $UserCart->spec_id = $spec_id;
        }else{
            $num = $num + $UserCart->num;
        }
        $UserCart->num = $num;
        $UserCart->save();

        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => array('id' => strval($UserCart->id)));
        return response()->json($result);
    }

    /**
     * @SWG\Delete(path="/v1/cart",
     *   tags={"cart"},
     *   summary="删除购物车",
     *   description="",
     *   operationId="cart",
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
     *     name="ids",
     *     in="query",
     *     description="购物车id",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="id")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function deleteCart(Request $request)
    {
        $uid = $request->input('uid', 0);
        $ids = $request->input('ids', 0);
        $id_array = explode(',',$ids);


        $UserCartNum = UserCart::whereIn('id',$id_array)->whereUid($uid)->count();
        //参数不正确
        if ( $UserCartNum == 0){
            return response()->json(array('ret' => self::RET_FAIL, 'msg' => self::PARAMETER_ERROR, 'data' => (object)array()));
        }
        UserCart::whereIn('id',$id_array)->whereUid($uid)->delete();

        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => $id_array);
        return response()->json($result);
    }

    /**
     * @SWG\Post(path="/v1/cart/{id}",
     *   tags={"cart"},
     *   summary="改购物车数量",
     *   description="",
     *   operationId="cart",
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
     *     name="id",
     *     in="path",
     *     description="购物车id",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="id")
     *   ),
     *   @SWG\Parameter(
     *     name="num",
     *     in="query",
     *     description="购物车数量",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="num")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function modifyCart(Request $request,$id)
    {
        $uid = $request->input('uid', 0);
        $num = $request->input('num', 0);


        $UserCart = UserCart::whereId($id)->whereUid($uid)->first();
        //参数不正确
        if (is_null($UserCart)){
            return response()->json(array('ret' => self::RET_FAIL, 'msg' => self::PARAMETER_ERROR, 'data' => (object)array()));
        }
        $UserCart->num = $num;
        $UserCart->save();

        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => array('id' => strval($UserCart->id)));
        return response()->json($result);
    }


    /**
     * @SWG\Get(path="/v1/cart",
     *   tags={"cart"},
     *   summary="获取购物车",
     *   description="",
     *   operationId="cart",
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
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function getCart(Request $request)
    {
        $uid = $request->input('uid', 0);

        $result = array();
        $supplier_ids = UserCart::whereUid($uid)->lists('supplier_id')->toArray();
        $supplier_ids = array_unique($supplier_ids);
        foreach($supplier_ids as $supplier_id){

            $supplierResult = array();
            $store = array();
            $SupplierBase = SupplierBase::whereId($supplier_id)->first();
            $SupplierExpress = SupplierExpress::whereSupplierId($supplier_id)->first();
            $store['id'] = strval($supplier_id);
            $store['name'] = $SupplierBase['store_name'];
            $store['logo'] = $SupplierBase['store_logo'] != '' ? env('IMAGE_DISPLAY_DOMAIN').$SupplierBase['store_logo'] : '';
            $store['free_express_amount'] = isset($SupplierExpress['total_amount']) ? $SupplierExpress['total_amount'] : '0';
            $store['express_amount'] = isset($SupplierExpress['express_amount']) ? $SupplierExpress['express_amount'] : '0';
            $store['is_pick_up'] = strval($SupplierBase['is_pick_up']);
            $supplierResult['store'] = $store;

            $supplierResult['goods'] = array();
            $UserCartGoods = UserCart::whereUid($uid)->whereSupplierId($supplier_id)->get();
            foreach($UserCartGoods as $goods){
                $tmp = array();
                $GoodsBase = GoodsBase::whereId($goods['goods_id'])->first();
                $tmp['id'] = strval($goods['id']);
                $tmp['goods_id'] = strval($goods['goods_id']);
                $tmp['goods_title'] = strval($GoodsBase['title']);
                $tmp['goods_cover'] = env('IMAGE_DISPLAY_DOMAIN').$GoodsBase['first_image'].'?imageslim';
                $GoodsSpec = GoodsSpec::whereId($goods['spec_id'])->first();
                $tmp['spec_id'] = strval($goods['spec_id']);
                $tmp['spec_name'] = $GoodsSpec['name'];
                $tmp['spec_price'] = strval($GoodsSpec['price']);
                $tmp['num'] = strval($goods['num']);
                $tmp['num_goods'] = strval($GoodsSpec['num']);
                $tmp['num_limit'] = strval($GoodsSpec['num_limit']);
                $tmp['is_coupon_goods'] = self::getIsCouponGoods($goods['goods_id']);
                if($GoodsBase['state'] != 1){
                    $tmp['state'] = '2';
                }else{
                    $tmp['state'] = '1';
                }

                $gift = array();
                $goodsGift = GoodsGift::whereGoodsId($goods['goods_id'])->get();
                foreach($goodsGift as $v){
                    $giftTemp = array();
                    $giftGoods = GoodsBase::whereId($v['gift_id'])->first();
                    $giftTemp['goods_id'] = strval($v['gift_id']);
                    $giftTemp['name'] = strval($giftGoods['title']);
                    $gift[] = $giftTemp;
                }
                $tmp['gift'] = $gift;
                $supplierResult['goods'][] = $tmp;
            }

            $supplierResult['coupon'] = array();
            $CouponUser = CouponUser::whereUid($uid)->whereSupplierId($supplier_id)->whereState(CouponUser::state_unused)->orderBy('amount_order','desc')->get();
            if(count($CouponUser) > 0){
                $supplierResult['coupon'] = OrderController::formatCoupon($CouponUser);
            }




            $result[] = $supplierResult;
        }

        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => $result);
        return response()->json($result);
    }




}

