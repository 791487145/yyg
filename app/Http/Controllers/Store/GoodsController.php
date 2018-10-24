<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Store\StoreController;
use App\Models\ConfCategory;
use App\Models\ConfExpress;
use App\Models\ConfPavilion;
use App\Models\GoodsBase;
use App\Models\GoodsDesc;
use App\Models\GoodsExt;
use App\Models\GoodsGift;
use App\Models\GoodsImage;
use App\Models\GoodsOptLog;
use App\Models\GoodsSpec;
use App\Models\SupplierBase;
use App\Models\SupplierExpress;
use App\Models\UserCart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\Console\Tests\Input\InputTest;
use Illuminate\Support\Facades\View;
class GoodsController extends StoreController
{
    private $offset = 20;
    /* 商品库 */
    public function index($state = 1){
        //接收参数
        $goodsName = Input::get('goods_name');
        $category = Input::get('category_id');
        $pavilion = Input::get('pavilion');
        $numSoldStart = Input::get('num_sold_start');
        $numSoldEnd = Input::get('num_sold_end');
        $location = Input::get('location');
        $goodsBase = GoodsBase::whereSupplierId($this->user['id']);
        switch ($state){
            case 0:
                $goodsBase = $goodsBase->whereState(GoodsBase::state_online)->whereNum($state);
                break;
            default:
                //1--出售中 2--已下架
                $goodsBase = $goodsBase->whereState($state);
                break;
        }
        //判断参数
        if ($goodsName) {
            //根据商品搜索
            $goodsBase = $goodsBase->where('title', 'like', '%' . $goodsName . '%');
            View::share('goods_name', $goodsName);
        }
        if ($category) {
            //根据分类搜索
            $goodsBase = $goodsBase->whereCategoryId($category);
            View::share('category_id', $category);
        }
        if ($pavilion) {
            //根据分馆搜索
            $goodsBase = $goodsBase->wherePavilion($pavilion);
            View::share('pavilion_id', $pavilion);
        }
        if ($numSoldStart > 0 && $numSoldEnd > $numSoldStart) {
            //根据销量搜索
            $goodsBase = $goodsBase->whereBetween('num_sold', [$numSoldStart, $numSoldEnd]);
            View::share('num_sold_start', $numSoldStart);
            View::share('num_sold_end', $numSoldEnd);
        }
        if($location){
            //根据橱窗搜索
            $goodsBase = $goodsBase->whereLocation($location);
            View::share('location_id', $location);
        }
        //获取数据
        $goodsList = $goodsBase->orderBy('id','desc')->paginate($this->offset);
        //属性拼接
        foreach ($goodsList as $goods) {
            $goodsSpec = $this->getPrice($goods->id);
            $goods->price_buying = $goodsSpec['price_buying'];
            $goods->price = $goodsSpec['price'];
            $goods->gift = 0;
            $goods->img = $goods->first_image;
            $goodsGift = GoodsGift::whereGoodsId($goods->id)->get()->toArray();
            if($goodsGift){
                $goods->gift = 1;
            }
        }
        //获取分类
        $categories = ConfCategory::all();
        //获取分馆
        $pavilions = ConfPavilion::whereState(ConfPavilion::state_online)->get();
        return view('store.goods.list')->with([
            'goodsList'=>$goodsList,
            'state'=>$state,
            'categories' => $categories,
            'pavilions' => $pavilions
        ]);
    }
    /* 商品审核列表页 */
    public function review($state = 0)
    {
        $param = self::supplierInfo();
        switch ($state){
            case 1:
                $status = '已通过';
                $goodsBase = GoodsBase::whereSupplierId($this->user['id'])->whereState($state);
                break;
            case 3:
                $status = '已驳回';
                $goodsBase = GoodsBase::whereSupplierId($this->user['id'])->whereState($state);
                break;
            case 0:
                $status = '待审核';
                $goodsBase = GoodsBase::whereSupplierId($this->user['id'])->whereState($state);
                break;
        }
        $keywords = Input::get('keywords');
        if($keywords){
            $goodsBase = $goodsBase->where('title','like','%'.$keywords.'%')->orderBy('id','desc')->paginate($this->offset);
        }else{
            $goodsBase = $goodsBase->orderBy('id','desc')->paginate($this->offset);
        }
        $goodsBase->keywords = $keywords;
        $states = [0, 1, 3];
        if (in_array($state, $states)) {
            foreach ($goodsBase as $goods){
                //获取价格区间,分馆,分类
                $category = ConfCategory::whereId($goods->category_id)->first();
                if ($category){
                    $goods->category_name = $category->name;
                }
                $pavilion = ConfPavilion::whereId($goods->pavilion)->first();
                if ($pavilion){
                    $goods->pavilion_name = $pavilion->name;
                }
                $goods->price = $this->getPrice($goods->id);
                $goods->status = $status;
                $goods->img = $goods->first_image;
            }
            return view('store.goods.review')->with(['goodsBase'=>$goodsBase,'state'=>$state,'param'=>$param]);
        }

    }

    /* 创建表单 */
    public function create()
    {
        //获取分类
        $conf_categories = ConfCategory::all();
        //获取场馆
        $pavilions = ConfPavilion::whereState(ConfPavilion::state_online)->get();
        //获取商品 当作赠品
        $goods = GoodsBase::whereSupplierId($this->user['id'])->whereState(GoodsBase::state_online)->get();

        $param = self::supplierInfo();
        //是否设置运费
        $supplierExpress = SupplierExpress::whereSupplier_id($this->user['id'])->first();
        return view('store.goods.add')->with([
            'pavilions' => $pavilions,
            'conf_categories' => $conf_categories,
            'goods' => $goods,
            'param' => $param,
            'supplierExpress'=>$supplierExpress,
        ]);
    }

    /* 添加商品 */
    public function store(Request $request)
    {
        $data=$request->all();
        //验证表单
        $validator=Validator::make(
            array(
                'cover'         =>  Input::get('cover'),
                'images'        =>  Input::get('images')
            ),
            array(
                'cover'         =>  'required',
                'images'        =>  'required'
            ),
            array(
                'cover.required'         =>  '请上传商品封面',
                'images.required'        =>  '请上传商品轮播图'
            )
        );
        if($validator->fails()){
            return $this->getReturnResult('no',$validator->errors()->first());
        }
        /* 判断商品参数 */
        foreach ($data['spec_name'] as $spec_key => $spec_val){
            $price = isset($data['price'])?$data['price'][$spec_key]:0;
            $price_buying = isset($data['price_buying'])?$data['price_buying'][$spec_key]:0;
            $price_market = isset($data['price_market'])?$data['price_market'][$spec_key]:0;
            /*$express_fee_mode = isset($data['express_fee_mode'])?$data['express_fee_mode'][$spec_key]:2;
            $is_pick_up = isset($data['is_pick_up'])?$data['is_pick_up'][$spec_key]:2;*/
            if ($price < $price_buying){
                return $this->getReturnResult('no','零售价必须大于供应价');
            }
            if ($price_market == 0){
                return $this->getReturnResult('no','原价必须大于零');
            }
            /*if ($express_fee_mode!= 1 && $is_pick_up != 1){
                return $this->getReturnResult('no','请选择运费');
            }*/
        }
        //存入商品goods_base
        $baseData = [
            'title'         =>  Input::get('title',''),
            'supplier_id'   =>  $this->user['id'],
            'category_id'   =>  Input::get('category_id',0),
            'cover'         =>  Input::get('cover',''),
            'pavilion'      =>  Input::get('pavilion',0)
        ];
        $goodsBase = GoodsBase::create($baseData);
        $goodsId = $goodsBase->id;
        //商品扩展goods_ext
        $extData = [
            'goods_id'          =>  $goodsId,
            'important_tips'    =>  Input::get('important_tips',''),
            'send_out_address'  =>  Input::get('send_out_address',''),
            'send_out_desc'     =>  Input::get('send_out_desc',''),
            'product_area'      =>  Input::get('product_area',''),
            'shelf_life'        =>  Input::get('shelf_life',''),
            'pack'              =>  Input::get('pack',''),
            'store'             =>  Input::get('store',''),
            'express_desc'      =>  Input::get('express_desc',''),
            'sold_desc'         =>  Input::get('sold_desc',''),
            'level'             =>  Input::get('level',''),
            'product_license'   =>  Input::get('product_license',''),
            'company'           =>  Input::get('company',''),
            'dealer'            =>  Input::get('dealer',''),
            'food_addiitive'    =>  Input::get('food_addiitive',''),
            'food_burden'       =>  Input::get('food_burden',''),
            'address'           =>  Input::get('address',''),
            'remark'            =>  Input::get('remark',''),
            'description'       =>  $this->getPushDescription(Input::get('myEditor','')),
        ];
        GoodsExt::create($extData);
        // 商品图片goods_images
        foreach ($data['images'] as $key=>$image){
            $imageData = [
                'goods_id'  =>  $goodsId,
                'name'      =>  $image
            ];
            if($key == 0){
                GoodsBase::whereId($goodsId)->update(['first_image'=>$image]);
            }
            GoodsImage::create($imageData);
        }
        // 商品 属性/规格 goods_spec
        $goodsStock = 0;
        foreach ($data['spec_name'] as $spec_key => $spec_val){
            $goods_spec = [
                'goods_id'      =>  $goodsId,
                'name'          =>  $spec_val,
                'pack_num'      =>  isset($data['pack_num'])?$data['pack_num'][$spec_key]:'',
                'num'           =>  isset($data['num'])?$data['num'][$spec_key]:'',
                //'num_sold'      =>  $data['num_sold'][$spec_key],//已售数量
                'weight'        =>  isset($data['weight'])?$data['weight'][$spec_key]:'',
                'weight_net'    =>  isset($data['weight_net'])?$data['weight_net'][$spec_key]:'',
                'long'          =>  isset($data['long'])?$data['long'][$spec_key]:'',
                'wide'          =>  isset($data['wide'])?$data['wide'][$spec_key]:'',
                'height'        =>  isset($data['height'])?$data['height'][$spec_key]:'',
                'price'         =>  isset($data['price'])?$data['price'][$spec_key]:'',
                'price_buying'  =>  isset($data['price_buying'])?$data['price_buying'][$spec_key]:'',
                'price_market'  =>  isset($data['price_market'])?$data['price_market'][$spec_key]:'',
                'state'         =>  1,
                //'platform_fee'  =>  $data['platform_fee'][$spec_key],//平台服务费
                //'guide_rate'    =>  $data['guide_rate'][$spec_key],//导游分成
                //'travel_agency_rate'  =>  $data['travel_agency_rate'][$spec_key],//旅行社分成
                //'express_fee_mode'    =>  isset($data['express_fee_mode'])?$data['express_fee_mode'][$spec_key]:2,
                //'is_pick_up'    =>  isset($data['is_pick_up'])?$data['is_pick_up'][$spec_key]:2
            ];
            $goodsStock += isset($data['num'])?$data['num'][$spec_key]:0;
            GoodsSpec::create($goods_spec);
        }
        $goodsBase->num = $goodsStock;
        $goodsBase->save();
        //赠品
        if(isset($data['giftId'])){
            foreach ($data['giftId'] as $key=>$gift){
                $giftData = [
                    'goods_id' =>  $goodsId,
                    'gift_id'  =>  $gift,
                    'spec_id'  =>  isset($data['giftSpec'])?$data['giftSpec'][$key]:0
                ];
                GoodsGift::create($giftData);
            }

        }
        //写入操作日志goods_opt_log
        $content['source'] = 'store';
        $content['content'] = '添加ID：'.$goodsId.'商品';
        $content['content'] = urlencode($content['content']);
        $content = urldecode(json_encode($content));
        $goods_opt_log = [
            'type'      =>  '4',
            'uid'       =>  $this->user['id'],
            'gid'       =>  $goodsId,
            'content'   =>  $content
        ];
        GoodsOptLog::create($goods_opt_log);
        return $this->getReturnResult('yes','商品添加成功');
    }

    /* 商品编辑 */
    function update($id){
        $action = Input::get('action') ? Input::get('action') : false;
        $seletc = Input::get('image_select');
        //局部更新
        if($action){
            $ids = explode(',',$id);
            switch ($action){
                case 'off':
                    foreach ($ids as $id){
                        $goods = GoodsBase::whereSupplierId($this->user['id'])->whereId($id)->first();
                        if(!$goods){
                            return false;
                        }
                        if ($goods->state == 1){
                            $goods->state = 2;
                            GoodsGift::whereGiftId($goods->id)->delete();
                            UserCart::whereGoodsId($goods->id)->delete();
                            $goods->save();
                        }
                    }
                    $data = ['ret'=>'SUCCESS','msg'=>'商品下架成功'];
                    break;
                case 'on':
                    foreach ($ids as $id) {
                        $goods = GoodsBase::whereSupplierId($this->user['id'])->whereId($id)->first();
                        if (!$goods) {
                            return false;
                        }
                        if ($goods->state == 2) {
                            $goods->state = 1;
                            $goods->save();
                        }
                    }
                    $data = ['ret'=>'SUCCESS','msg'=>'商品上架成功'];
                    break;
                case 'delete':
                    foreach ($ids as $id){
                        $goods = GoodsBase::whereSupplierId($this->user['id'])->whereId($id)->first();
                        if(!$goods){
                            return false;
                        }
                        $goods->state = -1;
                        $goods->save();
                    }
                    $data = ['ret'=>'SUCCESS','msg'=>'商品删除成功'];
                    break;
            }
                return response()->json($data);
        }
        //商品更新
            $data = Input::all();
            $goods = GoodsBase::whereId($id)->whereSupplierId($this->user['id'])->first();
            if ($goods->state <= 1){
                return $this->getReturnResult('no','操作失败');
            }
            /* 判断商品参数 */
        foreach ($data['spec_name'] as $spec_key=>$spec_name){
                $price = isset($data['price'])?$data['price'][$spec_key]:0;
                $price_buying = isset($data['price_buying'])?$data['price_buying'][$spec_key]:0;
                $price_market = isset($data['price_market'])?$data['price_market'][$spec_key]:0;
                //$express_fee_mode = isset($data['express_fee_mode'])?$data['express_fee_mode'][$spec_key]:2;
                //$is_pick_up = isset($data['is_pick_up'])?$data['is_pick_up'][$spec_key]:2;

            if ($price < $price_buying){
                    return $this->getReturnResult('no','零售价必须大于供应价');
                }

                if ($price_market == 0){
                    return $this->getReturnResult('no','原价必须大于零');
                }

                /*if ($express_fee_mode!= 1 && $is_pick_up != 1){
                    return $this->getReturnResult('no','请选择运费');
                }*/
            }

            //保存商品GoodsBase
            $goods_base = [
                'title'         =>  $data['title'],
                'category_id'   =>  $data['category_id'],
                'cover'         =>  $data['cover'],
                'pavilion'      =>  $data['pavilion']
            ];
            if (Input::get('review') == 1){
                $goods_base['state'] = 0;
            }
            GoodsBase::find($id)->update($goods_base);
            //保存商品属性GoodsExt
            $goods_ext = [
                'important_tips'    =>  Input::get('important_tips',''),
                'send_out_address'  =>  Input::get('send_out_address',''),
                'send_out_desc'     =>  Input::get('send_out_desc',''),
                'product_area'      =>  Input::get('product_area',''),
                'shelf_life'        =>  Input::get('shelf_life',''),
                'pack'              =>  Input::get('pack',''),
                'store'             =>  Input::get('store',''),
                'express_desc'      =>  Input::get('express_desc',''),
                'sold_desc'         =>  Input::get('sold_desc',''),
                'level'             =>  Input::get('level',''),
                'product_license'   =>  Input::get('product_license',''),
                'company'           =>  Input::get('company',''),
                'dealer'            =>  Input::get('dealer',''),
                'food_addiitive'    =>  Input::get('food_addiitive',''),
                'food_burden'       =>  Input::get('food_burden',''),
                'address'           =>  Input::get('address',''),
                'remark'            =>  Input::get('remark',''),
                'description'       =>  $this->getPushDescription(Input::get('myEditor','')),
            ];
            GoodsExt::whereGoodsId($id)->update($goods_ext);
            //保存商品图片GoodsImages 只有加操作
           /* if(isset($data['images'])){
                //添加轮播图
                foreach ($data['images'] as $image_val){
                    $goods_images = [
                        'goods_id'  =>  $id,
                        'name'      =>  $image_val
                    ];
                    GoodsImage::create($goods_images);
                }
            }*/
        if(!is_null($data['image_add'])){
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
        /*
        foreach($data['spec_id'] as $spec_key=>$spec_id){
            //保存改价日志
            $goodsSpec = GoodsSpec::whereId($spec_id)->pluck('price_buying');
                if(isset($goodsSpec) && $goodsSpec != $data['price_buying'][$spec_key]){
                    $goodsOptLog = new GoodsOptLog();
                    $goodsOptLog->type = GoodsOptLog::type_change_priceBuying;
                    $goodsOptLog->uid = $this->user['id'];
                    $goodsOptLog->gid = $id;
                    $logData['supplier_base_id'] = $this->user['id'];
                    $logData['user'] = urlencode($this->user['name']);
                    $logData['source'] = 'supplier';
                    $logData['action'] = urlencode('修改供应单价');
                    $logData['goods_title'] = urlencode($goods->title);
                    $logData['spec_id'] = $spec_id;
                    $logData['before'] = $goodsSpec;
                    $logData['after'] = number_format($data['price_buying'][$spec_key],2);
                    $goodsOptLog->content = urldecode(json_encode($logData));
                    $goodsOptLog->save();
                }
        }*/
        /** 保存商品规格GoodsSpec  spec_id存在则修改,不存在则添加 **/
            foreach ($data['spec_name'] as $spec_key=>$spec_name){
                //修改规格
                $goods_spec = [
                    'name'          =>  $spec_name,
                    'pack_num'      =>  isset($data['pack_num'])?$data['pack_num'][$spec_key]:'',
                    'num'           =>  isset($data['num'])?$data['num'][$spec_key]:'',
                    //'num_sold'      =>  $data['num_sold'][$spec_key],//已售数量
                    'weight'        =>  isset($data['weight'])?$data['weight'][$spec_key]:'',
                    'weight_net'    =>  isset($data['weight_net'])?$data['weight_net'][$spec_key]:'',
                    'long'          =>  isset($data['long'])?$data['long'][$spec_key]:'',
                    'wide'          =>  isset($data['wide'])?$data['wide'][$spec_key]:'',
                    'height'        =>  isset($data['height'])?$data['height'][$spec_key]:'',
                    'price_market'  =>  isset($data['price_market'])?$data['price_market'][$spec_key]:'',
                    //'price_buying'  =>  isset($data['price_buying'])?$data['price_buying'][$spec_key]:'',
                    //'platform_fee'  =>  $data['platform_fee'][$spec_key],//平台服务费
                    //'guide_rate'    =>  $data['guide_rate'][$spec_key],//导游分成
                    //'travel_agency_rate'  =>  $data['travel_agency_rate'][$spec_key],//旅行社分成
                    //'express_fee_mode'    =>  isset($data['express_fee_mode'])?$data['express_fee_mode'][$spec_key]:'',
                   // 'is_pick_up'    =>  isset($data['is_pick_up'])?$data['is_pick_up'][$spec_key]:'',
                ];
                if ($goods->state == 3){
                    $goods_spec['price'] = isset($data['price'])?$data['price'][$spec_key]:'';
                    $goods_spec['price_buying'] = isset($data['price_buying'])?$data['price_buying'][$spec_key]:'';
                    $goods_spec['price_market'] = isset($data['price_market'])?$data['price_market'][$spec_key]:'';
                }
                if(!empty($data['spec_id'][$spec_key])){
                    $GoodsBase = GoodsBase::whereId($id)->first();
                    $GoodsSpecNum = GoodsSpec::whereId($data['spec_id'][$spec_key])->pluck('num');
                    if($GoodsSpecNum != $data['num'][$spec_key]){
                        $goodsTotalNum = $GoodsBase->num - $GoodsSpecNum + $data['num'][$spec_key];
                        GoodsBase::whereId($id)->update(['num'=>$goodsTotalNum]);
                    }
                    GoodsSpec::whereId($data['spec_id'][$spec_key])->update($goods_spec);
                }else{
                    $goods_spec['goods_id'] = $id;
                    GoodsSpec::create($goods_spec);
                }
            }
            //保存赠品GoodsGift 只有加操作
            if(isset($data['giftId'])){
                //添加赠品
                foreach ($data['giftId'] as $key => $gift_val){
                    $goods_gift = [
                        'goods_id'  =>  $id,
                        'gift_id'   =>  $gift_val,
                        'spec_id'   =>  isset($data['giftSpec'])?$data['giftSpec'][$key]:0
                    ];
                    GoodsGift::create($goods_gift);
                }
            }


            //写入操作日志goods_opt_log
        $content['source'] = 'store';
        $content['content'] = '更新ID:'.$id.'商品';
        $content['content'] = urlencode($content['content']);
        $content = urldecode(json_encode($content));
            $goods_opt_log = [
                'type'      =>  '3',
                'uid'       =>  $this->user['id'],
                'gid'       =>  $id,
                'content'   =>  $content
            ];
            GoodsOptLog::create($goods_opt_log);
            if (Input::get('review') == 1){
               return $this->getReturnResult('yes','商品已重新上传成功');
            }
            return $this->getReturnResult('yes','商品编辑成功');

    }

    function reviewEdit($id){
        return $this->edit($id);
    }
    function reviewShow($id){
        return $this->show($id);
    }

    /* GET商品更新 */
    function edit($id){
        $param = self::supplierInfo();
        $goodsBase = GoodsBase::whereSupplierId($this->user['id'])->whereId($id)->first();
        if ($goodsBase){
            if ($goodsBase->state <= 1){
                return Redirect::to(url('goods',$id));
            }
            $goods = GoodsBase::getGoods($goodsBase);
            if($goods){
                //添加赠品及规格数据
                $gifts = GoodsBase::whereSupplierId($this->user['id'])->whereState(GoodsBase::state_online)->get();
                $goods->gifts = $gifts;
                foreach ($goods->gift as $gift){
                    $gift->goods = GoodsBase::whereId($gift->gift_id)->first();
                    $gift->spec = GoodsSpec::whereId($gift->spec_id)->first();
                }
                $goods->ext->description = $this->getFetchDescription($goods->ext->description);
                return view('store.goods.edit')->with(['goods' => $goods,'param'=>$param]);
            }
        }
    }

    /* 商品详情 */
    function show($id){
        $goodsBase = GoodsBase::whereSupplierId($this->user['id'])->whereId($id)->first();
        $goods = GoodsBase::getGoods($goodsBase);
        $param = self::supplierInfo();
        $goods->ext->description = $this->getFetchDescription($goods->ext->description);
        if($goods){
            //添加赠品及规格数据
            $gifts = GoodsBase::whereSupplierId($this->user['id'])->whereState(1)->get();
            if(!$gifts->isEmpty()){
                $specs = GoodsSpec::whereGoodsId($gifts->first()->id)->get();
                $goods->gifts = $gifts;
                $goods->specs = $specs;
                foreach ($goods->gift as $gift){
                    $gift->goods = GoodsBase::whereId($gift->gift_id)->first();
                    $gift->spec = GoodsSpec::whereId($gift->spec_id)->first();
                }
            }
            return view('store.goods.show',compact('goods','param'));
        }

    }
    /* 删除商品 */
    function destroy($id){
        //return response()->json(['ret'=>'SUCCESS','msg'=>'商品删除成功']);
    }

    /* AJAX操作商品 */
    function ajaxEdit($id,$action){
        switch ($action){
            case 'image':
                $image = GoodsImage::whereId($id)->first();
                if($image){
                    $goods = GoodsBase::whereId($image->goods_id)->first();
                    if ($goods->supplier_id == $this->user['id']){
                        GoodsImage::whereId($id)->delete();
                        $data = $this->getReturnResult('yes','图片删除成功');
                    }
                }
                break;

            case 'spec':
                $spec = GoodsSpec::whereId($id)->first();
                if($spec){
                    $goods = GoodsBase::whereId($spec->goods_id)->first();
                    if ($goods->supplier_id == $this->user['id']){
                        GoodsSpec::whereId($id)->update(['state' => 0]);
                        $data = $this->getReturnResult('yes','规格删除成功');
                    }
                }
                break;
            case 'gift':
                $gift = GoodsGift::whereId($id)->first();
                if($gift){
                    $goods = GoodsBase::whereId($gift->goods_id)->first();
                    if ($goods->supplier_id == $this->user['id']){
                        GoodsGift::whereId($id)->delete();
                        $data = $this->getReturnResult('yes','赠品删除成功');
                    }
                }
                break;
        }
        return response()->json($data);
    }

    /* AJAX商品图片上传 */
    function upload(){
        return response()->json(['img'=>$this->uploadToQiniu(),'id'=>time()]);
    }

    /* 获取赠品规格 */
    function goodsSpecs($id){
        $goods = GoodsBase::whereSupplierId($this->user['id'])->whereState(1)->whereId($id)->first();
        $specs = [];
        if($goods){
            $specs = GoodsSpec::whereGoodsId($goods->id)->get();
            $goods->img = $goods->first_image;
        }
        return response()->json(['data'=>$specs,'state'=>1,'goods'=>$goods]);
    }

    /* 获取赠品规格 */
    function goodsSpec($id){
        if ($id <= 0){
            return response()->json(['state'=>0]);
        }
        $spec = GoodsSpec::whereId($id)->first();
        return response()->json(['data'=>$spec,'state'=>1]);
    }
}
