<?php

namespace App\Http\Controllers\Wx;

use App\Models\ConfTheme;
use App\Models\GoodsSpec;
use Cookie;
use App\Models\ConfBanner;
use App\Models\UserCart;
use App\Http\Requests;
use App\Models\GoodsBase;
use App\Models\ConfPavilion;
use App\Models\ConfPavilionTag;
use App\Http\Controllers\Wx\WxLocationController;

class PavilionController extends WxController
{
    public function pavilionGoods($id,$display_state = 0)
    {
        $open_id = Cookie::get('openid');
        $pavilion = ConfPavilion::whereId($id)->first();
        $GoodBases = GoodsBase::whereState(GoodsBase::state_online)->wherePavilion($id);

        if($display_state == 0){
            $GoodBases = $GoodBases->orderBy('num_sold','desc');
        }else{
            $GoodBases = $GoodBases->orderBy('num_favorite','desc');
        }

        $pavilion->banners = ConfBanner::wherePavilionId($id)->whereState(ConfBanner::state_online)->whereLocation(ConfBanner::location_wx)->get();
        $pavilion->themes = ConfTheme::wherePavilionId($id)->whereState(ConfTheme::state_online)->whereLocation(ConfTheme::location_wx)->get();

        $GoodBases = $GoodBases->get();
        $GoodBases = GoodsSpec::goodsSpecPriceCartNum($GoodBases,$open_id);
        //dd($GoodBases);

        return view('wx.pavilions.pavilion_goods',compact('GoodBases','display_state','pavilion'));
    }

    public function pavilionlists()
    {
        $Pavilions = ConfPavilion::whereState(ConfPavilion::state_online)->orderBy('display_order','desc')->get();

        $pavilionBanners = ConfBanner::wherePavilionId(9999)->whereLocation(ConfBanner::location_wx)
            ->whereState(ConfBanner::state_online)->orderBy('display_order','desc')->get();

        foreach($Pavilions as $Pavilion){
            $tags = ConfPavilionTag::wherePavilionId($Pavilion->id)->orderBy('display_order','desc')->get();
            $Pavilion->tag = $tags;
        }
        return view('wx.pavilions.pavilion',compact('Pavilions','pavilionBanners'));
    }

    public function indexLocation($id)
    {
        $Pavilions = ConfPavilion::whereState(ConfPavilion::state_online)->orderBy('display_order','desc')
            ->where('id','!=',$id)->get();

        $pavilionBanners = ConfBanner::wherePavilionId(9999)->whereLocation(ConfBanner::location_wx)
            ->whereState(ConfBanner::state_online)->orderBy('display_order','desc')->get();

        $ConfPavilion = ConfPavilion::whereId($id)->first();

        foreach($Pavilions as $Pavilion){
            $tags = ConfPavilionTag::wherePavilionId($Pavilion->id)->orderBy('display_order','desc')->get();
            $Pavilion->tag = $tags;
        }

        $signPackage = WxLocationController::signature();
        return view('wx.pavilions.pavilion_location',compact('Pavilions','pavilionBanners','ConfPavilion','signPackage'));
    }

}
