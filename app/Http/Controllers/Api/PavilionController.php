<?php namespace App\Http\Controllers\Api;

use App\Models\ConfBanner;
use App\Models\ConfPavilion;
use App\Models\ConfTheme;
use App\Models\CouponBase;
use App\Models\GoodsBase;
use App\Models\GoodsExt;
use App\Models\GoodsGift;
use App\Models\GoodsImage;
use App\Models\GoodsMaterialBase;
use App\Models\GoodsMaterialImage;
use App\Models\GoodsSpec;
use App\Models\GuideStoreGood;
use App\Models\SupplierBase;
use App\Models\UserFavorite;
use Log;
use Lang;
use Illuminate\Http\Request;
use App\Http\Controllers\GenController;


class PavilionController extends GenController
{


    /**
     * @SWG\Get(path="/v1/pavilion/banner_theme",
     *   tags={"pavilion"},
     *   summary="获取Banner和专题",
     *   description="",
     *   operationId="pavilion_id",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="pavilion_id",
     *     in="query",
     *     description="地方錧id",
     *     required=true,
     *     type="integer",
     *     @SWG\Schema(ref="pavilion")
     *   ),
     *   @SWG\Response(response=200,description="successful operation")
     * )
     */
    public function getBannerTheme(Request $request)
    {

        $pavilion_id = $request->input('pavilion_id',0);

        $result = array();
        $result['theme'] = array();
        $result['banner'] = array();

        $ConfBanner = ConfBanner::whereState(ConfBanner::state_online)->wherePavilionId($pavilion_id);
        $ConfBanner = $ConfBanner->orderBy('display_order','desc')->get();
        foreach($ConfBanner as $banner){
            $tmp = array();
            $tmp['name'] = $banner['name'];
            $tmp['type'] = strval($banner['url_type']);
            $tmp['url'] = $banner['url_content'];
            $tmp['cover'] = $banner['cover'] != '' ? env('IMAGE_DISPLAY_DOMAIN').$banner['cover'] : '';
            $result['banner'][] = $tmp;
        }

        $ConfTheme = ConfTheme::whereState(ConfTheme::state_online)->wherePavilionId($pavilion_id);
        $ConfTheme = $ConfTheme->orderBy('display_order','desc')->get();
        foreach($ConfTheme as $v){
            $tmp = array();
            $tmp['name'] = $v['name'];
            $tmp['content'] = $v['name'];
            $tmp['url'] = $v['url'];
            $tmp['url_type'] = strval($v['url_type']);
            $tmp['cover'] = $v['cover'] != '' ? env('IMAGE_DISPLAY_DOMAIN').$v['cover'] : '';
            $result['theme'][] = $tmp;
        }

        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' =>$result );
        return response()->json($result);
    }


}

