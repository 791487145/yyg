<?php namespace App\Http\Controllers\Api;

use App\Models\ConfBanner;
use App\Models\ConfPavilion;
use App\Models\ConfTheme;
use App\Models\GoodsBase;
use App\Models\GoodsSpec;
use Log;
use Illuminate\Http\Request;
use App\Http\Controllers\Wx\WxLocationController;
use App\Http\Controllers\GenController;


class WxController extends GenController
{

    /**
 * @SWG\get(path="/v1/wx/userInfo",
 *   tags={"wx"},
 *   summary="获取openID",
 *   description="",
 *   operationId="wx",
 *  produces={"application/json"},
 *   @SWG\Parameter(
 *     name="iv",
 *     in="query",
 *     description="",
 *     required=true,
 *     type="string",
 *     @SWG\Schema(ref="iv")
 *   ),
 *   @SWG\Parameter(
 *     name="encryptedData",
 *     in="query",
 *     description="",
 *     required=true,
 *     type="string",
 *     @SWG\Schema(ref="encryptedData")
 *   ),
 *   @SWG\Parameter(
 *     name="appid",
 *     in="query",
 *     description="",
 *     required=true,
 *     type="string",
 *     @SWG\Schema(ref="appid")
 *   ),
 *   @SWG\Parameter(
 *     name="sessionKey",
 *     in="query",
 *     description="",
 *     required=true,
 *     type="string",
 *     @SWG\Schema(ref="sessionKey")
 *   ),
 *   @SWG\Response(response=200,description="successful operation"),
 * )
 */
    public function getUserInfo(Request $request)
    {
        $encryptedData = $request->input('encryptedData');
        $appid = $request->input('appid');
        $sessionKey = $request->input('sessionKey');
        $iv = $request->input('iv');

        $errCode = WxLocationController::decryptData($appid,$sessionKey,$encryptedData, $iv, $data);
        return response($errCode);

    }


    /**
     * @SWG\get(path="/v1/wx/sessionKey",
     *   tags={"wx"},
     *   summary="获取sessionKey",
     *   description="",
     *   operationId="wx",
     *  produces={"application/json"},
     *   @SWG\Parameter(
     *     name="js_code",
     *     in="query",
     *     description="",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="js_code")
     *   ),
     *   @SWG\Parameter(
     *     name="secret",
     *     in="query",
     *     description="",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="secret")
     *   ),
     *   @SWG\Parameter(
     *     name="appid",
     *     in="query",
     *     description="",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="$appid")
     *   ),
     *   @SWG\Parameter(
     *     name="grant_type",
     *     in="query",
     *     description="",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="grant_type")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function getSessionKey(Request $request)
    {
        $grant_type = $request->input('grant_type');
        $appid = $request->input('appid');
        $secret = $request->input('secret');
        $js_code = $request->input('js_code');

        $result = WxLocationController::getSessionKeyUrl($appid,$secret,$js_code,$grant_type);
        return response($result);
    }


    /**
     * @SWG\get(path="/v1/wx/wxStoreIndex",
     *   tags={"wx"},
     *   summary="获取sessionKey",
     *   description="",
     *   operationId="wx",
     *  produces={"application/json"},
     *   @SWG\Parameter(
     *     name="openId",
     *     in="query",
     *     description="",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="openId")
     *   ),
     *   @SWG\Parameter(
     *     name="pavilionId",
     *     in="query",
     *     description="",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="pavilionId")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function wxStoreIndex(Request $request)
    {
        $openId = $request->input('openId');
        $pavilionId = $request->input('pavilionId',24);

        $result['banner'] = ConfBanner::wherePavilionId($pavilionId)->whereState(ConfBanner::state_online)->whereLocation(ConfBanner::location_wx)->get();
        $result['theme'] = ConfTheme::wherePavilionId($pavilionId)->whereState(ConfTheme::state_online)->whereLocation(ConfTheme::location_wx)->get();
        $result['ConfPavilions'] = ConfPavilion::whereState(ConfPavilion::state_online)->orderBy('display_order','desc')->paginate(9);
        $result['GoodBases'] = GoodsBase::whereState(GoodsBase::state_online)->whereLocation(GoodsBase::location_index_recommend)
            ->wherePavilion($pavilionId)->orderBy('location_order','desc')->get();
        $result['GoodBases'] = GoodsSpec::goodsSpecPriceCartNum($result['GoodBases'],$openId);

        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => $result);
        return response()->json($result);

    }






}

