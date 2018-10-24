<?php

namespace App\Http\Controllers\Admin;

use App\Models\ConfCategory;
use App\Models\ConfPavilion;
use App\Models\GoodsBase;
use App\Models\GoodsCategory;
use App\Models\GoodsDesc;
use App\Models\GoodsExt;
use App\Models\GoodsGift;
use App\Models\GoodsImage;
use App\Models\GoodsOptLog;
use App\Models\GoodsSpec;
use App\Models\PlatformSm;
use App\Models\SupplierBase;
use App\Models\SupplierExpress;
use App\Models\UserCart;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Admin\BaseController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use Auth;
use Maatwebsite\Excel\Facades\Excel;

class GoodsController extends BaseController
{
    //商品下架
    const goodsDown = 11;
    //橱窗商品
    const location = 1;

    /* 显示列表页，搜索页 */
    public function index($state = 1)
    {
        //声明允许接收参数
        $states = [1, 'location', 2];
        if (in_array($state, $states)) {
            if ($state == 'location') {
                $goods = GoodsBase::whereLocation(1)->whereState(1)->orderBy('location_order','desc');
            } else {
                $goods = GoodsBase::whereState($state)->orderBy('id','desc');
            }
            $suppliers = [];
            $goodsSupplier = $goods->get();
            $confPavilions = ConfPavilion::whereState(ConfPavilion::state_online)->get();
            foreach($goodsSupplier as $val){
                $supplier = SupplierBase::whereId($val->supplier_id)->first();
                if($supplier){
                    $suppliers[$supplier->id] = $supplier->name;
                }
            }
            //接收参数
            $title = Input::get('title');
            $category_id = Input::get('category_id');
            $supplier_id = Input::get('supplier_id');
            $num_sold_start = Input::get('num_sold_start');
            $num_sold_end = Input::get('num_sold_end');
            $pavilion_id = Input::get('pavilion_id',0);

            //判断参数
            if ($title) {
                $goods = $goods->where('title', 'like', '%' . $title . '%');
            }
            if ($category_id != 0) {
                $goods = $goods->whereCategoryId($category_id);
            }
            if($pavilion_id != 0){
                $goods = $goods->wherePavilion($pavilion_id);
            }
            if($supplier_id){
                $goods = $goods->whereSupplierId($supplier_id);
            }

            if ($num_sold_start > 0 && $num_sold_end > $num_sold_start) {
                $goods = $goods->whereBetween('num_sold', [$num_sold_start, $num_sold_end]);
            }
            //获取数据
            $goodsList = $goods->paginate($this->page);
            //字段拼接
            foreach ($goodsList as $goods) {
                $goodsSpec = $this->getPrice($goods->id);
                $goods->price_buying = $goodsSpec['price_buying'];
                $goods->price = $goodsSpec['price'];
                $goods->bannerFirst = GoodsImage::whereGoodsId($goods->id)->first();
                $goods->pavilion = ConfPavilion::whereId($goods->pavilion)->pluck('name');
                $goods->guide_rate = floatval($goodsSpec['guide_rate']).'%';
                $goods->travel_agency_rate = floatval($goodsSpec['travel_agency_rate']).'%';
            }

            //获取分类
            $categories = ConfCategory::all();
            //TODO::获取厨窗
            //$chuchuang = Chuchuang::all();
            return view('boss.goods.index')->with(['state' => $state,'confPavilions'=>$confPavilions,'pavilion_id'=>$pavilion_id,'goodsList' => $goodsList, 'categories' => $categories,'suppliers'=>$suppliers,'title'=>$title,'category_id'=>$category_id,'supplier_id'=>$supplier_id,'num_sold_start'=>$num_sold_start,'num_sold_end'=>$num_sold_end]);
        }
    }

    /**
     *审核列表页
     */
    public function check($state = 1)
    {
        //声明允许接收参数 0未审核 3驳回
        $states = [0, 1, 3];
        switch ($state){
            case 1:
                $status = '已通过';
                break;
            case 3:
                $status = '已驳回';
                break;
            case 0:
                $status = '待审核';
                break;
        }
        if (in_array($state, $states)) {
            if ($state != 1) {
                $goods = GoodsBase::whereState($state);
            } else {
                $goods = GoodsBase::whereIn('state', [1, 2]);
            }
            //搜索
            $keywords = Input::get('keywords');
            $pavilionValue = Input::get('pavilion');
            $categoryValue = Input::get('category');
            if (!is_null($keywords)) {
                $goods = $goods->where('title', 'like', '%' . $keywords . '%');
            }
            if($pavilionValue){
                $goods = $goods->wherePavilion($pavilionValue);
            }
            if($categoryValue){
                $goods = $goods->whereCategoryId($categoryValue);
            }
                $goods = $goods->orderBy('id','desc')->paginate($this->page);

            //获取价格
                foreach ($goods as $val){
                    $goodsSpec = $this->getPrice($val->id);
                    $val->price = $goodsSpec['price'];
                    $val->price_buying = $goodsSpec['price_buying'];
                    $val->pavilion = ConfPavilion::getName($val->pavilion);
                    $val->category = ConfCategory::getName($val->category_id);
                    $val->state = $status;
                    $val->bannerFirst = GoodsImage::whereGoodsId($val->id)->first();
                }
                //获取馆的种类
                    $pavilions = ConfPavilion::whereState(ConfPavilion::state_online)->get();
                    $categorys = ConfCategory::all();
            return view('boss.goods.check')->with(['state' => $state, 'goods_list' => $goods,'pavilions'=>$pavilions,'categorys'=>$categorys,'pavilionValue'=>$pavilionValue,'categoryValue'=>$categoryValue,'keywords'=>$keywords]);
        } else {
            return view('errors.404');
        }

    }

    /**
     *ajax快捷提交动作
     */
    public function action($action, $id ,Request $request)
    {
        switch ($action) {
            //取消厨窗
            case 'location_cancel':
                $return = GoodsBase::whereId($id)->update(['location' => 0]);
                break;
            //商品上架 审核通过
            case 'goods_up':
                $return = GoodsBase::whereId($id)->update(['state' => 1]);
                break;
            //商品下架
            case 'goods_down':
                GoodsGift::whereGiftId($id)->delete();
                UserCart::whereGoodsId($id)->delete();
                $return = GoodsBase::whereId($id)->update(['state' => 2]);
                $goods = GoodsBase::whereId($id)->first();
                $goodsInfo = "商品ID.$id";
                $goodsOptLog = new GoodsOptLog();
                $text = "【易游购】你好，你的".$goodsInfo."已被平台下架，你可以重新修改商品信息后再次上架，以免给你带来不必要的损失~
";
                $content = ['source'=>'boss','content'=>'商品强制下架','sms'=>$text];
                $content['content'] = urlencode($content['content']);
                $content['sms'] = urlencode($content['sms']);
                $content = urldecode(json_encode($content));
                $goodsOptLog->type = 2;
                $goodsOptLog->uid = Auth::user()->id;
                $goodsOptLog->gid = $goods->id;
                $goodsOptLog->content = $content;
                $goodsOptLog->save();

                //取消下架短信
               /* $supplier = SupplierBase::whereId($goods->supplier_id)->first();
                $mobile = $supplier->mobile;
                $type = self::goodsDown;
                $ip = ip2long($request->getClientIp());
                parent::platformSendSms($mobile, $ip, $type, $text);*/
                break;

            case 'refute':
                //审核驳回发送短信。获取驳回原因
                $refute = Input::get('refute_reason');
                if(empty($refute)){
                    return response()->json(['ret' => '请输入商品审核驳回原因']);
                }
                $goodsUser = GoodsBase::whereId($id)->first();
                $user = SupplierBase::whereId($goodsUser->supplier_id)->first();
                $return = GoodsBase::whereId($id)->update(['state' => GoodsBase::state_return]);
                //发送短信.....
                $tpl_value = "【易游购】抱歉，你上传的商品未能通过审核，原因：".$refute."。";
                Parent::platformSendSms($user->mobile,ip2long($request->getClientIp()),1,$tpl_value);

                break;
        }
        if ($return) {
            return response()->json(['msg' => '操作成功','id'=>$id]);
        }
    }

    /* GET设为厨窗 */
    public function location_fix($id)
    {
        //获取商品信息
        $goods = GoodsBase::whereId($id)->first();
        $pavilions = ConfPavilion::whereState(ConfPavilion::state_online)->get();
        //TODO::获取厨窗
        return view('boss.goods.location_fix')->with(['pavilions'=>$pavilions,'goods'=>$goods]);
    }

    /* POST设为厨窗 */
    function location_edit(){
        //设为厨窗
        //$return = GoodsBase::whereId($id)->update(['location' => 1]);
        $data = Input::all();
        $data['location'] = 1;
        if(empty($data['location_order'])){
            return response()->json(['ret'=>'no','msg' => '请填写排序']);
        }
        $GoodBase = new GoodsBase();
        $GoodBase->whereId($data['id'])->update($data);

        return response()->json(['ret'=>'yes']);
    }

    /* 编辑商品 */
    public function edit($id)
    {
        $goodsBase = GoodsBase::whereId($id)->first();
        if ($goodsBase->state != 0 && $goodsBase->state != 2){
            return $this->show($id);
        }

        $supplier_id = $goodsBase->supplier_id;
        $supplierExpress = SupplierExpress::whereSupplier_id($supplier_id)->first();
        $since = SupplierBase::whereId($supplier_id)->pluck('is_pick_up');
        $storeBase = SupplierBase::whereId($goodsBase->supplier_id)->first();
        if($goodsBase){
            $goods = GoodsBase::getGoods($goodsBase);

            

            foreach ($goods->gift as $gift){
                $gift->data = GoodsBase::whereId($gift->gift_id)->first();
                $gift->spec = GoodsSpec::whereId($gift->spec_id)->first();
            }
            $goods->ext->description = $this->getFetchDescription($goods->ext->description);
            return view('boss.goods.edit')->with(['goods' => $goods,'store'=>$storeBase,'supplierExpress'=>$supplierExpress,'since'=>$since]);
        }
    }

    /* 更新商品 */
    public function update($id,Request $request)
    {
        $data = Input::all();
        //dd($data);
        $seletc = Input::get('image_select');
        $accept = isset($data['goods_accept'])?$data['goods_accept']:0;
        $goodsInfo = GoodsBase::whereId($id)->first();
        $goods_category = isset($data['goods_category'])?$data['goods_category']:'';
        $user = $request->user();
        if ($goods_category){
            GoodsCategory::whereGoodsId($id)->delete();
            foreach ($goods_category as $category){
                GoodsCategory::create(['goods_id'=>$id,'category_id'=>$category]);
            }
        }
        //保存商品属性GoodsExt
        $goods_ext = [
            'important_tips'    =>  $data['important_tips'],
            'send_out_address'  =>  $data['send_out_address'],
            'send_out_desc'     =>  $data['send_out_desc'],
            'product_area'      =>  $data['product_area'],
            'shelf_life'        =>  $data['shelf_life'],
            'pack'              =>  $data['pack'],
            'store'             =>  $data['store'],
            'express_desc'      =>  $data['express_desc'],
            'sold_desc'         =>  $data['sold_desc'],
            'level'             =>  $data['level'],
            'product_license'   =>  $data['product_license'],
            'company'           =>  $data['company'],
            'dealer'            =>  $data['dealer'],
            'food_addiitive'    =>  $data['food_addiitive'],
            'food_burden'       =>  $data['food_burden'],
            'address'           =>  $data['address'],
            'description'       =>  $this->getPushDescription(Input::get('editorValue','')),
            'remark'            =>  $data['remark']
        ];
        //dd($goods_ext);

        //更新前商品属性
        $goodsExtBefore = GoodsExt::whereGoodsId($id)->first()->toArray();
        if(!is_null(array_diff($goods_ext,$goodsExtBefore))){

            $goodsOptLog = new GoodsOptLog();
            $goodsOptLog->type = GoodsOptLog::spec_change_before;
            $goodsOptLog->uid = $user->id;
            $goodsOptLog->gid = $id;
            $logData['source'] = 'boss';
            $logData['user'] = urlencode($user->name);
            $logData['user_id'] = $user->id;
            $logData['state'] = urlencode('更新前商品属性');
            $logData['important_tips'] = urlencode($goodsExtBefore['important_tips']);
            $logData['send_out_address'] = urlencode($goodsExtBefore['send_out_address']);
            $logData['send_out_desc'] = urlencode($goodsExtBefore['send_out_desc']);
            $logData['product_area'] = urlencode($goodsExtBefore['product_area']);
            $logData['shelf_life'] = urlencode($goodsExtBefore['shelf_life']);
            $logData['pack'] = urlencode($goodsExtBefore['pack']);
            $logData['store'] = urlencode($goodsExtBefore['store']);
            $logData['express_desc'] = urlencode($goodsExtBefore['express_desc']);
            $logData['sold_desc'] = urlencode($goodsExtBefore['sold_desc']);
            $logData['level'] = urlencode($goodsExtBefore['level']);
            $logData['product_license'] = urlencode($goodsExtBefore['product_license']);
            $logData['company'] = urlencode($goodsExtBefore['company']);
            $logData['dealer'] = urlencode($goodsExtBefore['dealer']);
            $logData['food_addiitive'] = urlencode($goodsExtBefore['food_addiitive']);
            $logData['food_burden'] = urlencode($goodsExtBefore['food_burden']);
            $logData['address'] = urlencode($goodsExtBefore['address']);
            $logData['description'] = urlencode($goodsExtBefore['description']);
            $logData['remark'] = urlencode($goodsExtBefore['remark']);

            $goodsOptLog->content = urldecode(json_encode($logData));
            $goodsOptLog->save();

            //更新商品属性
            GoodsExt::whereGoodsId($id)->update($goods_ext);
        }


        //更新后商品属性
        $goodsExtAfter = GoodsExt::whereGoodsId($id)->first()->toArray();
        if(!empty(array_diff($goodsExtAfter,$goodsExtBefore))){
            $goodsOptLog = new GoodsOptLog();
            $goodsOptLog->type = GoodsOptLog::spec_change_after;
            $goodsOptLog->uid = $user->id;
            $goodsOptLog->gid = $id;
            $logData['source'] = 'boss';
            $logData['user'] = urlencode($user->name);
            $logData['user_id'] = $user->id;
            $logData['state'] = urlencode('更新后商品属性');
            $logData['important_tips'] = urlencode($goodsExtAfter['important_tips']);
            $logData['send_out_address'] = urlencode($goodsExtAfter['send_out_address']);
            $logData['send_out_desc'] = urlencode($goodsExtAfter['send_out_desc']);
            $logData['product_area'] = urlencode($goodsExtAfter['product_area']);
            $logData['shelf_life'] = urlencode($goodsExtAfter['shelf_life']);
            $logData['pack'] = urlencode($goodsExtAfter['pack']);
            $logData['store'] = urlencode($goodsExtAfter['store']);
            $logData['express_desc'] = urlencode($goodsExtAfter['express_desc']);
            $logData['sold_desc'] = urlencode($goodsExtAfter['sold_desc']);
            $logData['level'] = urlencode($goodsExtAfter['level']);
            $logData['product_license'] = urlencode($goodsExtAfter['product_license']);
            $logData['company'] = urlencode($goodsExtAfter['company']);
            $logData['dealer'] = urlencode($goodsExtAfter['dealer']);
            $logData['food_addiitive'] = urlencode($goodsExtAfter['food_addiitive']);
            $logData['food_burden'] = urlencode($goodsExtAfter['food_burden']);
            $logData['address'] = urlencode($goodsExtAfter['address']);
            $logData['description'] = urlencode($goodsExtAfter['description']);
            $logData['remark'] = urlencode($goodsExtAfter['remark']);

            $goodsOptLog->content = urldecode(json_encode($logData));
            $goodsOptLog->save();
        }
        //保存商品图片GoodsImages 只有加减操作

        if($data['image_add']){
            //添加轮播图
            $existImages = GoodsImage::whereGoodsId($id)->get();
            $images=[];
            foreach($existImages as $v){
                $images[] = $v['name'];
            }
            $array = array_diff($data['image_add'],$images);
            foreach($array as $v){
                $goods_images = [
                    'goods_id'  =>  $id,
                    'name'      =>  $v
                ];
                GoodsImage::create($goods_images);
            }

        }
        if(isset($seletc)){
            GoodsImage::whereGoodsId($id)->delete();
            $goodsBase = GoodsBase::whereId($id)->first();
            $goodsBase ->first_image = $data['image_add'][0];
            $goodsBase->save();
            foreach($data['image_add'] as $v){
                $goods_images = [
                    'goods_id'  =>  $id,
                    'name'      =>  $v
                ];
                GoodsImage::create($goods_images);
            }
        }
        if($data['image_del']){
            //删除轮播图
            foreach ($data['image_del'] as $image_val){
                GoodsImage::whereId($image_val)->delete();
            }
            $goodsImgs = GoodsImage::whereGoodsId($id)->lists('name')->toArray();
            GoodsBase::whereId($id)->update(['first_image'=>$goodsImgs['0']]);
        }

        /**判断spec_del删除对应spec_id 判断spec_name添加到spec表 判断spec_id name修改 **/
        if(!empty($data['spec_del'])){
            //删除规格
            foreach ($data['spec_del'] as $spec_id){
                GoodsSpec::whereId($spec_id)->delete();
            }
        }
        foreach ($data['spec_id'] as $spec_key=>$spec_id){
            //修改规格
            $goods_spec = [
                'name'          =>  $data['name'][$spec_key],
                'pack_num'      =>  $data['pack_num'][$spec_key],
                'num'           =>  $data['num'][$spec_key],
                'num_limit'     =>  $data['num_limit'][$spec_key],
                //'num_sold'      =>  $data['num_sold'][$spec_key],//已售数量
                'weight'        =>  $data['weight'][$spec_key],
                'weight_net'    =>  $data['weight_net'][$spec_key],
                'long'          =>  $data['long'][$spec_key],
                'wide'          =>  $data['wide'][$spec_key],
                'height'        =>  $data['height'][$spec_key],
                'price'         =>  $data['price'][$spec_key],
                'price_buying'  =>  $data['price_buying'][$spec_key],
                'price_market'  =>  $data['price_market'][$spec_key],
                'platform_fee'  =>  $data['platform_fee'][$spec_key],//平台服务费
                'guide_rate'    =>  $data['guide_rate'][$spec_key],//导游分成
                'travel_agency_rate'  =>  $data['travel_agency_rate'][$spec_key],//旅行社分成
               /* 'express_fee_mode'    =>  $data['express_fee_mode'][$spec_key],*/
               /* 'is_pick_up'    =>  $data['is_pick_up'][$spec_key],*/
            ];
            if ($goods_spec['platform_fee'] < 0){
                return  $this->getReturnResult('no','平台服务费不能小于0');
            }

            $GoodsBase = GoodsBase::whereId($id)->first();
            $GoodsSpecNum = GoodsSpec::whereId($spec_id)->pluck('num');
            if($GoodsSpecNum != $data['num'][$spec_key]){
                $goodsTotalNum = $GoodsBase->num - $GoodsSpecNum + $data['num'][$spec_key];
                GoodsBase::whereId($id)->update(['num'=>$goodsTotalNum]);
            }

            //保存改价日志
            $goodsSpec = GoodsSpec::whereId($spec_id)->pluck('price_buying');
            if($goodsSpec != $data['price_buying'][$spec_key]){
                $goodsOptLog = new GoodsOptLog();
                $goodsOptLog->type = GoodsOptLog::type_change_priceBuying;
                $goodsOptLog->uid = $user->id;
                $goodsOptLog->gid = $id;
                $logChangePriceData['source'] = 'boss';
                $logChangePriceData['user'] = urlencode($user->name);
                $logChangePriceData['user_id'] = $user->id;
                $logChangePriceData['action'] = urlencode('修改供应单价');
                $logChangePriceData['goods_title'] = urlencode($goodsInfo->title);
                $logChangePriceData['spec_id'] = $spec_id;
                $logChangePriceData['before'] = $goodsSpec;
                $logChangePriceData['after'] = number_format($data['price_buying'][$spec_key],2);
                $goodsOptLog->content = urldecode(json_encode($logChangePriceData));
                $goodsOptLog->save();
            }

            //更新前商品规格
            $goodsSpecInfo = GoodsSpec::whereId($spec_id)->select('name','pack_num','num','num_limit','weight','weight_net','long','wide','height','price','price_buying','price_market','platform_fee','guide_rate','travel_agency_rate')->first()->toArray();


                $goodsChangeSpecLog = new GoodsOptLog();
                $goodsChangeSpecLog->type = GoodsOptLog::spec_change_before;
                $goodsChangeSpecLog->uid = $user->id;
                $goodsChangeSpecLog->gid = $id;
                $specChangeLog['source'] = 'boss';
                $specChangeLog['user'] = urlencode($user->name);
                $specChangeLog['user_id'] = urlencode($user->id);
                $specChangeLog['spec_id'] = $spec_id;
                $specChangeLog['state'] = urlencode('更新前商品规格');
                $specChangeLog['name'] = urlencode($goodsSpecInfo['name']);
                $specChangeLog['pack_num'] = $goodsSpecInfo['pack_num'];
                $specChangeLog['num'] = $goodsSpecInfo['num'];
                $specChangeLog['num_limit'] = $goodsSpecInfo['num_limit'];
                /*$specChangeLog['num_sold'] = $goodsSpecInfo->num_sold;*/
               /* $specChangeLog['num_water'] = $goodsSpecInfo->num_water;*/
                $specChangeLog['weight'] = $goodsSpecInfo['weight'];
                $specChangeLog['weight_net'] = $goodsSpecInfo['weight_net'];
                $specChangeLog['long'] = $goodsSpecInfo['long'];
                $specChangeLog['wide'] = $goodsSpecInfo['wide'];
                $specChangeLog['height'] = $goodsSpecInfo['height'];
                $specChangeLog['price'] = $goodsSpecInfo['price'];
                $specChangeLog['price_buying'] = $goodsSpecInfo['price_buying'];
                $specChangeLog['price_market'] = $goodsSpecInfo['price_market'];
                $specChangeLog['platform_fee'] = $goodsSpecInfo['platform_fee'];
                $specChangeLog['guide_rate'] = $goodsSpecInfo['guide_rate'];
                $specChangeLog['travel_agency_rate'] = $goodsSpecInfo['travel_agency_rate'];
                $goodsChangeSpecLog->content = urldecode(json_encode($specChangeLog));
                $goodsChangeSpecLog->save();
                //更新商品规格
                GoodsSpec::whereId($spec_id)->update($goods_spec);
            




            //更新后商品规格
            $goodsSpecInfoChange = GoodsSpec::whereId($spec_id)->select('name','pack_num','num','num_limit','weight','weight_net','long','wide','height','price','price_buying','price_market','platform_fee','guide_rate','travel_agency_rate')->first()->toArray();
            if(!empty(array_diff($goodsSpecInfoChange,$goodsSpecInfo))){
                $goodsChangeSpecLog = new GoodsOptLog();
                $goodsChangeSpecLog->type = GoodsOptLog::spec_change_after;
                $goodsChangeSpecLog->uid = $user->id;
                $goodsChangeSpecLog->gid = $id;
                $specChangeLog['source'] = 'boss';
                $specChangeLog['user'] = urlencode($user->name);
                $specChangeLog['user_id'] = urlencode($user->id);
                $specChangeLog['spec_id'] = $spec_id;
                $specChangeLog['state'] = urlencode('更新后商品规格');
                $specChangeLog['name'] = urlencode($goodsSpecInfoChange['name']);
                $specChangeLog['pack_num'] = $goodsSpecInfoChange['pack_num'];
                $specChangeLog['num'] = $goodsSpecInfoChange['num'];
                $specChangeLog['num_limit'] = $goodsSpecInfoChange['num_limit'];
               /* $specChangeLog['num_sold'] = $goodsSpecInfoChange->num_sold;
                $specChangeLog['num_water'] = $goodsSpecInfoChange->num_water;*/
                $specChangeLog['weight'] = $goodsSpecInfoChange['weight'];
                $specChangeLog['weight_net'] = $goodsSpecInfoChange['weight_net'];
                $specChangeLog['long'] = $goodsSpecInfoChange['long'];
                $specChangeLog['wide'] = $goodsSpecInfoChange['wide'];
                $specChangeLog['height'] = $goodsSpecInfoChange['height'];
                $specChangeLog['price'] = $goodsSpecInfoChange['price'];
                $specChangeLog['price_buying'] = $goodsSpecInfoChange['price_buying'];
                $specChangeLog['price_market'] = $goodsSpecInfoChange['price_market'];
                $specChangeLog['platform_fee'] = $goodsSpecInfoChange['platform_fee'];
                $specChangeLog['guide_rate'] = $goodsSpecInfoChange['guide_rate'];
                $specChangeLog['travel_agency_rate'] = $goodsSpecInfoChange['travel_agency_rate'];
                $goodsChangeSpecLog->content = urldecode(json_encode($specChangeLog));
                $goodsChangeSpecLog->save();
            }
        }
        if(isset($data['spec_name'])){
            //增加规格
            foreach ($data['spec_name'] as $spec_key=>$spec_name){
                $goods_spec = [
                    'goods_id'      =>  $id,
                    'name'          =>  $spec_name,
                    'pack_num'      =>  $data['spec_pack_num'][$spec_key],
                    'num'           =>  $data['spec_num'][$spec_key],
                    'num_limit'     =>  $data['num_limit'][$spec_key],
                    //'num_sold'      =>  $data['spec_num_sold'][$spec_key],//已售数量
                    'weight'        =>  $data['spec_weight'][$spec_key],
                    'weight_net'    =>  $data['spec_weight_net'][$spec_key],
                    'long'          =>  $data['spec_long'][$spec_key],
                    'wide'          =>  $data['spec_wide'][$spec_key],
                    'height'        =>  $data['spec_height'][$spec_key],
                    'price'         =>  $data['spec_price'][$spec_key],
                    'price_buying'  =>  $data['spec_price_buying'][$spec_key],
                    'price_market'  =>  $data['spec_price_market'][$spec_key],
                    'platform_fee'  =>  $data['spec_platform_fee'][$spec_key],//平台服务费
                    'guide_rate'    =>  $data['spec_guide_rate'][$spec_key],//导游分成
                    'travel_agency_rate'  =>  $data['spec_travel_agency_rate'][$spec_key],//旅行社分成
                   /* 'express_fee_mode'    =>  $data['spec_express_fee_mode'][$spec_key],*/
                   /* 'is_pick_up'    =>  $data['spec_is_pick_up'][$spec_key],*/
                ];
                if ($goods_spec['platform_fee'] < 0){
                    return  $this->getReturnResult('no','平台服务费不能小于0');
                }
                GoodsSpec::create($goods_spec);
            }
        }
        //保存赠品GoodsGift 只有加减操作
        if($data['gift_add']){
            //添加赠品
            foreach ($data['gift_add'] as $key => $gift_val){
                $goods_gift = [
                    'goods_id'  =>  $id,
                    'gift_id'   =>  $gift_val,
                    //'spec_id'   =>  $data['spec_add'][$key]//赠品规格
                ];
                GoodsGift::create($goods_gift);
            }
        }
        if($data['gift_del']){
            //删除赠品
            foreach ($data['gift_del'] as $gift_val){
                GoodsGift::whereGiftId($gift_val)->delete();
            }
        }
       /* //写入操作日志goods_opt_log
        $content['user'] = urlencode($user->name);
        $content['content'] = '编辑ID:'.$id.'商品';
        $content['content'] = urlencode($content['content']);
        $content = urldecode(json_encode($content));
        $goods_opt_log = [
            'type'      =>  '3',
            'uid'       =>  $user->id,
            'gid'       =>  $id,
            'content'   =>  $content
        ];
        GoodsOptLog::create($goods_opt_log);*/
        //保存商品GoodsBase
        $goods_base = [
            'title'         =>  $data['title'],
            'category_id'   =>  $data['category_id'],
            'cover'         =>  $data['cover'],
            'pavilion'      =>  $data['pavilion']
        ];
        if ($accept){
            $goods_base['state'] = GoodsBase::state_online;
            $data = $this->getReturnResult('yes','商品审核成功');
        }else{
            $data = $this->getReturnResult('yes','商品修改成功');
        }
        GoodsBase::find($id)->update($goods_base);
        return $data;
    }

    /* 上传文件 */
    function upload()
    {
        return response()->json(['img' => $this->uploadToQiniu()]);
    }
    /* GET添加赠品 */
    function gift(){
        //获取商品
        $goods = GoodsBase::all();
        return view('boss.goods.gift')->with(['goods'=>$goods]);
    }
    /* GET获取规格 */
    function gift_guide($id){
        $guide = GoodsSpec::whereGoodsId($id)->get();
        return response()->json(['guide'=>$guide]);
    }
    /* POST添加赠品 */
    function gift_store(){
        //接收赠品id及规格
        $gift_id = Input::get('gift_id');
        $guide_id = Input::get('guide');
        if(!$gift_id || !$guide_id){
            return response()->json(['ret'=>'no','msg'=>'请选择商品和规格']);
        }
        //获取赠品信息
        $goods = GoodsBase::whereId($gift_id)->first();
        $goods->spec = GoodsSpec::whereId($guide_id)->first();
        return response()->json(['ret'=>'yes','goods'=>$goods,'msg'=>'赠品添加成功']);
    }
    /* 商品详情页 */
    function show($id){
        $goodsBase = GoodsBase::whereId($id)->first();
        $storeBase = SupplierBase::whereId($goodsBase->supplier_id)->first();
        if($goodsBase){
            $goods = GoodsBase::getGoods($goodsBase);
            $supplier_id = $goodsBase->supplier_id;
            $supplierExpress = SupplierExpress::whereSupplier_id($supplier_id)->first();
            $since = SupplierBase::whereId($supplier_id)->pluck('is_pick_up');
            foreach ($goods->gift as $gift){
                $gift->data = GoodsBase::whereId($gift->gift_id)->first();
                $gift->spec = GoodsSpec::whereId($gift->spec_id)->first();
            }
            $goods->ext->description = $this->getFetchDescription($goods->ext->description);
            return view('boss.goods.show')->with(['goods' => $goods,'store'=>$storeBase,'supplierExpress'=>$supplierExpress,'since'=>$since]);
        }
    }

    //更改橱窗位置
    public function changeLocation(request $request){
        $location = $request->input('location');
        $goodsId = $request->input('goodsId');
        $goodsBase = GoodsBase::find($goodsId);
        $goodsBase->location_order = $location;
        $goodsBase->save();
    }

    //导出商品信息
    public function exportGoods(Request $request,$state=1){
        $data = $request->all();
        $goods = self::getAllGoods($data,$state);
        $field = ['商品名称','商品品类','所属分馆','规格','产地','发货地','供货价','零售价','利润','导游分成','旅行社分成','平台分成','售后说明','发货说明','快递说明','店铺名'];
        $items[] = $field;
        $i =1;
        foreach($goods as $value){
            $value->category_id = ConfCategory::whereId($value->category_id)->pluck('name');
            $value->pavilion = ConfPavilion::whereId($value->pavilion)->pluck('name');
           foreach($value->goodsSpecs as $v){
               $items[$i] = [$value->title,$value->category_id,$value->pavilion,$v['name'],$value->product_area,$value->send_out_address,$v['price_buying'],$v['price'],$v['price']-$v['price_buying'],$v['guide_rate'],$v['travel_agency_rate'],$v['platform_fee'],$value->sold_desc,$value->send_out_desc,$value->express_desc,$value->goodsStoreName];
               $i++;
           }
        }
        Excel::create(date('YmdHis'),function($excel) use ($items){
            $excel->sheet('goods', function($sheet) use ($items){
                $sheet->setWidth(array(
                    'A'=>60,
                    'B'=>20,
                    'C'=>20,
                    'D'=>20,
                    'E'=>20,
                    'F'=>20,
                    'G'=>20,
                    'H'=>20,
                    'I'=>20,
                    'J'=>20,
                    'K'=>20,
                    'L'=>20,
                    'M'=>20,
                    'N'=>20,
                    'O'=>20,
                    'P'=>20
                ));
                $sheet->rows($items);

            });
        })->export('xlsx');

    }

    //查询所有商品
    static private function getAllGoods($data,$state=1){
        $goods = new GoodsBase();
        if($state == 1){
            $goods = $goods->whereState(GoodsBase::state_online);
        }
        if($state == 'location'){
            $goods = $goods->whereState(self::location)->whereState(GoodsBase::state_online);
        }
        if($state == 2){
            $goods = $goods->whereState(GoodsBase::state_down);
        }
        if(!empty($data['pavilion_id'])){
            $goods = $goods->wherePavilion($data['pavilion_id']);
        }
        if(!empty($data['category_id'])){
            $goods = $goods->whereCategoryId($data['category_id']);
        }
        if(!empty($data['location'])){
            $goods = $goods->whereLocation($data['location']);
        }
        if(!empty($data['supplier_id'])){
            $goods = $goods->whereSupplierId($data['supplier_id']);
        }
        if(!empty($data['num_sold_start']) && $data['num_sold_end'] > $data['num_sold_start']){
            $goods = $goods->whereBetween('num_sold',[$data['num_sold_start'],$data['num_sold_end']]);
        }
        if(!empty($data['title'])){
            $goods = $goods->where('title','like','%'.$data['title'].'%');
        }
        $goods = $goods->orderBy('id','desc')->get();
        foreach($goods as $info){
            $goodsStoreName = SupplierBase::whereId($info->supplier_id)->pluck('store_name');
            $goodsSpecs = GoodsSpec::whereGoodsId($info->id)->get()->toArray();
            $goodsExt = GoodsExt::whereGoodsId($info->id)->first();
            $info->product_area = $goodsExt->product_area;
            $info->send_out_address =$goodsExt->send_out_address;
            $info->sold_desc =$goodsExt->sold_desc;
            $info->send_out_desc =$goodsExt->send_out_desc;
            $info->express_desc =$goodsExt->express_desc;
            $info->goodsSpecs = $goodsSpecs;
            $info->goodsStoreName = $goodsStoreName;
        }
        return $goods;

    }

    public function goodsNunSoldEdit(Request $request)
    {
        $spec_id = $request->input('specId',0);
        $num_water = $request->input('num_water',0);
        $GoodsSpec = GoodsSpec::whereId($spec_id)->first();
        $spec_num_sold = $GoodsSpec->num_sold - $GoodsSpec->num_water + $num_water;
        $GoodsBase = GoodsBase::whereId($GoodsSpec->goods_id)->first();
        $goods_num_sold = $GoodsBase->num_sold - $GoodsSpec->num_water + $num_water;
        $goods_num_water = $GoodsBase->num_water - $GoodsSpec->num_water + $num_water;
        GoodsSpec::whereId($spec_id)->update(['num_sold'=>$spec_num_sold,'num_water'=>$num_water]);
        GoodsBase::whereId($GoodsBase->id)->update(['num_sold'=>$goods_num_sold,'num_water'=>$goods_num_water]);
        return response()->json(['ret'=>'yes','msg'=>BaseController::CREATESUCCESS]);

    }

}
