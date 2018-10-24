<?php

namespace App\Http\Controllers\Travel;

use App\Models\TaBase;
use App\Models\TaSm;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\GenController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use App\Models\ConfCity;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
class TravelController extends GenController
{
    public $user = array();
    function __construct(){
        $this->user = \Session::get(TaBase::SESSION_TA);
        $travel = TaBase::whereId($this->user['id'])->first();
        $route = Route::currentRouteAction();
        list($controller, $action) = explode('@', $route);
        $controller = explode('\\',$controller);
        $controller = end($controller);
        View::share('controller', $controller);
        View::share('action', $action);
        View::share('travel', $travel);

    }
    
    /* 获取城市 */
    function getCity($province){
        if($province == 0){
            return response()->json(false);
        }
        $city = ConfCity::whereParentId($province)->get();
        return response()->json($city);
    }
    
    /* 旅行社logo上传 */
    function uploadPlupLoad(){
        return response()->json(['img' => $this->uploadPlupLoadToQu()]);
    }
    
    /* 弹出框供应商设置 */
    function AlertSetting(){
        $travelinfo = $this->user;
        $params = Input::all();
        $data = array();
        $data['ta_logo']        = $params['ta_logo'];
        $data['ta_province_id'] = $params['travel_province_id'];
        $data['ta_city_id']     = $params['travel_city_id'];
        TaBase::whereId($travelinfo['id'])->update($data);
        return Redirect::back();
        
    }

    /* 验证手机验证码 */
    function checkTravelCode($mobile,$type,$code){
        $sms = TaSm::whereMobile($mobile)->whereType($type)->whereIsValid(0)->orderBy('created_at','desc')->first();
        if (!$sms){
            return false;
        }else{
            if($sms->code != $code){
                return false;
            }
        }
        return true;
    }
}
