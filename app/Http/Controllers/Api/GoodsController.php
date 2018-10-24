<?php namespace App\Http\Controllers\Api;

use App\Models\ConfPavilion;
use App\Models\ConfTheme;
use App\Models\CouponBase;
use App\Models\CouponGood;
use App\Models\GoodsBase;
use App\Models\GoodsExt;
use App\Models\GoodsGift;
use App\Models\GoodsImage;
use App\Models\GoodsMaterialBase;
use App\Models\GoodsMaterialImage;
use App\Models\GoodsSpec;
use App\Models\GuideStoreGood;
use App\Models\SupplierBase;
use App\Models\SupplierExpress;
use App\Models\UserFavorite;
use Log;
use Lang;
use Illuminate\Http\Request;
use App\Http\Controllers\GenController;
use App\Http\Controllers\Wx\WxLocationController;
use App\Models\CommentGood;
use App\Models\CommentGoodsImage;
use App\Models\OrderGood;
use App\Models\UserBase;
use App\Models\GuideBase;
use App\Models\OrderBase;

class GoodsController extends GenController
{

    /**
     * @SWG\Get(path="/v1/goods/list",
     *   tags={"goods"},
     *   summary="获取商品列表",
     *   description="",
     *   operationId="goods",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="pavilion",
     *     in="query",
     *     description="地方錧id",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="pavilion")
     *   ),
     *   @SWG\Parameter(
     *     name="category",
     *     in="query",
     *     description="分类id",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="category")
     *   ),
     *   @SWG\Parameter(
     *     name="order_by",
     *     in="query",
     *     description="",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="order_by(1.返利高，11.返利低，2.销量高，12.销售低，3.人气高，13.人气低)")
     *   ),
     *   @SWG\Parameter(
     *     name="q",
     *     in="query",
     *     description="搜索关键词",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="q")
     *   ),
     *   @SWG\Parameter(
     *     name="page_num",
     *     in="query",
     *     description="页码",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="page_num")
     *   ),
     *  @SWG\Parameter(
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
    public function getGoodsList(Request $request)
    {

        $q = $request->input('q','');
        $pavilion = $request->input('pavilion',0);
        $category = $request->input('category',0);
        $order_by = $request->input('order_by',2);
        $page_num = intval($request->input('page_num', 0));
        $limit = intval($request->input('limit', 20));
        $offset = $page_num * $limit;

        //补充逻辑
        /*
        if($pavilion == 0){
            $ConfPavilion = ConfPavilion::whereName('乡亲直供馆')->first();
            $pavilion = $ConfPavilion['id'];
        }
        */

        $GoodsBase = GoodsBase::whereState(GoodsBase::state_online);

        if($q != ''){
            $GoodsBase = $GoodsBase->where('title','like','%'.$q.'%');
        }

        if($pavilion > 0){
            $GoodsBase = $GoodsBase->wherePavilion($pavilion);
        }

        if($category > 0){
            $GoodsBase = $GoodsBase->whereCategoryId($category);
        }

        if($order_by == 1){
            $GoodsBase = $GoodsBase->OrderBy('guide_amount','desc');
        }

        if($order_by == 11){
            $GoodsBase = $GoodsBase->OrderBy('guide_amount','asc');
        }

        if($order_by == 2){
            $GoodsBase = $GoodsBase->OrderBy('num_sold','desc');
        }

        if($order_by == 12){
            $GoodsBase = $GoodsBase->OrderBy('num_sold','asc');
        }

        if($order_by == 3){
            $GoodsBase = $GoodsBase->OrderBy('num_favorite','desc');
        }

        if($order_by == 13){
            $GoodsBase = $GoodsBase->OrderBy('num_favorite','asc');
        }

        $GoodsBase = $GoodsBase->offset($offset)->limit($limit)->get();

        $result = array();
        foreach($GoodsBase as $v){
            $tmp = array();
            $tmp['id'] = strval($v['id']);
            $tmp['title'] = $v['title'];
            $GoodsSpec = GoodsSpec::whereGoodsId($v['id'])->first();
            $spec = GoodsSpec::whereGoodsId($v['id'])->get();

            $num = 0 ;
            $num_sold = 0 ;
            foreach($spec as $vv){
                $num = $num + $vv['num'];
                $num_sold = $num_sold + $vv['num_sold'];
            }

            $tmp['price'] = strval($GoodsSpec['price']);
            $tmp['rebate'] = strval(number_format(($GoodsSpec['price'] * $GoodsSpec['guide_rate'] / 100),2));
            $tmp['num'] = strval($num);
            $tmp['num_sold'] = strval($num_sold);
            $tmp['cover'] = env('IMAGE_DISPLAY_DOMAIN').$v['first_image'].'?imageslim';
            $tmp['pavilion'] = strval($v['pavilion']);
            $tmp['category'] = strval($v['category_id']);
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
     * @SWG\Get(path="/v1/goods/{id}",
     *   tags={"goods"},
     *   summary="获取商品详情",
     *   description="",
     *   operationId="goods",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     description="商品id",
     *     required=true,
     *     type="integer",
     *     @SWG\Schema(ref="id")
     *   ),
     *   @SWG\Parameter(
     *     name="uid",
     *     in="query",
     *     description="导游的uid",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="pavilion")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function getGoodsById($id,Request $request)
    {
        $uid = $request->input('uid',0);
        $goodsBase = GoodsBase::whereId($id)->first();
        $goodsExt = GoodsExt::whereGoodsId($id)->first();
        $goodsImages = GoodsImage::whereGoodsId($id)->orderBy('id','asc')->get();
        $goodsSpec = GoodsSpec::whereGoodsId($id)->get();
        $goodsGift = GoodsGift::whereGoodsId($id)->get();
        $SupplierBase = SupplierBase::whereId($goodsBase['supplier_id'])->first();
        $SupplierExpress = SupplierExpress::whereSupplierId($goodsBase['supplier_id'])->first();

        $banner = array();
        foreach($goodsImages as $v){
            $banner[] = env('IMAGE_DISPLAY_DOMAIN').$v['name'].'?imageslim';
        }

        $spec = array();
        foreach($goodsSpec as $v){
            $tmp = array();
            $tmp['id'] = strval($v['id']);
            $tmp['goods_id'] = strval($v['goods_id']);
            $tmp['name'] = strval($v['name']);
            $tmp['num'] = strval($v['num']);
            $tmp['num_sold'] = strval($v['num_sold']);
            $tmp['num_limit'] = strval($v['num_limit']);
            $tmp['weight'] = strval(number_format($v['weight'],2)).'克';
            if($v['weight'] > 1000){
                $tmp['weight'] = strval(number_format($v['weight']/1000,2)).'千克';
            }
            $tmp['weight_net'] = strval(number_format($v['weight_net'],2)).'克';
            if($v['weight_net'] > 1000){
                $tmp['weight_net'] = strval(number_format($v['weight_net']/1000,2)).'千克';
            }
            $tmp['long'] = strval($v['long']);
            $tmp['wide'] = strval($v['wide']);
            $tmp['height'] = strval($v['height']);
            $tmp['price'] = strval($v['price']);
            $tmp['price_market'] = strval($v['price_market']);
            $tmp['guide_rate'] = strval($v['guide_rate']*$v['price']/100);
            $spec[] = $tmp;
        }

        $gift = array();
        foreach($goodsGift as $v){
            $tmp = array();
            $giftGoods = GoodsBase::whereId($v['gift_id'])->first();
            $tmp['goods_id'] = strval($v['gift_id']);
            $tmp['name'] = strval($giftGoods['title']);
            $gift[] = $tmp;
        }

        $store = array();
        $store['id'] = strval($goodsBase['supplier_id']);
        $store['name'] = $SupplierBase['store_name'];
        $store['avatar'] = $SupplierBase['avatar'] != '' ? env('IMAGE_DISPLAY_DOMAIN').$SupplierBase['avatar'] : '';
        $store['logo'] = $SupplierBase['store_logo'] != '' ? env('IMAGE_DISPLAY_DOMAIN').$SupplierBase['store_logo'] : '';
        $store['free_express_amount'] = isset($SupplierExpress['total_amount']) ? $SupplierExpress['total_amount'] : '0';
        $store['express_amount'] = isset($SupplierExpress['express_amount']) ? $SupplierExpress['express_amount'] : '0';
        $store['is_pick_up'] = strval($SupplierBase['is_pick_up']);

        $attribute = array();
        $attribute['important_tips'] = strval($goodsExt['important_tips']);
        $attribute['send_out_address'] = strval($goodsExt['send_out_address']);
        $attribute['send_out_desc'] = strval($goodsExt['send_out_desc']);
        $attribute['product_area'] = strval($goodsExt['product_area']);
        $attribute['shelf_life'] = strval($goodsExt['shelf_life']);
        $attribute['pack'] = strval($goodsExt['pack']);
        $attribute['store'] = strval($goodsExt['store']);
        $attribute['express_desc'] = strval($goodsExt['express_desc']);
        $attribute['sold_desc'] = strval($goodsExt['sold_desc']);
        $attribute['level'] = strval($goodsExt['level']);
        $attribute['product_license'] = strval($goodsExt['product_license']);
        $attribute['company'] = strval($goodsExt['company']);
        $attribute['dealer'] = strval($goodsExt['dealer']);
        $attribute['food_additive'] = strval($goodsExt['food_addiitive']);
        $attribute['food_burden'] = strval($goodsExt['food_burden']);
        $attribute['address'] = strval($goodsExt['address']);
        $attribute['description'] = 'http://'.env('API_DOMAIN').'/v1/goods/'.$id.'/detail';
        $attribute['remark'] = strval($goodsExt['remark']);


        $result['title'] = strval($goodsBase['title']);
        $result['banner'] = $banner;
        $result['spec'] = $spec;
        $result['attribute'] = $attribute;
        $result['gift'] = $gift;
        $result['store'] = $store;

        $result['is_selected'] = '0';
        $goods_id = GuideStoreGood::whereGuideId($uid)->lists('goods_id')->toArray();
        if(in_array($id,$goods_id)){
            $result['is_selected'] = '1';
        }

        $UserFavorite = UserFavorite::whereUid($uid)->whereGoodsId($id)->first();
        $result['favorite_id'] = '0';
        if(!is_null($UserFavorite)){
            $result['favorite_id'] = strval($UserFavorite['id']);
        }

        $result['is_coupon_goods'] = self::getIsCouponGoods($id);


        if($goodsBase['state'] != 1){
            $result['state'] = '2';
        }else{
            $result['state'] = '1';
        }
        
        //取出当前商品的所有评论
        $comments = CommentGood::whereGoodsId($id)->whereState(1)
        ->orWhere(function ($query) use ($uid, $id) {
            $query->where('uid',$uid)
            ->where('goods_id',$id);
        })->orderBy('created_at','desc')->paginate(2);
        //对数据进行加工
        foreach($comments as $val){
            //判断是不是导游
            $userBase = UserBase::whereId($val->uid)->first();
            //取昵称
            $nicknake = $userBase->nick_name;
            if(empty($nicknake)){
                $molile = $userBase->mobile;
                $left   = substr($molile,0,3);
                $right  = substr($molile,7);
                $mobile = $left.str_repeat('*', 4).$right;
                $val->nicknake = $mobile;
            }else{
                if(mb_strlen($nicknake)<2){
                    $val->nicknake = mb_substr($nicknake,0,1).str_repeat('*', 4);
                }else{
                    $val->nicknake = mb_substr($nicknake,0,1).str_repeat('*', 3).mb_substr($nicknake,-1);
                }
            }
            //取头像
            if($userBase->is_guide){
                $guideinfo = GuideBase::whereUid($val->uid)->first();
                $val->headimg = empty($guideinfo->avatar)?'':env('IMAGE_DISPLAY_DOMAIN').$guideinfo['avatar'];
            }else{
                $val->headimg = '';
            }
            $comment_img = CommentGoodsImage::whereCommentId($val->id)->select('image_name')->get()->toArray();
            $commentImg = array();
            if(!empty($comment_img)){
                foreach($comment_img as $img){
                    $commentImg[] = env('IMAGE_DISPLAY_DOMAIN').$img['image_name'];
                }
            }
            $val->comment_img = $commentImg;
        }
        $result['comments'] = $comments->toArray();
        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => $result);
        return response()->json($result);
    }

    
    /**
     * @SWG\Get(path="/v1/goods/{id}/comments",
     *   tags={"goods"},
     *   summary="获取当前商品的更多评论详情",
     *   description="",
     *   operationId="getGoodsComments",
     *   @SWG\Parameter(
     *     name="uid",
     *     in="query",
     *     description="用户id",
     *     required=true,
     *     type="integer",
     *     @SWG\Schema(ref="uid")
     *   ),
     *   @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     description="商品id",
     *     required=true,
     *     type="integer",
     *     @SWG\Schema(ref="id")
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
    public function getGoodsComments($id,Request $request){
        $goodsid  = $id;
        $uid      = intval($request->input('uid',0));
        $page_num = intval($request->input('page_num', 0));
        $limit    = intval($request->input('limit', 20));
        $offset   = $page_num * $limit;
        
        if(empty($uid)){
            return response()->json(['ret'=>self::RET_FAIL,'msg'=>'请传递用户的id','data' => '']);
        }
        $goodsInfo= OrderBase::whereId($goodsid)->first()->toArray(); 
        if(empty($goodsInfo)){
            return response()->json(['ret'=>self::RET_FAIL,'msg'=>'请传递正确的商品id','data' => '']);
        }
        
        //取出当前商品的所有评论
        $comments = CommentGood::whereGoodsId($goodsid)->whereState(1)
        ->orWhere(function ($query) use ($uid, $goodsid) {
          $query->where('uid',$uid)
          ->where('goods_id',$goodsid);
        })->orderBy('created_at','desc')->offset($offset)->limit($limit)->get();
        //对数据进行加工
        foreach($comments as $val){
            //判断是不是导游
            $userBase = UserBase::whereId($val->uid)->first();
            //取昵称
            $nicknake = $userBase->nick_name;
            if(empty($nicknake)){
                $molile = $userBase->mobile;
                $left   = substr($molile,0,3);
                $right  = substr($molile,7);
                $mobile = $left.str_repeat('*', 4).$right;
                $val->nicknake = $mobile;
            }else{
                if(mb_strlen($nicknake)<2){
                    $val->nicknake = mb_substr($nicknake,0,1).str_repeat('*', 4);
                }else{
                    $val->nicknake = mb_substr($nicknake,0,1).str_repeat('*', 3).mb_substr($nicknake,-1);
                }
            }
            //取头像
            if($userBase->is_guide){
                $guideinfo = GuideBase::whereUid($val->uid)->first();
                $val->headimg = empty($guideinfo->avatar)?'':env('IMAGE_DISPLAY_DOMAIN').$guideinfo['avatar'];
            }else{
                $val->headimg = '';
            }
            $comment_img = CommentGoodsImage::whereCommentId($val->id)->select('image_name')->get()->toArray();
            $commentImg = array();
            if(!empty($comment_img)){
                foreach($comment_img as $img){
                    $commentImg[] = env('IMAGE_DISPLAY_DOMAIN').$img['image_name'];
                }
            }
            $val->comment_img = $commentImg;
        }
        
        $comments = $comments->toArray();
        //dd($comments);
        $result   = array('ret' => self::RET_SUCCESS, 'msg' => '评论数据获取成功', 'data' =>$comments);
        return response()->json($result);
    }
    

    /**
     * @SWG\Get(path="/v1/goods/{id}/material",
     *   tags={"goods"},
     *   summary="获取商品素材",
     *   description="",
     *   operationId="goods",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     description="商品id",
     *     required=true,
     *     type="integer",
     *     @SWG\Schema(ref="id")
     *   ),
     *   @SWG\Parameter(
     *     name="taid",
     *     in="query",
     *     description="旅行社id(没有可以填0)",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="taid")
     *   ),
     *  @SWG\Parameter(
     *     name="gid",
     *     in="query",
     *     description="导游id",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="gid")
     *   ),
     *   @SWG\Parameter(
     *     name="page_num",
     *     in="query",
     *     description="页码",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="page_num")
     *   ),
     *  @SWG\Parameter(
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
    public function getGoodsMaterial($id,Request $request)
    {
        $taid = intval($request->input('taid', 0));
        $gid = intval($request->input('gid', 0));
        $page_num = intval($request->input('page_num', 0));
        $limit = intval($request->input('limit', 20));
        $offset = $page_num * $limit;

        $GoodsMaterialBase = GoodsMaterialBase::whereGoodsId($id)->orderBy('id','desc')->offset($offset)->limit($limit)->get();

        $result = array();
        foreach($GoodsMaterialBase as $v){
            $tmp = array();
            $tmp['content'] = $v['content'];
            $tmp['like_num'] = strval($v['like_num']);
            $tmp['forward_num'] = strval($v['forward_num']);
            $GoodsMaterialImage = GoodsMaterialImage::whereMaterialId($v['id'])->get();
            $images = array();
            foreach($GoodsMaterialImage as $vv){
                $images[] = env('IMAGE_DISPLAY_DOMAIN').$vv['image_name'].'?imageslim';
            }
            $tmp['images'] = $images;
            $tmp['url_short'] = self::xlUrlAPI(urlencode('http://'.env('H5_DOMAIN').'/goods/'.$id.'?gid='.$gid.'&taid='.$taid));
            //$tmp['url_short'] = WxLocationController::shortUrl(urlencode('http://'.env('H5_DOMAIN').'/goods/'.$id.'?gid='.$gid.'&taid='.$taid));
            $tmp['created_at'] = date('Y-m-d H:i:s',strtotime($v['created_at']));
            $result[] = $tmp;
        }

        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => $result);
        return response()->json($result);
    }


    /**
     * @SWG\Get(path="/v1/goods/theme_recommend",
     *   tags={"goods"},
     *   summary="获取专题和推荐",
     *   description="",
     *   operationId="goods",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="pavilion",
     *     in="query",
     *     description="地方錧id",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="pavilion")
     *   ),
     *   @SWG\Parameter(
     *     name="uid",
     *     in="query",
     *     description="uid",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="pavilion")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function getThemeRecommend(Request $request)
    {

        $pavilion = $request->input('pavilion',0);
        $uid = $request->input('uid',0);

        if($pavilion == 0){
            $ConfPavilion = ConfPavilion::whereName('乡亲直供馆')->first();
            $pavilion = $ConfPavilion['id'];
        }

        $result = array();
        $result['theme'] = array();
        $result['recommend'] = array();

        $ConfTheme = ConfTheme::whereState(ConfTheme::state_online)->wherePavilionId($pavilion);
        $ConfTheme = $ConfTheme->orderBy('display_order','desc')->get();

        foreach($ConfTheme as $v){
            $tmp = array();
            $tmp['name'] = $v['name'];
            $tmp['content'] = strlen($v['content']) > 1 ? $v['content'] : $v['name'];
            $tmp['url'] = $v['url'];
            $tmp['url_type'] = strval($v['url_type']);
            $tmp['cover'] = $v['cover'] != '' ? env('IMAGE_DISPLAY_DOMAIN').$v['cover'] : '';
            $result['theme'][] = $tmp;
        }

        $GoodsBase = GoodsBase::whereState(GoodsBase::state_online)
                        ->whereLocation(GoodsBase::location_index_recommend)
                        ->wherePavilion($pavilion);
        $GoodsBase = $GoodsBase->orderBy('location_order','desc')->get();

        foreach($GoodsBase as $v){
            $tmp = array();
            $tmp['id'] = strval($v['id']);
            $tmp['title'] = $v['title'];
            $GoodsSpec = GoodsSpec::whereGoodsId($v['id'])->first();
            $tmp['price'] = strval($GoodsSpec['price']);
            $tmp['price_market'] = strval($GoodsSpec['price_market']);

            $tmp['rebate'] = strval(number_format(($GoodsSpec['price'] * $GoodsSpec['guide_rate'] / 100),2));

            $tmp['num'] = strval($v['num']);
            $tmp['num_sold'] = strval($v['num_sold']);
            $tmp['cover'] = $v['cover'] != '' ? env('IMAGE_DISPLAY_DOMAIN').$v['cover'] : '';
            $tmp['pavilion'] = strval($v['pavilion']);
            $tmp['category'] = strval($v['category_id']);
            $tmp['is_selected'] = self::isSelectedGoods($uid,$v['id']);
            if($v['state'] != 1){
                $tmp['state'] = '2';
            }else{
                $tmp['state'] = '1';
            }
            $result['recommend'][] = $tmp;
        }

        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' =>$result );
        return response()->json($result);
    }

    /**
     * @SWG\Get(path="/v1/goods/store/{id}",
     *   tags={"goods"},
     *   summary="获取供应商的商品列表",
     *   description="",
     *   operationId="getStoreInfo",
     *   @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     description="供应商id",
     *     required=true,
     *     type="integer",
     *     @SWG\Schema(ref="pavilion")
     *   ),
     *   @SWG\Parameter(
     *     name="uid",
     *     in="query",
     *     description="导游的uid",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="pavilion")
     *   ),
     *  @SWG\Parameter(
     *     name="page_num",
     *     in="query",
     *     description="页码",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="page_num")
     *   ),
     *  @SWG\Parameter(
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
    public function getSupplierStoreGoods($id, Request $request){

        $uid = intval($request->input('uid', 0));
        $page_num = intval($request->input('page_num', 0));
        $limit = intval($request->input('limit', 20));
        $offset = $page_num * $limit;

        $GoodsBase = GoodsBase::whereSupplierId($id)->whereState(GoodsBase::state_online)->orderBy('id','desc')->offset($offset)->limit($limit)->get();
        $result = array();
        foreach($GoodsBase as $v){
            $tmp = array();
            $tmp['id'] = strval($v['id']);
            $tmp['goods_id'] = strval($v['id']);
            $tmp['title'] = isset($v['title']) ? $v['title'] : '';
            $GoodsSpec = GoodsSpec::whereGoodsId($v['id'])->first();
            $tmp['price'] = strval($GoodsSpec['price']);
            $tmp['price_market'] = strval($GoodsSpec['price_market']);
            $tmp['rebate'] = strval(number_format(($GoodsSpec['price'] * $GoodsSpec['guide_rate'] / 100),2));

            $tmp['num'] = strval($v['num']);
            $tmp['cover'] = $v['cover'] != '' ? env('IMAGE_DISPLAY_DOMAIN').$v['cover'] : '';
            $tmp['is_selected'] = self::isSelectedGoods($uid,$v['id']);
            if($v['state'] != 1){
                $tmp['state'] = '2';
            }else{
                $tmp['state'] = '1';
            }
            $result[] = $tmp;
        }
        return response()->json(array('ret' => self::RET_SUCCESS, 'msg' => '', 'data'=>$result));
    }


    /**
     * @SWG\Get(path="/v1/coupon/goods/",
     *   tags={"goods"},
     *   summary="获取优惠券商品列表",
     *   description="",
     *   operationId="getGoodsListByCoupon",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="coupon_id",
     *     in="query",
     *     description="优惠券id",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="category")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function getGoodsListByCoupon(Request $request)
    {

        $coupon_id= $request->input('coupon_id',0);
        $CouponBase = CouponBase::whereId($coupon_id)->first();
        $goods_id = CouponGood::whereSupplierId($CouponBase['supplier_id'])->lists('goods_id');
        $GoodsBase = GoodsBase::whereState(GoodsBase::state_online)->whereIn('id',$goods_id)->get();

        $result = array();
        foreach($GoodsBase as $v){
            $tmp = array();
            $tmp['id'] = strval($v['id']);
            $tmp['title'] = $v['title'];
            $GoodsSpec = GoodsSpec::whereGoodsId($v['id'])->first();
            $spec = GoodsSpec::whereGoodsId($v['id'])->get();

            $num = 0 ;
            $num_sold = 0 ;
            foreach($spec as $vv){
                $num = $num + $vv['num'];
                $num_sold = $num_sold + $vv['num_sold'];
            }

            $tmp['price'] = strval($GoodsSpec['price']);
            $tmp['price_market'] = strval($GoodsSpec['price_market']);
            $tmp['rebate'] = strval(number_format(($GoodsSpec['price'] * $GoodsSpec['guide_rate'] / 100),2));
            $tmp['num'] = strval($num);
            $tmp['num_sold'] = strval($num_sold);
            $tmp['cover'] = env('IMAGE_DISPLAY_DOMAIN').$v['first_image'].'?imageslim';
            $tmp['pavilion'] = strval($v['pavilion']);
            $tmp['category'] = strval($v['category_id']);
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



    public function viewGoodsDetail($id)
    {
        $base = GoodsBase::whereId($id)->first();
        $ext = GoodsExt::whereGoodsId($id)->first();
        $data = array();
        $data['title'] = $base['sale_title'];
        $data['description'] = self::getFetchDescription($ext['description']);

        return view('api.goods_detail', compact('data'));
    }


    private function isSelectedGoods($uid,$goods_id){
        $goodsIdArray = GuideStoreGood::whereGuideId($uid)->lists('goods_id')->toArray();
        $is_selected = '0';
        if(in_array($goods_id,$goodsIdArray)){
            $is_selected = '1';
        }
        return $is_selected;
    }


     static public function xlUrlAPI($url){
        /* 这是我申请的APPKEY，大家可以测试使用 */
        $key = '1562966081';
        $baseurl = 'http://api.t.sina.com.cn/short_url/shorten.json?source='.$key.'&url_long='.$url;
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL,$baseurl);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        $strRes=curl_exec($ch);
        curl_close($ch);
        $arrResponse=json_decode($strRes,true);
        if (isset($arrResponse->error) || !isset($arrResponse[0]['url_long']) || $arrResponse[0]['url_long'] == '')
            return $url;
        return $arrResponse[0]['url_short'];
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    




}

