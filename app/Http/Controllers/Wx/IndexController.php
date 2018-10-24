<?php

namespace App\Http\Controllers\Wx;

use Log;
use Cookie;
use App\Models\ConfCity;
use App\Models\GoodsSpec;
use App\Models\UserCart;
use App\Http\Requests;
use App\Models\GoodsBase;
use App\Models\ConfTheme;
use App\Models\ConfBanner;
use App\Models\ConfPavilion;
use Illuminate\Http\Request;
use App\Models\ConfPavilionCity;
use App\Http\Controllers\Wx\WxLocationController;

class IndexController extends WxController
{
    public function index(Request $request,$id=0)
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

        $url = $request->path();
        $params = Cookie::get("pavilionId");
        if($url == "/" || $url == "wx-index") {
            $id = $params;
        }else{
            $param = substr($id,-3);
            if($param == "abc"){
                $id = substr($id,0,strlen($id)-3);
            }
            if($id != $params){
                Cookie::queue('pavilionId',$id);
            }
        }

        if($id != 0){
            $ConfPavilion = ConfPavilion::whereState(ConfPavilion::state_online)->whereId($id)->first();
            if(empty($ConfPavilion)){
                $id = 0;
            }
        }
        if($id == 0){
            $ConfPavilion = ConfPavilion::whereState(ConfPavilion::state_online)->whereName("乡亲直供馆")->first();
        }
        
        $ConfBanners = ConfBanner::whereState(ConfBanner::state_online)->select(['id','cover','url_type','url_content'])
            ->wherePavilionId($ConfPavilion->id)->whereLocation(ConfBanner::location_wx)->orderBy('display_order','desc')->get();

        $ConfPavilions = ConfPavilion::whereState(ConfPavilion::state_online)->orderBy('display_order','desc')
            ->select(['id','name','cover'])->paginate(9);

        $ConfThemes = ConfTheme::whereState(ConfTheme::state_online)->orderBy('display_order','desc')
            ->wherePavilionId($ConfPavilion->id)->whereLocation(ConfTheme::location_wx)->select(['cover','url','url_type'])->get();

        $GoodBases = GoodsBase::whereState(GoodsBase::state_online)->whereLocation(GoodsBase::location_index_recommend)
            ->wherePavilion($ConfPavilion->id)->orderBy('location_order','desc')->select(['id','title','num','cover'])->get();

        $GoodBases = GoodsSpec::goodsSpecPriceCartNum($GoodBases,$open_id);
        $state = 0;
        $count = $this->count($open_id);
        $signPackage = WxLocationController::signature();
        if($url == "wx-index"){
            return view('wx.wx-index',compact('ConfPavilion','ConfPavilions','ConfBanners','ConfThemes','GoodBases','state','count','signPackage'));
        }
        return view('wx.index',compact('ConfPavilion','ConfPavilions','ConfBanners','ConfThemes','GoodBases','state','count','signPackage'));

    }


    public function getCityByLngLat(Request $request)
    {
        $lat = $request->input("lat",'');
        $lng = $request->input("lon",'');
        $url = 'http://api.map.baidu.com/geocoder/v2/?location='.$lat.','.$lng.'&output=json&pois=1&ak=h3etG2rzAhgQ7kwsayVh2np1ZmES5I0v';


        $data = json_decode(file_get_contents($url),true);
        $data = $data['result']['addressComponent']['province'];
        $provinceId = ConfCity::whereName($data)->whereParentId(1)->pluck('id');

        $pavilionId = ConfPavilionCity::whereCityId($provinceId)->pluck('pavilion_id');
        if(empty($pavilionId)){
            return response()->json(['ret'=>'no','province'=>$data]);
        }

        $pavilions = ConfPavilion::whereId($pavilionId)->whereState(ConfPavilion::state_online)->first();
        if(empty($pavilions)){
            return response()->json(['ret'=>'no','province'=>$data]);
        }
        return response()->json(['ret'=>'yes','content'=>$pavilions,'province'=>$data]);
    }




}
