<?php

namespace App\Http\Controllers\Wx;

use Cookie;
use App\Models\UserWx;
use App\Http\Requests;
use App\Models\UserCart;
use App\Models\GoodsBase;
use App\Models\GoodsSpec;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use App\Models\SupplierBase;

class CartController extends WxController
{
    public function cartLists()
    {
        $open_id = Cookie::get('openid');
        $UserCarts_id = UserCart::whereOpenId($open_id)->get();
        $Suppliers = $this->formatCartData($UserCarts_id);
        //dd($Suppliers);
        $count = self::count($open_id);
        $state = 2;
        return view('wx.cart.cart',compact('Suppliers','count','state'));
    }

    public function cartInsert(Request $request)
    {
        //down 商品下架；no:库存不足；not 没选商品；address 没有默认地址；shop 提交订单； yes 加入购物车成功
        $good_id = $request->input('good_id',0);
        $num = $request->input('num',1);
        $num = (int)$num;
        $spec_id = $request->input('spec_id','');
        $taid = $request->input('taid',0);
        $gid = $request->input('gid',0);
        $cart_id = $request->input('cart_id',0);
        $cart = $request->input('cart','');//详情页购买shop
        $open_id = $request->input('open_id','');
        if($open_id == ''){
            $open_id = Cookie::get('openid');
        }
        //商品是否已下架
        if($good_id != 0){
            $goodDetailState = GoodsBase::whereState(GoodsBase::state_online)->whereId($good_id)->first();
            if(is_null($goodDetailState)){
                return response()->json(['ret' => 'down']);
            }
        }

        if($num == 0){//商品数>1
            return response()->json(['ret' => 'not']);
        }

        $UserCart = new UserCart();
        //购物车页面中添加
        if($cart_id != 0){
            $UserCarts = $UserCart->whereId($cart_id)->first();
            $UserCarts->num = $num;
            $spec_id = GoodsSpec::whereId($UserCarts->spec_id)->first();
            if($spec_id->num_limit != 0 && $spec_id->num_limit < $num){
                return response()->json(['ret' => 'no']);
            }
            if($spec_id->num >= $UserCarts->num ){
                $UserCart->whereId($cart_id)->update(['num' => $UserCarts->num]);//1
                $count = self::count($open_id);
                return response()->json(['ret' => 'yes','count'=>$count]);
            }else{
                return response()->json(['ret' => 'no']);
            }
        }
        //主页，详情页
        if($spec_id == ''){//规格
            $spec_id = GoodsSpec::whereGoodsId($good_id)->first();
        }else{
            $spec_id = GoodsSpec::whereId($spec_id)->first();
        }
        if($spec_id->num_limit != 0 && $spec_id->num_limit < $num){
            return response()->json(['ret' => 'no']);
        }
        $UserCarts = $UserCart->whereSpecId($spec_id->id)->whereOpenId($open_id)->whereGoodsId($good_id)->first();
        $GoodBase = GoodsBase::whereId($good_id)->select('supplier_id')->first();
        //购物车中有原商品
        if($UserCarts != ''){
            //商品详情页直接购买
            if($spec_id->num >= $num ) {
                if ($cart == 'shop') {
                    UserCart::whereOpenId($open_id)->update(array('is_selected' => 0));
                    UserCart::whereOpenId($open_id)->whereId($UserCarts->id)->update(array('is_selected' => 1,'num' => $num,'guide_id'=>$gid,'ta_id'=>$taid));
                    return response()->json(['ret' => 'shop']);
                }
            }
            $num = $UserCarts->num + $num;
            if($spec_id->num >= $num ){
                $UserCart->whereId($UserCarts->id)->update(['num' => $num]);//1
                $count = self::count($open_id);
                return response()->json(['ret' => 'yes','count' => $count]);
            }
            return response()->json(['ret' => 'no']);//购物车数量大于库存
        }
        //第一次加入购物车
        if($spec_id->num < $num){
            return response()->json(['ret' => 'no']);
        }
        $UserCart->supplier_id = $GoodBase->supplier_id;
        $UserCart->goods_id = $good_id;
        $UserCart->spec_id = $spec_id->id;
        $UserCart->num = $num;
        $UserCart->open_id = $open_id;
        $UserCart->ta_id = $taid;
        $UserCart->guide_id = $gid;
        $UserCart->save();

        if($cart == 'shop'){
            if($spec_id->num >= $num){
                UserCart::whereOpenId($open_id)->update(array('is_selected'=>0));
                UserCart::whereOpenId($open_id)->whereId($UserCart->id)->update(array('is_selected' => 1));
                return response()->json(['ret' => 'shop']);
            }
            UserCart::whereId($UserCart->id)->delete();
            return response()->json(['ret' => 'no']);//购物车数量大于库存
        }
        $count = self::count($open_id);
        return response()->json(['ret' => 'yes','count' => $count]);
    }

    public function cartDel(Request $request)
    {
        $cart_ids = $request->input('cart_id');
        $UserCart = new UserCart();
        $res = $UserCart->whereIn('id',$cart_ids)->delete();
        return response()->json(['ret'=>'yes','res'=>$res]);
    }


    public function cartSelected(Request $request)
    {
        $open_id = Cookie::get('openid');
        $cart_ids = $request->input('cart_id',array());
        UserCart::whereOpenId($open_id)->update(array('is_selected'=>0));
        if(!empty($cart_ids)){
            UserCart::whereOpenId($open_id)->whereIn('id',$cart_ids)->update(array('is_selected'=>1));
        }
        return  response()->json(['id',$cart_ids]);
    }
}
