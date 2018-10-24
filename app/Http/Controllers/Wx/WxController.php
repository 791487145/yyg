<?php

namespace App\Http\Controllers\Wx;

use App\Models\CouponBase;
use App\Models\CouponGood;
use App\Models\CouponUser;
use App\Models\GoodsBase;
use App\Models\GoodsGift;
use App\Models\GoodsSpec;
use App\Models\SupplierBase;
use App\Models\SupplierExpress;
use App\Models\UserCart;
use App\Models\UserWx;
use Illuminate\Http\Request;
use Log;
use Cookie;
use App\Http\Requests;
use App\Http\Controllers\GenController;

class WxController extends GenController
{
    public $user = array();
    const page = 10;

    static public function setWxAuth($request, $redirect_uri)
    {

        if (Cookie::get('openid') != '' && env('APP_ENV') == 'production') {
            Log::alert('cookie open_id:' . Cookie::get('openid'));
            return Cookie::get('openid');
        }
        $return_code = $request->input('code', 0);
        $ta_id = $request->input('taid', 0);
        $guide_id = $request->input('gid', 0);


        $appid = env("WX_APPID");
        $secret = env("WX_APPSECRET");
        $response_type = 'code';
        $scope = "snsapi_base";
        $state = '123';

        if ($return_code != 0) {
            $code = $return_code;

            Log::alert('wechat login success,return code:' . $code);
        } else {
            Log::alert('begin wechat login:');
            $curl = urlencode('http://' . env('H5_DOMAIN') . $redirect_uri);
            $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $appid . '&redirect_uri=' . $curl . '&response_type=' . $response_type . '&scope=' . $scope . '&state=' . $state . '#wechat_redirect';
            Log::alert('after url:' . $url);
            header("Location:" . $url);
            exit;
        }

        //特殊情况
        $UserWx = UserWx::whereCode($code)->first();
        if($UserWx){
            return $UserWx['open_id'];
        }else{
            $get_token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $appid . '&secret=' . $secret . '&code=' . $code . '&grant_type=authorization_code';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $get_token_url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            $res = curl_exec($ch);
            curl_close($ch);
            $json_obj = json_decode($res, true);
            Log::alert($json_obj);

            //更新记录
            if(isset($json_obj['openid'])){
                $UserWx = UserWx::whereOpenId($json_obj['openid'])->first();
                if (is_null($UserWx)) {
                    $UserWx = new UserWx();
                    $UserWx->open_id = $json_obj['openid'];
                    $UserWx->union_id = $json_obj['unionid'];
                    $UserWx->code = $code;
                    $UserWx->ta_id = $ta_id;
                    $UserWx->guide_id = $guide_id;
                    $UserWx->saveOrFail();
                }else{
                    $UserWx->union_id = $json_obj['unionid'];
                    $UserWx->code = $code;
                    $UserWx->save();
                }

            }

        }

        return isset($json_obj['openid']) ? $json_obj['openid'] : '';
    }


    protected function formatCartData($UserCart)
    {
        $supplier = array();
        foreach ($UserCart as $v) {
            $supplier[$v['supplier_id']][] = array('spec_id' => $v['spec_id'], 'num' => $v['num'], 'cart_id' => $v['id']);
        }
        $data = array();
        $data['total_amount'] = 0;
        $data['list'] = array();
        $open_id = Cookie::get('openid');
        $uid = UserWx::whereOpenId($open_id)->pluck('uid');
        //$uid = 24;
        foreach ($supplier as $k => $spec) {
            $tmp = array();
            $SupplierBase = SupplierBase::whereId($k)->first();
            $tmp['is_pick_up'] = $SupplierBase->is_pick_up;//是否允许自提
            $tmp['store_name'] = $SupplierBase['store_name'];
            $tmp['goods'] = array();
            $tmp['supplier_id'] = $k;
            $tmp['num'] = 0;
            $tmp['amount'] = 0;
            $CouponUsers = CouponUser::whereState(0)->whereUid($uid)->whereSupplierId($k)->orderBy('amount_coupon','desc')->get()->toArray();
            //dd($CouponUsers);
            foreach ($spec as $v) {
                $t = array();
                $GoodsSpec = GoodsSpec::whereId($v['spec_id'])->first();
                $GoodsBase = GoodsBase::whereId($GoodsSpec['goods_id'])->first();

                $t['image'] = env('IMAGE_DISPLAY_DOMAIN') . $GoodsBase->first_image;
                $t['cart_id'] = $v['cart_id'];
                $t['title'] = $GoodsBase['title'];
                //优惠券
                $CouponGoods = CouponGood::whereGoodsId($GoodsSpec['goods_id'])->whereState(CouponGood::STATE_NORMAL)->get();
                if(!$CouponGoods->isEmpty()){
                    $t['coupon_state'] = "优惠券商品";
                    $tmp['coupon_goods'] = 1;
                }
                if(!$CouponGoods->isEmpty() && !empty($CouponUsers)){
                    $tmp['coupon_exits'] = 1;
                }

                $t['spec_state'] = $GoodsBase['state'];
                $t['spec'] = $GoodsSpec['name'];
                $t['spec_num'] = $GoodsSpec['num'];
                if($GoodsSpec['num_limit'] > 0) {
                    $t['spec_limit'] = $GoodsSpec['num_limit'];
                }
                $t['price'] = $GoodsSpec['price'];
                $t['express_fee_mode'] = $GoodsSpec['express_fee_mode'];
                $t['is_pick_up'] = $GoodsSpec['is_pick_up'];
                $t['good_id'] = $GoodsBase['id'];
                $t['num'] = $v['num'];

                $t['gift'] = array();
                $GoodsGift = GoodsGift::whereGoodsId($GoodsSpec['goods_id'])->get();
                foreach($GoodsGift as $vv){
                    $GoodsBaseGift = GoodsBase::whereId($vv['gift_id'])->whereState(GoodsBase::state_online)->first();
                    if(!empty($GoodsBaseGift)){
                        $GoodsSpecGift = GoodsSpec::whereId($vv['spec_id'])->first();
                        $t['gift'][] = array('title' => $GoodsBaseGift['title'], 'id' => $GoodsBaseGift['id'],'spec_name'=>$GoodsSpecGift['name'],'cover_image'=>$GoodsBaseGift['first_image']);
                    }
                }

                $tmp['goods'][] = $t;
                $tmp['num'] = $tmp['num'] + $t['num'];
                $tmp['amount'] = $tmp['amount'] + ($t['price'] * $t['num']);
            }

            //优惠卷逻辑
            $param = array();
            if(isset($tmp['coupon_exits']) && $tmp['coupon_exits'] = 1){
                $tmp['coupon_base'] = $CouponUsers;
                foreach($CouponUsers as $couponUser){
                    if($couponUser['amount_order'] <= $tmp['amount']){
                        $param[] = $couponUser;
                    }
                }
            }
            //小计数额满足部分优惠券
            if(!empty($param)){
                $tmp['counpon_state'] = 1;
                $tmp['coupon_order'] = $param[0]['amount_order'];
                $tmp['coupon_amount'] = $param[0]['amount_coupon'];
                $tmp['coupon_user_id'] = $param[0]['id'];
                $tmp['coupon_amount_num'] = $param[0]['amount_coupon'];
            }
            //小计数额不满足全部优惠券
            if(isset($tmp['coupon_exits']) && $tmp['coupon_exits'] == 1 && empty($param)){
                $tmp['coupon_exits'] = 2;
                $tmp['coupon_amount_num'] = 0;
            }

            //没有优惠券
            if(!isset($tmp['coupon_exits']) && !isset($tmp['coupon_goods'])){
                $tmp['coupon_exits'] = 0;
                $tmp['coupon_amount_num'] = 0;
                $tmp['coupon_amount'] = 0;
            }
            if(!isset($tmp['coupon_exits']) && isset($tmp['coupon_goods'])){
                $tmp['coupon_exits'] = 2;
                $tmp['coupon_amount_num'] = 0;
            }

            $data['total_amount'] = $data['total_amount'] - $tmp['coupon_amount_num'];

            //运费逻辑
            $express_type = SupplierExpress::whereSupplierId($tmp['supplier_id'])->first();
            $tmp['express_type'] = 1;
            $tmp['total_amount'] = $express_type->total_amount;//满
            $tmp['express_amount'] = $express_type->express_amount;//运费

            if($tmp['total_amount'] > $tmp['amount']){
                $tmp['express_type_select'] = 2;//有运费
                $tmp['express_amount_num'] = $express_type->express_amount;
                $data['total_amount'] = $data['total_amount'] + $tmp['express_amount'];
            }else{
                $tmp['express_type_select'] = 1;//无运费
                $tmp['express_amount_num'] = 0;
            }

            $data['list'][] = $tmp;
            $data['total_amount'] = bcadd($data['total_amount'],$tmp['amount'],2)  ;
        }

        return $data;
    }

    protected function count($openid)
    {
        $count = UserCart::whereOpenId($openid)->count();
        return $count;
    }


}
