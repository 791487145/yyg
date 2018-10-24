<?php

namespace App\Http\Controllers\Admin;

use App\Models\CouponBase;
use App\Models\CouponGood;
use App\Models\CouponUser;
use App\Models\GoodsBase;
use App\Models\SupplierBase;
use App\Models\UserBase;
use Illuminate\Http\Request;
use Cookie;
use Log;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class CouponController extends Controller
{

    //用户领取优惠券
    static public function couponUser($uid,$coupon_id,$order_no,$supplier_id){
        $coupon = self::getCouponInfo($coupon_id);
        Log::alert('$coupon数据:' . print_r($coupon, true));
        $couponUserId = self::saveCouponUser($uid,$coupon,$order_no,$supplier_id);
        return $couponUserId;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $now = date('Y-m-d H:i:s');
        $couponSupplierIds = array_unique(CouponBase::lists('supplier_id')->toArray());
        $supplierBase = SupplierBase::whereIn('id',$couponSupplierIds)->get();
        foreach($supplierBase as $v){
            $v->goodsId = CouponGood::whereSupplierId($v->id)->lists('goods_id');
            $supplierCouponIds = array_unique(CouponBase::where('supplier_id',$v->id)->lists('id')->toArray());
            $couponInfos = CouponBase::whereIn('id',$supplierCouponIds)->orderBy('id','desc')->get();
            foreach($couponInfos as $coupon){
                if($coupon->end_time < $now){
                    $coupon->expired = 1;
                }
            }
            $v->couponInfos = $couponInfos;
        }
        return view('boss.coupon.coupon_lists',compact('supplierBase'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $suppliers = SupplierBase::whereState(SupplierBase::STATE_VALID)->get();
        return view('boss.coupon.add',compact('suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $formData = $request->all();
        if($formData['amount_coupon'] > $formData['amount_order']){
            return $data = ['ret'=>'no','msg'=>'优惠金额不能大于订单金额'];
        }
        $coupon = new CouponBase();
        $coupon->title = $formData['title'];
        $coupon->supplier_id = $formData['supplier_id'];
        $coupon->send_type = $formData['send_type'];
        $coupon->amount_order = $formData['amount_order'];
        $coupon->amount_coupon = $formData['amount_coupon'];
        $coupon->start_time = $formData['start_time'];
        $coupon->end_time = $formData['end_time'];
        $coupon->state = CouponBase::state_normal;
        $coupon->save();

        return $data = ['ret'=>'yes','msg'=>'添加成功'];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id,$state = 0)
    {
        $couponUser = CouponUser::whereCouponId($id)->whereState($state)->get();
        foreach($couponUser as $value){
            $user = UserBase::whereId($value->uid)->first();
            if(!is_null($user)){
                $value->identity = $user->is_guide == 1 ? '导游' : '游客';
                $value->name = $user->nick_name == '' ? 'UID.'.$user->id : $user->nick_name;
                $value->mobile = $user->mobile;
            }
        }
        return view('boss.coupon.show',compact('couponUser','id','state','count'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $coupon = self::getCouponInfo($id);
        $supplier_id = CouponGood::whereCouponId($id)->pluck('supplier_id');
        $supplier = SupplierBase::whereId($supplier_id)->first();
        return view('boss.coupon.edit',compact('coupon','supplier','id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $formData = $request->all();
        if(empty($formData['goods_id'])){
            return $data= ['ret'=>'no','msg'=>'商品不能为空'];
        }
        $couponGoods = CouponGood::whereCouponId($id)->whereState(CouponGood::STATE_NORMAL)->lists('goods_id')->toArray();
        $cutArray = array_diff($couponGoods,$formData['goods_id']);
        $addArray = array_diff($formData['goods_id'],$couponGoods);
        if(!empty($cutArray)){
            foreach($cutArray as $value){
                CouponGood::whereCouponId($id)->whereGoodsId($value)->update(['state'=>CouponGood::STATE_DELETE]);
            }
        }
        if(!empty($addArray)){
            foreach($addArray as $value){
                $supplier_id = GoodsBase::whereId($value)->pluck('supplier_id');
                $couponSaveGoods = new CouponGood();
                $couponSaveGoods->coupon_id = $id;
                $couponSaveGoods->goods_id = $value;
                $couponSaveGoods->supplier_id = $supplier_id;
                $couponSaveGoods->state = CouponGood::STATE_NORMAL;
                $couponSaveGoods->save();
            }
        }
        $data['title'] = $formData['title'];
        $data['send_type'] = $formData['send_type'];
        $data['amount_order'] = $formData['amount_order'];
        $data['amount_coupon'] = $formData['amount_coupon'];
        $data['start_time'] = $formData['start_time'];
        $data['end_time'] = $formData['end_time'];
        $data['state'] = $formData['state'];
        CouponBase::whereId($id)->update($data);
        return $data = ['ret'=>'yes','msg'=>'更新成功'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $coupon = CouponBase::whereId($id)->update(['state'=>CouponBase::state_delete]);
        if($coupon){
            return $data = ['state'=>'yes','msg'=>'删除成功'];
        }else{
            return $data = ['state'=>'no','msg'=>'删除失败'];
        }
    }

    public function supplierCouponGoods($supplier_id){
        $supplierGoods = GoodsBase::whereSupplierId($supplier_id)->whereState(GoodsBase::state_online)->get();
        $selectGoods = CouponGood::whereSupplierId($supplier_id)->lists('goods_id')->toArray();
        return view('boss.coupon.goods',compact('supplierGoods','supplier_id','selectGoods'));
    }

    public function supplierAddCouponGoods($supplier_id,Request $request){
        $goodsIds = $request->input('goodsId',array());
        $existsGoodsIds = CouponGood::whereSupplierId($supplier_id)->lists('goods_id')->toArray();
        $addGoodsIds = array_diff($goodsIds,$existsGoodsIds);
        $deleteGoodsIds = array_diff($existsGoodsIds,$goodsIds);
        if(!empty($addGoodsIds)){
            foreach($addGoodsIds as $goodsId){
                $couponGoodsBase = CouponGood::whereSupplierId($supplier_id)->whereGoodsId($goodsId)->first();
                if(is_null($couponGoodsBase)){
                    $couponGoods = new CouponGood();
                    $couponGoods->coupon_id = 0;
                    $couponGoods->goods_id = $goodsId;
                    $couponGoods->supplier_id = $supplier_id;
                    $couponGoods->state = CouponGood::STATE_NORMAL;
                    $couponGoods->save();
                }
            }
        }
        if(!empty($deleteGoodsIds)){
            foreach($deleteGoodsIds as $goodsId){
                   CouponGood::whereSupplierId($supplier_id)->whereGoodsId($goodsId)->delete();
            }
        }
        return $supplier_id;
    }

    //获取优惠券信息
    static private function getCouponInfo($id){
        $coupon = CouponBase::whereId($id)->first();
        return $coupon;
    }

    //记录用户优惠券绑定关系
    static private function saveCouponUser($uid,$couponBase,$order_no,$supplier_id){

        $open_id = Cookie::get('openid');
        $couponUser = CouponUser::whereUid($uid)->whereCouponId($couponBase->id)->whereState(CouponUser::state_unused)->first();
        if(is_null($couponUser)){
            $couponUser = new CouponUser();
            $couponUser->uid = $uid;
            $couponUser->open_id = $open_id;
            $couponUser->supplier_id = $supplier_id;
            $couponUser->send_source = $order_no;
            $couponUser->coupon_id = $couponBase->id;
            $couponUser->title = $couponBase->title;
            $couponUser->amount_order = $couponBase->amount_order;
            $couponUser->amount_coupon = $couponBase->amount_coupon;
            $couponUser->start_time = $couponBase->start_time;
            $couponUser->end_time = $couponBase->end_time;
            $couponUser->state = CouponUser::state_unused;
            $couponUser->save();
        }
        return $couponUser->id;
    }


    //获取优惠券供应商对应商品
   /* public function supplierGoods($id,$coupon_id = 0){

        $goods = GoodsBase::whereSupplierId($id)->whereState(GoodsBase::state_online)->get();
        if($coupon_id){
            $coupon_goods = CouponGood::whereSupplierId($id)->whereCouponId($coupon_id)->whereState(CouponGood::STATE_NORMAL)->lists('goods_id')->toArray();
            foreach($goods as $value){
                if(in_array($value->id,$coupon_goods)){
                    $value->checked = 1;
                }
            }
        }
        return $goods;

    }*/

    //优惠券使用和未使用的条数
    public function couponUseStateCount($id,$state){
        $count = CouponUser::whereCouponId($id)->whereState($state)->count();
        return $count;
    }



}
