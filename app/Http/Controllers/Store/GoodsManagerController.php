<?php

namespace App\Http\Controllers\Store;

use App\Models\GoodsBase;
use App\Models\GoodsSpec;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Crypt;
use App\Models\GoodsGift;
class GoodsManagerController extends Controller
{


    public function index()
    {
        return view('store.goods.manage');
    }

    //分界面
    public function managerGood($state)
    {
        $state = Crypt::decrypt($state);//解密
        if($state == GoodsBase::state_finish) {
            $good = GoodsBase::whereNum(0);
            $good = $good->whereIn('state',[GoodsBase::state_online]);//已售罄
        }elseif($state == GoodsBase::state_down) {
            $good = GoodsBase::whereState($state);//下架
        }else {
            $good = GoodsBase::whereState($state)->where('num','>',0);//出售中
        }

        $good = $good->paginate(5);
        foreach($good as $goods){
            $GoogSpec = GoodsSpec::whereGoodsId($goods->id)->first();
            $GoodsGift = GoodsGift::whereGoodsId($goods->id)->first();
            if(!empty($GoodsGift)){
                $goods->gift_id = $GoodsGift->gift_id;
            }
            $goods->price_buying = $GoogSpec->price_buying;
            $goods->price = $GoogSpec->price;
        }

        $good->state = $state;
        return view('store.goods.manage',['good'=>$good]);

    }


    //删除上下架
    public function doGoods($action,$id)
    {
        switch($action) {
            case 'delete':
                $RET = GoodsBase::whereId($id)->update(['state' => GoodsBase::state_delete]);
                break;

            case 'state_online':
                $RET = GoodsBase::whereId($id)->update(['state' => GoodsBase::state_online]);
                break;

            case 'state_down':
                $RET = GoodsBase::whereId($id)->update(['state' => GoodsBase::state_down]);
                break;
        }

        if($RET){
            return $id;
        }
    }


    //搜索
    public function searchGood()
    {
        $title = Input::get('title');
        $num_min = Input::get('num_min');
        $num_max = Input::get('num_max');
        $state = Input::get('state');
        $good = GoodsBase::where('state',$state);

        if ($title != null) {//商品名
            $good = $good->where('title', 'like', '%'.$title.'%');
        };

        if ($num_min != null) {//销售量
            $good = $good->where('num_sold', '>', $num_min);
        }

        if ($num_max != null) {
            $good = $good->where('num_sold', '<=', $num_max);
        }

        $good = $good->paginate(5);
        $good->state = $state;

        return view('store.goods.manage',['good' => $good]);

    }





}
