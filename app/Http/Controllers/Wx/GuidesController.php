<?php

namespace App\Http\Controllers\Wx;

use App\Models\UserBase;
use App\Models\UserCart;
use Cookie;
use App\Models\GoodsBase;
use App\Models\GoodsSpec;
use App\Models\GuideBase;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\GuideStoreGood;

class GuidesController extends WxController
{
    /*
     * 导游商品列表
     * $id 导游id
     * */
    public function GuidesGoodsList($id,Request $request)
    {
        //微信端授权接口
        if(env('APP_ENV') == 'local'){
            $open_id = 'o1-zuw6uMAPVZB5Oc-uQUcBiQw-Q';
        }else{
            $open_id = self::setWxAuth($request,$_SERVER['REQUEST_URI']);
        }
        if($open_id != ''){
            Cookie::queue('openid',$open_id);
        }
        $pageNum = 0;
        $GuidesStore = GuideBase::whereId($id)->first();
        $GuidesStore->real_name = UserBase::whereId($GuidesStore->uid)->pluck('nick_name');
        $GoodsLists = $this->GuideGoods($GuidesStore,$open_id,$pageNum);

        //bind
        self::bindWxGuideId($open_id,$id);

        return view('wx.guides.store',['goodslists'=>$GoodsLists,'guidesstore'=>$GuidesStore,'id'=>$id]);
    }

    private function GuideGoods($GuidesStore,$open_id,$pageNum)
    {
        $offset = $pageNum * (self::page);
        //$GuidesGoodsLists = GuideStoreGood::whereGuideId($GuidesStore->uid)->offset($offset)->limit(self::page)->orderBy('id','desc')->lists('goods_id');
        $GuidesGoodsLists = GuideStoreGood::whereGuideId($GuidesStore->uid)->offset($offset)->orderBy('id','desc')->limit(10)->lists('goods_id');;
        //dd($GuidesGoodsLists);
        $GoodsLists = array();
        if(!empty($GuidesGoodsLists)){
            foreach($GuidesGoodsLists as $GuidesGoodsList){
                $param = GoodsBase::whereId($GuidesGoodsList)->first();
                if(!is_null($param)){
                    $GoodsLists[] = $param;
                }
            }
            //dd($GuidesGoodsLists);
            $GoodsLists = GoodsSpec::goodsSpecPriceCartNum($GoodsLists,$open_id);
        }
        return $GoodsLists;
    }


    public function GuidesGoodsLimit(Request $request)
    {
        $id = $request->input("id");
        $open_id = $request->input("open_id");
        $pageNum = $request->input("pageNum");
        $GuidesStore = GuideBase::whereId($id)->first();
        $goodsLists = $this->GuideGoods($GuidesStore,$open_id,$pageNum);
        //dd($goodsLists);
        return response()->json(['GoodBases' => $goodsLists,'page_num'=>$pageNum]);
    }

}
