<?php

namespace App\Http\Controllers\Admin;

use App\Models\ConfCity;
use App\Models\ConfPavilionCity;
use Queue;
use Log;
use Validator;
use Redirect;
use Session;
use App\Models\User;
use App\Jobs\NewPush;
use App\Models\UserWx;
use App\Models\UserNews;
use App\Models\UserBase;
use App\Models\OrderGood;
use App\Models\ConfTheme;
use App\Models\ConfBanner;
use App\Models\ConfExpress;
use App\Models\ConfHotWord;
use App\Models\ConfPavilion;
use App\Models\PlatformNews;
use App\Models\ConfPavilionTag;
use App\Models\GoodsBase;
use App\Models\GoodsMaterialBase;
use App\Models\GoodsMaterialImage;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use App\Models\ConfCategory;
use zgldh\QiniuStorage\QiniuStorage;
use PhpParser\Node\Expr\Cast\Object_;
use App\Models\GoodsGift;
use App\Models\SupplierBase;


class ConfController extends BaseController
{
    //文字公告
    const noticeType = 1;
    const imgNoticeType = 2;
    private $location = 99;//app or 微信商城

    function lunBolist()
    {
        $name = Input::get('name','');
        $location = Input::get('location',$this->location);
        $pavilion_id = Input::get('pavilion_id','');
        $confBanners = new ConfBanner();
        $tmp['name'] = $name;
        $tmp['pavilion_id'] = $pavilion_id;
        $tmp['location'] = $location;

        if ($name != '') {
            $confBanners = $confBanners->where('name', 'like', '%' . $name . '%');
        }

        if ($location != $this->location) {
            $confBanners = $confBanners->where('location',$location);
        }

        if (!empty($pavilion_id)) {
            $confBanners = $confBanners->where('pavilion_id', $pavilion_id);
        }

        $confPavilions = ConfPavilion::whereState(ConfPavilion::state_online)->get();
        $confPavilionsId = ConfPavilion::whereState(ConfPavilion::state_online)->lists('id');
        $confPavilionsId[] = "$this->pavilion_id";
        $confBanners = $confBanners->orderBy('display_order', 'asc')->whereIn('state', [0, 1, 2])->whereIn('pavilion_id',$confPavilionsId)->paginate($this->page);

        foreach ($confBanners as $confBanner) {
            $confPavilion = ConfPavilion::whereId($confBanner->pavilion_id)->first();
            if($confBanner->location == 1){
                $confBanner->location = 'app导游端';
            }else{
                $confBanner->location = '微商城';
            }
            if(!is_null($confPavilion)){
                $confBanner->pavilion_name = $confPavilion->name;
            }
        }

        return view("boss.conf.lunbo_list",compact( 'confBanners', 'confPavilions','tmp'));

    }

    function addLunBo()
    {
        $confPavilions = ConfPavilion::whereState(ConfPavilion::state_online)->get();
        return view("boss.conf.lunbo_add", ['confPavilions' => $confPavilions]);
    }

    //文件上传
    public function upload()
    {
        return response()->json(['img' => $this->uploadToQiniu()]);
    }

    //文件上传
    public function uploadMaterial()
    {
        return response()->json(['img' => $this->uploadPlupLoadToQu()]);
    }
    
    function addingLunBo()
    {
        $data = Input::all();

        $validator = Validator::make($data, [
            'name' => 'required',
            'pavilion_id' => 'required',
            'display_order' => 'required',
            'url_type' => 'required',
        ]);
        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator)->withInput();//输出错误信息
        }

        $ConfBanner = new ConfBanner();
        $ConfBanner->name = $data['name'];
        $ConfBanner->pavilion_id = $data['pavilion_id'];
        $ConfBanner->cover = $data['cover'];
        $ConfBanner->location = $data['location'];
        $ConfBanner->display_order = $data['display_order'];
        $ConfBanner->url_type = $data['url_type'];
        $ConfBanner->url_content = $data['url_content'];
        $ConfBanner->start_time = $data['start_time'];
        $ConfBanner->end_time = $data['end_time'];
        $ConfBanner->state = ConfBanner::WAIT_ONLINE;
        $ConfBanner->save();

    }

    function updateLunBo($id)
    {
        $confPavilions = ConfPavilion::whereState(ConfPavilion::state_online)->get();
        $confBanners = ConfBanner::whereId($id)->first();
        return view("boss.conf.lunbo_edit",compact('confBanners','confPavilions'));
    }

    function editLunBo()
    {
        $data = Input::all();
        $data['state']= ConfBanner::WAIT_ONLINE;
        $ConfBanner = new ConfBanner();
        $ConfBanner->whereId($data['id'])->update($data);
    }

    function delLunBo($id)
    {
        $ConfBanner = new ConfBanner();
        $ConfBanner->whereId($id)->update(['state' => ConfBanner::state_del]);
        return $id;
    }

    //地方馆
    function localShoplist()
    {
        $ConfPavilions = ConfPavilion::orderBy('display_order', 'desc')->whereState(ConfPavilion::state_online)->paginate($this->page);

        foreach($ConfPavilions as $ConfPavilion){
            $ConfPavilion->goods_num = GoodsBase::wherePavilion($ConfPavilion->id)->count();
            $city_ids = ConfPavilionCity::wherePavilionId($ConfPavilion->id)->lists('city_id');
            $city_names = ConfCity::whereIn('id',$city_ids)->lists('name')->toArray();
            $ConfPavilion->city_names = implode(',',$city_names);
            $Goods_id = GoodsBase::wherePavilion($ConfPavilion->id)->lists('id');
            $prices = OrderGood::whereIn('goods_id',$Goods_id)->lists('price');
            $ConfPavilion->amount = array_sum($prices->toArray());
        }

        return view("boss.conf.local_shop_list", ['ConfPavilions' => $ConfPavilions]);
    }

    function localShopadd()
    {
        $confPavilions = ConfPavilion::whereState(ConfPavilion::state_online)->get();
        $citys = ConfCity::whereParentId(1)->get();
        $selectedCitys = ConfPavilionCity::lists('city_id')->toArray();
        return view("boss.conf.local_shop_add", ['confPavilions' => $confPavilions,'citys'=>$citys,'selectedCitys'=>$selectedCitys]);
    }

    function addLocalShop()
    {
        $data = Input::all();
        $validator = Validator::make($data, [
            'name' => 'required',
            'display_order' => 'required',
            'citys' => 'required'
        ]);
        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator)->withInput();//输出错误信息
        }
        $ConfPavilion = new ConfPavilion();
        $ConfPavilion->name = $data['name'];
        $ConfPavilion->display_order = $data['display_order'];
        $ConfPavilion->cover = $data['cover'];
        $ConfPavilion->new_cover = $data['newImg'];
        $ConfPavilion->state = 1;
        $ConfPavilion->background = $data['background'];
        $ConfPavilion->description = $data['description'];
        $RET = $ConfPavilion->save();
        
        foreach($data['citys'] as $key=>$value){
            $confPavilionCitys = new ConfPavilionCity();
            $selectedCitys = $confPavilionCitys->lists('city_id')->toArray();
            if(!in_array($value,$selectedCitys)){
                $confPavilionCitys->pavilion_id = $ConfPavilion->id;
                $confPavilionCitys->city_id = $value;
                $confPavilionCitys->save();
            }
        }


            foreach ($data['tag_name'] as $k => $v) {
                if($v){
                    $ConfPavilionTag = new ConfPavilionTag();
                    $ConfPavilionTag->name = $v;
                    $ConfPavilionTag->pavilion_id = $ConfPavilion->id;
                    $ConfPavilionTag->goods_id = $data['goods_id'][$k];
                    $ConfPavilionTag->display_order = $data['display_order_tag'][$k];
                    $ConfPavilionTag->save();
                }
            }
        return $RET=['ret'=>$RET];
    }

    function localShopdel($action, $id)
    {
        if ($action == 'del') {
            $ConfPavilion = new ConfPavilion();
            $ConfPavilion->whereId($id)->update(array('state' => ConfPavilion::state_del));
            ConfPavilionCity::wherePavilionId($id)->delete();
        } else {
            $ConfPavilion = new ConfPavilionTag();
            $ConfPavilion->whereId($id)->delete();
        }

        return $id;
    }

    public function localShopUpdate($id)
    {
        $citys = ConfCity::whereParentId(1)->get();
        $selectedCitys = ConfPavilionCity::where('pavilion_id','!=',$id)->lists('city_id')->toArray();
        $selfSelectedCitys = ConfPavilionCity::wherePavilionId($id)->lists('city_id')->toArray();
        $ConfPavilions = ConfPavilion::whereId($id)->first();
        $ConfPavilionTags = ConfPavilionTag::wherePavilionId($ConfPavilions->id)->get();
        return view("boss.conf.local_shop_edit", ['ConfPavilions' => $ConfPavilions, 'ConfPavilionTags' => $ConfPavilionTags,'citys'=>$citys,'selectedCitys'=>$selectedCitys,'selfSelectedCitys'=>$selfSelectedCitys]);

    }

    public function localShopEdit()
    {
        $data = Input::all();
        $ConfPavilion = new ConfPavilion();
        $tmp = array();
        $tmp['name'] = $data['name'];
        $tmp['display_order'] = $data['display_order'];
        $tmp['cover'] = $data['cover'];
        $tmp['new_cover'] = $data['newImg'];
        $tmp['background'] = $data['background'];
        $tmp['description'] = $data['description'];
        $RET = $ConfPavilion->whereId($data['id'])->update($tmp);

        if(isset($data['tag_id'])) {
            foreach ($data['tag_id'] as $k => $v) {
                $ConfPavilionTag = new ConfPavilionTag();
                $tem = array();
                $tem['goods_id'] = $data['goods_id'][$k];
                $tem['name'] = $data['tag_name'][$k];
                $tem['display_order'] = $data['display_order_tag'][$k];
                $ConfPavilionTag->whereId($v)->update($tem);
            }
        }

        if($data['citys']){
            ConfPavilionCity::wherePavilionId($data['id'])->delete();
            foreach($data['citys'] as $value){
                $confCitys = new ConfPavilionCity();
                $confCitys->pavilion_id = $data['id'];
                $confCitys->city_id = $value;
                $confCitys->save();
            }
        }

        if(!empty($data['tag_names'])){
            foreach ($data['tag_names'] as $k => $v) {
                $ConfPavilionTag = new ConfPavilionTag();
                $ConfPavilionTag->name = $v;
                $ConfPavilionTag->pavilion_id = $data['id'];
                $ConfPavilionTag->goods_id = $data['goods_ids'][$k];
                $ConfPavilionTag->display_order = $data['display_order_tags'][$k];
                $ConfPavilionTag->save();
            }
        }
        return $RET;

    }

    //商品品类
    function categorylist()
    {
        $name = Input::get('name');
        $ConfCategorys = new ConfCategory();

        if ($name != '') {
            $ConfCategorys = $ConfCategorys->where('name', 'like', '%' . $name . '%');
        }

        $tmp['name'] = $name;
        $ConfCategorys = $ConfCategorys->orderBy('display_order','asc')->paginate($this->page);
        return view("boss.conf.category_list", compact('ConfCategorys','tmp'));
    }

    function categoryAdd(Request $request)
    {
        if($request->isMethod('post')){
            $data = Input::all();
            $validator = Validator::make($data, [
                'name' => 'required',
                'display_order' => 'required'
            ]);
            if ($validator->fails()) {
                return Redirect::back()->withErrors($validator)->withInput();//输出错误信息
            }

            $ConfCategorys = new ConfCategory();
            $ConfCategorys->name = $data['name'];
            $ConfCategorys->display_order = $data['display_order'];
            $ConfCategorys->save();
            return response()->json(['ret'=>'yes','msg'=>BaseController::CREATESUCCESS]);
        }else{
            return view("boss.conf.category_add");
        }
    }

    function delCategory($id)
    {
        $ConfCategorys = new ConfCategory();
        $ConfCategorys->whereId($id)->delete();
        return $id;
    }

    function categoryEdit($id)
    {
        $ConfCategorys = ConfCategory::whereId($id)->first();
        return view("boss.conf.category_edit", ['ConfCategorys' => $ConfCategorys]);
    }

    function editCategory($id)
    {
        $data = Input::all();
        $ConfCategorys = new ConfCategory();
        $ConfCategorys->whereId($id)->update($data);
    }

    //关键字
    function keyWordlist()
    {
        $ConfHotWords = new ConfHotWord();
        $name = Input::get('name');
        $tmp = array();

        if ($name != '') {
            $ConfHotWords = $ConfHotWords->where('name', 'like', '%' . $name . '%');
        }

        $ConfHotWords = $ConfHotWords->orderBy('display_order', 'asc')->paginate($this->page);
        $tmp['name'] = $name;
        return view("boss.conf.keyword_list", compact('ConfHotWords','tmp'));
    }

    function keyWordadd(Request $request)
    {
        if($request->isMethod('post')){
            $data = Input::all();
            $validator = Validator::make($data, [
                'name' => 'required',
                'url' => 'required',
                'display_order' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(['yet'=>'no']);
            }

            $ConfHotWord = new ConfHotWord();
            $ConfHotWord->name = $data['name'];
            $ConfHotWord->url = $data['url'];
            $ConfHotWord->display_order = $data['display_order'];
            $ConfHotWord->save();
            return response()->json(['yet'=>'yes']);
        }else{
            return view("boss.conf.keyword_add");
        }
    }

    function delKeyWord($id)
    {
        $ConfHotWord = new ConfHotWord();
        $ConfHotWord->whereId($id)->delete();
        return $id;
    }

    function keyWordEdit($id)
    {
        $ConfHotWords = ConfHotWord::whereId($id)->first();
        return view("boss.conf.keyword_edit", ['ConfHotWords' => $ConfHotWords]);
    }

    function editKeyWord($id)
    {
        $data = Input::all();
        $ConfHotWord = new ConfHotWord();
        $ConfHotWord->whereId($id)->update($data);
    }

    //专题
    function themelist()
    {
        $name = Input::get('name','');
        $pavilion_id = Input::get('pavilion_id','');
        $location = Input::get('location',$this->location);
        $tmp['name'] = $name;
        $tmp['pavilion_id'] = $pavilion_id;
        $tmp['location'] = $location;
        $ConfsThemes = new ConfTheme();

        if ($name != null) {
            $ConfsThemes = $ConfsThemes->where('name', 'like', '%' . $name . '%');
        }

        if ($location != $this->location) {
            $ConfsThemes = $ConfsThemes->where('location',$location);
        }

        if ($pavilion_id != null) {
            $ConfsThemes = $ConfsThemes->where('pavilion_id', $pavilion_id);
        }

        $ConfPavilions = ConfPavilion::whereState(ConfPavilion::state_online)->get();
        $ConfsThemes = $ConfsThemes->orderBy('display_order', 'asc')->whereState(ConfTheme::state_online)->paginate($this->page);
        foreach ($ConfsThemes as $ConfsTheme) {
            $ConfPavilion = ConfPavilion::whereId($ConfsTheme->pavilion_id)->first();
            if($ConfsTheme->location == 1){
                $ConfsTheme->location = 'app导游端';
            }else{
                $ConfsTheme->location = '微商城';
            }
            $ConfsTheme->pavilion_name = $ConfPavilion->name;
        }

        return view("boss.conf.theme_list", compact('ConfPavilions', 'ConfsThemes','tmp'));
    }

    public function themeAdd()
    {
        $ConfPavilions = ConfPavilion::whereState(ConfPavilion::state_online)->get();
        return view("boss.conf.theme_add", ['ConfPavilions' => $ConfPavilions]);
    }

    public function addTheme()
    {
        $data = Input::all();
        $validator = Validator::make($data, [
            'name' => 'required',
            'url' => 'required',
            'url_type' => 'required'
        ]);
        if ($validator->fails()) {
           return $state = ['ret'=>'no','msg'=>'*为必填项，不能为空'];
        }
        $confTheme = new ConfTheme();
        $confTheme->name = $data['name'];
        $confTheme->url = $data['url'];
        $confTheme->url_type = $data['url_type'];
        $confTheme->pavilion_id = $data['pavilion_id'];
        $confTheme->display_order = $data['display_order'];
        $confTheme->cover = $data['cover'];
        $confTheme->content = trim($data['content']);
        $confTheme->location = $data['location'];
        $result = $confTheme->save();
        if($result){
            return $state = ['ret'=>'yes','msg'=>'保存成功！'];
        }else{
            return $state = ['ret'=>'no','msg'=>'保存失败！'];
        }
    }

    public function delTheme($id)
    {
        $ConfTheme = new ConfTheme();
        $ConfTheme->whereId($id)->update(array("state" => ConfTheme::state_del));
        return $id;
    }

    public function themeEdit($id)
    {
        $ConfThemes = ConfTheme::whereId($id)->first();
        $ConfPavilion = ConfPavilion::whereId($ConfThemes->pavilion_id)->first();
        $ConfPavilions = ConfPavilion::whereState(ConfPavilion::state_online)->get();
        $ConfThemes->pavilion_name = $ConfPavilion->name;
        return view("boss.conf.theme_edit", ['ConfThemes' => $ConfThemes, 'ConfPavilions' => $ConfPavilions]);
    }

    public function editTheme($id)
    {
        $data = Input::all();
        $confTheme = new ConfTheme();
        $confTheme->whereId($id)->update($data);
        if($confTheme){
            return $data = ['ret'=>'yes','msg'=>'更新成功'];
        }else{
            return $data = ['ret'=>'no','msg'=>'更新失败'];
        }
    }

    //素材库
    public function materialList()
    {
        $goods_name = Input::get('goods_name');
        $goods_name = trim($goods_name);
        $start_time = Input::get('start_time'); 
        $end_time   = Input::get('end_time'); 
        $supplierid = Input::get('supplierid',0);
        $GoodBases  = GoodsBase::whereIn('state',[GoodsBase::state_online,GoodsBase::state_down,GoodsBase::state_finish])->get();
        $GoodsMaterialBases = GoodsMaterialBase::orderBy('id','desc');
        $supplierbases      = SupplierBase::whereState(SupplierBase::STATE_VALID)->get();
        foreach($supplierbases as $supplier){
            if($supplier->name == ''){
                $supplier->name = 'SupplierID.'.$supplier->id;
            }
        }
        //进行筛选
        $GoodsMaterialBases = $this->materialFiltrate($GoodsMaterialBases,$goods_name,$start_time,$end_time,$supplierid);
        $GoodsMaterialBases = $GoodsMaterialBases->paginate(10);
        //$GoodsMaterialBases = GoodsMaterialBase::orderBy('id','desc')->paginate(2);
        foreach ($GoodsMaterialBases as $GoodsMaterialBase) {
            $GoodsImages = GoodsMaterialImage::whereMaterialId($GoodsMaterialBase->id)->get();
            $GoodBase = GoodsBase::whereId($GoodsMaterialBase->goods_id)->first();
            if(!is_null($GoodBase)){
                $GoodsMaterialBase->title = $GoodBase->title;
                $GoodsMaterialBase->cover = $GoodBase->cover;
            }
            $tmp = array();
            foreach ($GoodsImages as $GoodsImage) {
                $tmp[] = $GoodsImage->image_name;
            }
            $GoodsMaterialBase->tmp = $tmp;

        }
        return view("boss.conf.material_list")->with(['GoodBases' => $GoodBases, 'GoodsMaterialBases' => $GoodsMaterialBases,'supplierbases'=>$supplierbases,'goods_name'=>$goods_name,'start_time'=>$start_time,'end_time'=>$end_time,'supplierid'=>$supplierid]);
    }
    
    /**
     * 对素材进行筛选的函数
     * @param Object $object
     * @param string $goodname
     * @param string $starttime
     * @param string $endtime
     * @param string $supplier 0全部  1有赠品      2无赠品
     * @return object
     */
    public function materialFiltrate($object,$goodname,$starttime,$endtime,$supplierid){
        if($goodname){
            //先从goods_base数据表里面查询出所有的包含关键字的记录的id
            $goodsbase = GoodsBase::whereIn('state',[GoodsBase::state_online,GoodsBase::state_down,GoodsBase::state_finish])->where('title','like','%'.$goodname.'%')->select('id')->get()->toArray();
            $ids       = array();
            foreach($goodsbase as $val){
                $ids[] = $val['id'];
            }
            //再从goods_material_base数据表里面取出所有的goods_id和ids进行对比
            $trueid            = array();
            $goodsMaterialBase = GoodsMaterialBase::select('id','goods_id')->get()->toArray();
            foreach($goodsMaterialBase as $v){
                if(in_array($v['goods_id'], $ids)){
                    $trueid[]  = $v['id'];
                }
            }
            //进行真正的从goods_material_base进行真正的筛选
            $object->whereIn('id',$trueid);
        }
        if($starttime){
            if($endtime > $starttime){
                $object->whereBetween('created_at',[$starttime,$endtime]);
            }
        }elseif($endtime){
            $object->where('created_at','<',$endtime);
        }
        //进行特定供应商的素材筛选
        if($supplierid){
            $object->where('supplier_id',$supplierid);
        }
        /* $giftsid    = array();
        $containId  = array();
        $exclusiveId= array();
        //选出有赠品的所有的商品的id
        $goodsId = GoodsGift::select('goods_id')->get()->toArray();
        foreach($goodsId as $id){
            //所有有赠品商品的id
            $giftsid[] = $id['goods_id'];
        }
        $goodsMaterialBase = GoodsMaterialBase::select('id','goods_id')->get()->toArray();
        foreach($goodsMaterialBase as $v){
            if(in_array($v['goods_id'], $giftsid)){
                //有赠品的id集合
                $containId[]   = $v['id'];
            }else{
                //无赠品的id集合
                $exclusiveId[] = $v['id'];
            }
        }
        if($supplier == 1){ 
            if(!empty($containId)){
                $object->whereIn('id',$containId);
            }
        }elseif($supplier == 2){
            if(!empty($exclusiveId)){
                $object->whereIn('id',$exclusiveId);
            }
        } */
        
        return $object;
    }
    
    /**
     * 加载编辑素材页面的函数
     * @param int $id 当前素材的id
     */
    public function editMaterial($id){
        $GoodsMaterialBase = GoodsMaterialBase::whereId($id)->first();
        $goodid   = $GoodsMaterialBase->goods_id;
        $content  = $GoodsMaterialBase->content;
        $goodname = GoodsBase::whereId($goodid)->first()->title;
        $GoodsImages = GoodsMaterialImage::whereMaterialId($id)->get();
        return view("boss.conf.material_edit",['goodname'=>$goodname,'content'=>$content,'id'=>$id,'GoodsImages'=>$GoodsImages]);
    }
    
    /**
     * 更新素材的函数
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateMaterial(){
        $data = Input::all();
        $param= array();
        $param= !empty($data['image_name'])?$data['image_name']:array();
        $goodsMaterialBase = new GoodsMaterialBase();
        $flag = $goodsMaterialBase->whereId($data['id'])->update(['content'=>$data['content']]);
        if(!empty($param)){
            $GoodsMaterialImage = new GoodsMaterialImage();
            //用来判断照片是否被删除掉,因为那边的更新时往数据库里面从新添加
            $imageinfo = GoodsMaterialImage::whereMaterialId($data['id'])->select('image_name')->get()->toArray();
            $imagesname= array();
            foreach($imageinfo as $img){
                $imagesname[] = $img['image_name'];
            }
            foreach($param as $image){
                /* $GoodsMaterialImage->material_id = $data['id'];
                $GoodsMaterialImage->image_name  = $image;
                $GoodsMaterialImage->save(); */
                if(!in_array($image,$imagesname)){
                    $mark = $GoodsMaterialImage->insert(['material_id'=>$data['id'],'image_name'=>$image]);
                    if(!$mark){
                        return response()->json(['ret'=>'no','msg'=>'素材更新失败']);
                    } 
                }
                
            }
        }
        if($flag){
            return response()->json(['ret'=>'yes','msg'=>'素材更新成功']);
        }else{
            return response()->json(['ret'=>'no','msg'=>'素材更新失败']);
        }
    }
    
    public function materialImgDel(){
        $materialid = Input::get('id');
        $image_name = Input::get('name');
        GoodsMaterialImage::whereMaterialId($materialid)->where('image_name',$image_name)->delete();
    }

    public function materialDel ($id)
    {
        GoodsMaterialBase::whereId($id)->delete();
        $ret = GoodsMaterialImage::whereMaterialId($id)->delete();

        if($ret){
            return response()->json(['ret'=>'yes']);
        }
        return response()->json(['ret'=>'no']);
    }

    function addMaterial()
    {
        $data = Input::all();
        $id   = $data['id'];
        $supplierid = GoodsBase::whereId($id)->first()->supplier_id;
        if($data['id'] == ''){
           return response()->json(['ret'=>'no','msg'=>'请选择添加关联商品']); 
        }
        $GoodsMaterialBase = new GoodsMaterialBase();
        $GoodsMaterialBase->goods_id = $data['id'];
        $GoodsMaterialBase->content  = $data['content'];
        $GoodsMaterialBase->supplier_id = $supplierid;
        $mark = $GoodsMaterialBase->save();
        if(!$mark){
            return response()->json(['ret'=>'no','msg'=>'素材存储失败']);
        }

        if (!empty($data['image_name'])) {
            foreach ($data['image_name'] as $value) {
                $GoodsMaterialImage = new GoodsMaterialImage();
                $GoodsMaterialImage->material_id = $GoodsMaterialBase->id;
                $GoodsMaterialImage->image_name = $value;
                $flag = $GoodsMaterialImage->save();
                if(!$flag){
                    return response()->json(['ret'=>'no','msg'=>'素材存储失败']);
                }
            }
        }
        return response()->json(['ret'=>'yes','msg'=>'素材存储成功']);
    }
    
    //显示平台公告
    function platFormNotices()
    {
        $platFormNotices = PlatformNews::orderBy('id', 'desc')->where('cover','=','')->paginate(15);
        $state = self::noticeType;
        return view("boss.conf.notice_list", ['platFormNotices' => $platFormNotices,'state'=>$state]);
    }

    //保存公告
    function platFormNotice()
    {
        $data = Input::all();
        $validator = Validator::make($data, [
            'title' => 'required',
            'content' => 'required'
        ]);
        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator);//输出错误信息
        }

        $platFormNotices = new PlatformNews();
        $platFormNotices->title = $data['title'];
        $platFormNotices->content = $data['content'];
        $platFormNotices->save();
        if ($platFormNotices->save()) {
            return redirect('/conf/confnotices');
        }
    }

    //发送公告
    function updateNotice($id)
    {
        $tem = array();
        $platFormNotices = new PlatformNews();
        $tem['state'] = 1;
        $platFormNotices->whereId($id)->update($tem);

        $UserBase = UserBase::whereIsGuide(1)->get();
        foreach($UserBase as $v){
            $UserNew = new UserNews();
            $UserNew->uid = $v['id'];
            $UserNew->news_id = $id;
            $UserNew->save();
        }

        \Queue::push(new NewPush($id));

        return $id;
    }

    //删除公告
    function deleteNotice($id)
    {
        $platFormNotices = new PlatformNews();
        $platFormNotices->whereId($id)->delete();
    }

    //图文公告
    function ImgNotice()
    {
        $platFormNotices = PlatformNews::orderBy('id', 'desc')->where('cover','!=','')->paginate(10);
        $state = self::imgNoticeType;
        return view("boss.conf.notice_img",['platFormNotices' => $platFormNotices,'state'=>$state]);
    }

    //添加图文公告页面
    function addImgNotice()
    {
        return view("boss.conf.addnotice_img");
    }

    //处理添加图文
    function handleaddimg()
    {
        $data = Input::all();
        $validator = Validator::make($data, [
            'title' => 'required',
            'url' =>'required',
            'content' => 'required',
            'cover' => 'required'
        ]);
        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator);//输出错误信息
        }

        $platFormNotices = new PlatformNews();
        $platFormNotices->title = $data['title'];
        $platFormNotices->url = $data['url'];
        $platFormNotices->cover = $data['cover'];
        $platFormNotices->content = $data['content'];
        $platFormNotices->save();
        if ($platFormNotices->save()) {
            return redirect('/conf/confImgnotices');
        }
    }


    //快递公司
    function expressList()
    {
        $datas = ConfExpress::orderBy('order_sort', 'desc')->paginate($this->page);;
        return view("boss.conf.express_list", compact('datas'));
    }

    public function expressAdd(Request $request)
    {
        if($request->isMethod("post")){
            $param = $request->all();
            $ConfExpress = new ConfExpress();
            $ConfExpress->name = $param['name'];
            $ConfExpress->tel = $param['tel'];
            $ConfExpress->order_sort = $param['order_sort'];
            $ConfExpress->save();
            if($ConfExpress->id == 0){
                return response()->json(['yet'=>'no']);
            }else{
                return response()->json(['yet'=>'yes']);
            }
        }else{
            return view("boss.conf.express_add");
        }
    }

    public function expressDel($id)
    {
        ConfExpress::whereId($id)->delete();
        return $id;
    }

    public function expressEdit($id)
    {
        $ConfExpress = ConfExpress::whereId($id)->first();
        return view("boss.conf.express_edit",compact("ConfExpress"));
    }

    public function editExpress(Request $request)
    {
        $param = $request->all();
        ConfExpress::whereId($param['id'])->update($param);
        return response()->json(['yet'=>'yes']);

    }

}