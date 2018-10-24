<?php

namespace App\Http\Controllers\Store;

use App\Models\ConfCategory;
use App\Models\GoodsBase;
use App\Models\GoodsSpec;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class GoodsAuditController extends Controller
{

    public function index()
    {
        return view('store.goods.check');
    }

    //商品审核
    public function reviewGood($state)
    {

        $goods = GoodsBase::whereState($state)->paginate(5);
        foreach($goods as $good){
            $ConfCategory = ConfCategory::whereId($good->category_id)->first();
            $GoogSpec = GoodsSpec::whereId($good->id)->first();
            $good->category_name = $ConfCategory->name;
            $good->price_buying = $GoogSpec->price_buying;
        }
        $good->state=$state;

        return view('store.goods.check',['good'=>$good]);
    }

}
