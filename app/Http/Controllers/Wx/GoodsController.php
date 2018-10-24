<?php

namespace App\Http\Controllers\Wx;

use App\Models\ConfHotWord;
use App\Models\GuideBase;
use App\Models\SupplierExpress;
use App\Models\UserBase;
use Cookie;
use App\Http\Requests;
use App\Models\UserWx;
use App\Models\UserCart;
use App\Models\GoodsExt;
use App\Models\GoodsBase;
use App\Models\GoodsSpec;
use App\Models\GoodsGift;
use App\Models\GoodsImage;
use Illuminate\Http\Request;
use App\Models\ConfCategory;
use App\Models\SupplierBase;
use App\Models\UserFavorite;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use App\Models\CommentGood;
use App\Models\CommentGoodsImage;

class GoodsController extends WxController
{
    //商品详情页
    public function getGoodsDetail($goodsid,Request $request)
    {
        //微信端授权接口
        if(env('APP_ENV') == 'local'){
            $open_id = 'oY1sE1F30sAlbsa1vnSlDU8Jhh6A';
        }else{
            $open_id = self::setWxAuth($request,$_SERVER['REQUEST_URI']);
        }
        if($open_id != ''){
            Cookie::queue('openid',$open_id);
        }
        $userinfo  = UserWx::whereOpenId($open_id)->select(['uid'])->first();
        //dd($userinfo);
        $taid = $request->input('taid',0);
        $gid  = $request->input('gid',0);
        $ref  = $request->input('ref','');

        if($gid){
            $guideBase = GuideBase::whereId($gid)->first()->toArray();
            $guideBase['real_name'] = UserBase::whereId($guideBase['uid'])->pluck('nick_name');
        }else{
            $guideBase['id'] = $gid;
        }

        if($gid){
            $guideBase = GuideBase::whereId($gid)->first()->toArray();
            $guideBase['real_name'] = UserBase::whereId($guideBase['uid'])->pluck('nick_name');
        }else{
            $guideBase['id'] = $gid;
        }

        UserCart::whereOpenId($open_id)->update(['is_selected' => 0]);
        $goods = GoodsBase::whereId($goodsid)->first();
        $goods['cover_image'] = $goods['first_image'];
        $goodsExt = GoodsExt::whereGoodsId($goodsid)->first();

        if(!empty($goodsExt)){
            $goodsExt = $goodsExt->toArray();
            if($goodsExt['important_tips'] != ''){
                $goods['important_tips'] = $goodsExt['important_tips'];
            }
        }
        $goodsExt['description'] =$this->getFetchDescription($goodsExt['description']);
        //查询用户收藏表
        $userFavorites = UserFavorite::whereGoodsId($goodsid)->where('open_id',$open_id)->first();
        $goodsImgLists = GoodsImage::whereGoodsId($goodsid)->get()->toArray();
        $goodsSpecs = GoodsSpec::whereGoodsId($goodsid)->get()->toArray();
        $goodsGiftId = GoodsGift::whereGoodsId($goodsid)->lists('gift_id');

        $goodsGift = GoodsBase::whereIn('id', $goodsGiftId)->whereState(GoodsBase::state_online)->get();
       if(!empty($goodsGift)){
            $goodsGift = $goodsGift->toArray();
        }
        $supplier = SupplierBase::whereId($goods['supplier_id'])->first();
        if($supplier->is_pick_up == 1){
            $supplier->is_pick_up = "支持自提";
        }else{
            $supplier->is_pick_up = "";
        }
        $supplierExpress = SupplierExpress::whereSupplierId($goods['supplier_id'])->first();

        if($supplierExpress->total_amount == 0){
            $supplier->total_amount = "包邮";
        }else{
            $supplier->total_amount = "满".$supplierExpress->total_amount."包邮";
        }
        $count = $this->count($open_id);
        $weight = $goodsSpecs[0]['weight'];
        $weight_net = $goodsSpecs[0]['weight_net'];
        if($weight > 1000){
            $goodsSpecs[0]['weight'] = number_format($goodsSpecs[0]['weight']/1000,2)."千克";
        }else{
            $goodsSpecs[0]['weight'] = number_format($weight,2)."克";
        }
        if($weight_net > 1000){
            $goodsSpecs[0]['weight_net'] = number_format($goodsSpecs[0]['weight_net']/1000,2)."千克";
        }else{
            $goodsSpecs[0]['weight_net'] = number_format($weight_net,2)."克";
        }
        //bind
        self::bindWxGuideId($open_id,$gid,$ref);
        
        //取出当前商品的所有评论
        $comments = CommentGood::whereGoodsId($goodsid)->whereState(1)
        ->orWhere(function ($query) use ($userinfo, $goodsid) {
            $query->where('uid',$userinfo->uid)
                  ->where('goods_id',$goodsid);
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
                $val->headimg = empty($guideinfo->avatar)?'':$guideinfo->avatar;
            }else{
                $val->headimg = '';
            }
            $val->comment_img = CommentGoodsImage::whereCommentId($val->id)->select('image_name')->get()->toArray();
        } 
        
        return view('wx.goods',['count'=>$count,'goods'=>$goods,'goodsImgLists'=>$goodsImgLists,'goodsSpecs'=>$goodsSpecs,
                    'goodsExt'=>$goodsExt,'goodsGift'=>$goodsGift,'supplier'=>$supplier,'userFavorites'=>$userFavorites,
                    'taid'=>$taid,'guideBase'=>$guideBase,'comments'=>$comments]);
    }
    
    /**
     * 加载全部评论
     * @param int $goodsid 商品的id
     * @return Ambigous 
     */
    public function allComment($goodsid){
        $open_id = Cookie::get('openid');
        $userinfo  = UserWx::whereOpenId($open_id)->select(['uid'])->first();
        //取出当前商品的所有评论
        $comments = CommentGood::whereGoodsId($goodsid)->whereState(1)
        ->orWhere(function ($query) use ($userinfo, $goodsid) {
            $query->where('uid',$userinfo->uid)
                  ->where('goods_id',$goodsid);
        })->orderBy('created_at','desc')->get();
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
                $val->headimg = empty($guideinfo->avatar)?'':$guideinfo->avatar;
            }else{
                $val->headimg = '';
            }
            $val->comment_img = CommentGoodsImage::whereCommentId($val->id)->select('image_name')->get()->toArray();
        }
        return view('wx.goodcomment.allcomment',['comments'=>$comments]);
    }

    //商品价格显示处理
    public function handleGoodsPrice($id)
    {
        $goodsPrice = GoodsSpec::whereId($id)->first()->toArray();
        return $goodsPrice;
    }

    //收藏处理
    public function Collect(){
        $open_id = Cookie::get('openid');
        $uid = UserWx::whereOpenId($open_id)->first();
        $goodsid = Input::get('goodsid');
        $userfavorites = new UserFavorite();
        $userfavoritelists = $userfavorites->where(['open_id'=>$open_id,'goods_id'=>$goodsid])->get()->toArray();
        if(empty($userfavoritelists)) {
            $userfavorites->uid = $uid->uid;
            $userfavorites->open_id = $open_id;
            $userfavorites->goods_id = $goodsid;
            $userfavorites->save();
            return 1;
        }
        if(!empty($userfavoritelists)){
            $userfavorites->where(['open_id'=>$open_id,'goods_id'=>$goodsid])->delete();
            return 2;
        }
    }
    //供应商商品列表
    public function supplierGoodsList($id)
    {
        $open_id = Cookie::get('openid');
        $supplierGoodsLists = SupplierBase::whereId($id)->first()->toArray();
        $pageNum = 0;
        $goodsLists = $this->supplierLimit($id,$pageNum,$open_id);
        return view('wx.goods.store',['supplierGoodsLists'=>$supplierGoodsLists,'goodsLists'=>$goodsLists]);
    }

    private function supplierLimit($id,$pageNum,$open_id)
    {
        $offset = $pageNum * self::page;
        $goodsLists = GoodsBase::whereSupplierId($id)->whereState(GoodsBase::state_online)->orderBy('num_sold','desc')
            ->offset($offset)->limit(self::page)->get();

        $goodsLists = GoodsSpec::goodsSpecPriceCartNum($goodsLists,$open_id);
        return $goodsLists;
    }

    public function supplierGoodListPage(Request $request)
    {
        $open_id = Cookie::get('openid');
        $pageNum = $request->input('pageNum',1);
        $id = $request->input('id',1);
        $goodsLists = $this->supplierLimit($id,$pageNum,$open_id);
        if($goodsLists->isEmpty()){
            return response()->json(['ret' => 'no']);
        }

        return response()->json(['GoodBases' => $goodsLists,'page_num'=>$pageNum]);

    }

    //搜索
    public function SearchGoods(Request $request)
    {
        $open_id = Cookie::get('openid');
        $name = $request->input('title','');
        $data = $request->input('data',0);
        $display_state = $request->input('display_state',0);
        $q = json_decode(Cookie::get('q'),true);//cookie
        if(!is_null($q)){
            $q = array_unique($q);
        }
        $param['name'] = $name;
        $GoodBases = new GoodsBase();

        if($name != ''){
            if(count($q) < 10){
                if(count($q) == 0){
                    $q[] = $name;
                }else{
                    array_unshift($q,$name);
                }
            }else{
                array_pop($q);
                array_unshift($q,$name);
            }
            Cookie::queue('q',json_encode($q));
            $GoodBases = $GoodBases->where('title', 'like', '%' . $name . '%');
        }

        if($data == 1){
            Cookie::queue(Cookie::forget('q'));
            return response()->json(['ret'=>'yes']);
        }

        $GoodBases = $GoodBases->whereState(GoodsBase::state_online)->select(['id','title','num_sold','cover','first_image']);
        if($display_state == 0){
            $GoodBases = $GoodBases->orderBy('num_sold','desc')->get();
        }else{
            $GoodBases = $GoodBases->orderBy('num_favorite','desc')->get();
        }

        $keyWords = ConfHotWord::orderBy('display_order','desc')->get();
        $GoodBases = GoodsSpec::goodsSpecPriceCartNum($GoodBases,$open_id);
        //dd($GoodBases);
        return view('wx.goods.search',compact('param','GoodBases','display_state','q','keyWords'));

    }

    //分类
    public function CategoryGoods($category_id = 0,$display_state = 0)
    {
        $ConfCategorys = ConfCategory::select(['name','id'])->orderBy('display_order','desc')->get();
        $open_id = Cookie::get('openid');
        $GoodBases = GoodsBase::whereState(GoodsBase::state_online);
        $param['limit'] = 10;
        $param['page_num'] = 0;
        $param['category_id'] = $category_id;
        $param['display_state'] = $display_state;

        $GoodBases = $this->categoryGoodsDetail($GoodBases,$open_id,$category_id,$display_state,$param['page_num'],$param['limit']);
        $count = $this->count($open_id);
        $state = 1;
        return view('wx.goods.category_goods',compact('GoodBases','param','ConfCategorys','state','count'));
    }

    //分页
    public function categoryGoodsLimit(Request $request)
    {
        $category_id = $request->input('category_id',0);
        $display_state = $request->input('display_state',0);
        $page_num = $request->input('pageNum',1);
        $open_id = Cookie::get('openid');
        $GoodBases = GoodsBase::whereState(GoodsBase::state_online);
        $GoodBases = $this->categoryGoodsDetail($GoodBases,$open_id,$category_id,$display_state,$page_num,self::page);
        //无商品

        if($GoodBases->isEmpty()){
            return response()->json(['ret' => 'no']);
        }
        $GoodBases = $GoodBases->toArray();
        return response()->json(['GoodBases' => $GoodBases,'page_num'=>$page_num]);

    }

    private function categoryGoodsDetail($GoodBases,$open_id,$category_id,$display_state,$page_num,$limit)
    {
        if($display_state == 0){
            $GoodBases = $GoodBases->orderBy('num_sold','desc');
        }else{
            $GoodBases = $GoodBases->orderBy('num_favorite','desc');
        }
        $offset = $page_num * $limit;
        if($category_id == 0){
            $GoodBases = $GoodBases->offset($offset)->limit($limit)->get();
            //$GoodBases = $GoodBases->paginate(2);
        }else{
            $GoodBases = $GoodBases->offset($offset)->limit($limit)->whereCategoryId($category_id)->get();
        }

        $GoodBases = GoodsSpec::goodsSpecPriceCartNum($GoodBases,$open_id);
        return $GoodBases;
    }

}
