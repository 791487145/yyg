<?php namespace App\Http\Controllers\Api;

use App\Models\ConfCity;
use App\Models\ConfPavilion;
use App\Models\ConfPavilionCity;
use App\Models\Device;
use App\Models\GuideBase;
use App\Models\GuideTum;
use App\Models\SmsVerificationCode;
use App\Models\TaBase;
use App\Models\User;
use App\Models\UserBase;
use Log;
use Lang;
use App\Models\StoreUser;
use Illuminate\Http\Request;
use App\Http\Controllers\GenController;
use zgldh\QiniuStorage\QiniuStorage;


/**
 * @SWG\Swagger(
 *     basePath="",
 *     @SWG\Info(
 *         version="1.0",
 *         title=""
 *     )
 * )
 */
class UserNoSignController extends GenController
{

    /**
     * @SWG\Post(path="/v1/user/login",
     *   tags={"user"},
     *   summary="用户登陆",
     *   description="",
     *   operationId="login",
     *   @SWG\Parameter(
     *     name="mobile",
     *     in="query",
     *     description="手机号",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="#/definitions/string")
     *   ),
     *   @SWG\Parameter(
     *     name="password",
     *     in="query",
     *     description="密码",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="#/definitions/string")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function login(Request $request)
    {
        $mobile = $request->input('mobile');
        $password = $request->input('password');


        $UserBase = UserBase::whereMobile($mobile)->whereIsGuide(UserBase::is_guide_yes)->first();
        
        //用户名不正确
        if(is_null($UserBase)){
            $result = array('ret' => self::RET_USER_FAIL, 'msg' => Lang::get('comm.account_error'), 'data' => (object)array());
            return response()->json($result);
        }

        //密码不正确
        if($UserBase['password'] != self::passwdEncode($password,$UserBase['salt'])){
            $result = array('ret' => self::RET_USER_FAIL, 'msg' => Lang::get('comm.password_error'), 'data' => (object)array());
            return response()->json($result);
        }

        UserBase::whereId($UserBase['id'])->update(array('token'=>self::createToken($UserBase['mobile'],$UserBase['id'])));

        $GuideBase = GuideBase::whereUid($UserBase['id'])->first();
        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => self::getGuideUserInfo($GuideBase->id));
        return response()->json($result);
    }



    /**
     * @SWG\Post(path="/v1/user/register",
     *   tags={"user"},
     *   summary="用户注册",
     *   description="",
     *   operationId="register",
     *   @SWG\Parameter(
     *     name="mobile",
     *     in="query",
     *     description="手机号",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="#/definitions/string")
     *   ),
     *   @SWG\Parameter(
     *     name="password",
     *     in="query",
     *     description="密码",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="#/definitions/string")
     *   ),
     *  @SWG\Parameter(
     *     name="recode",
     *     in="query",
     *     description="验证码",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="#/definitions/string")
     *   ),
     *   @SWG\Parameter(
     *     name="invite_code",
     *     in="query",
     *     description="邀请码",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="#/definitions/string")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function register(Request $request)
    {
        $mobile = $request->input('mobile');
        $password = $request->input('password');
        $recode = $request->input('recode');
        $invite_code = strtolower($request->input('invite_code'));


        $num = SmsVerificationCode::whereMobile($mobile)->whereCode($recode)->whereType(SmsVerificationCode::TYPE_REGISTER)->whereIsValid(SmsVerificationCode::IS_VALID_YES)->count();
        if ($num == 0) {
            return response()->json(array('ret' => self::RET_FAIL, 'msg' => Lang::get('comm.recode_error'), 'data' => ''));
        }

        //invite_code
        $firstLetter = substr($invite_code,0,1);
        if($firstLetter == 's'){
            $TaBaseOrUser = User::whereInviteCode($invite_code)->first();
        }else{
            $TaBaseOrUser = TaBase::whereSelfInviteCode($invite_code)->first();
        }
        if(is_null($TaBaseOrUser)){
            return response()->json(array('ret' => self::RET_FAIL, 'msg' => Lang::get('comm.invite_code_error'), 'data' => ''));
        }


        //check 手机号
        if(!preg_match("/(^(13\\d|14[57]|15[^4,\\D]|17[13678]|18\\d)\\d{8}|170[^346,\\D]\\d{7})$/",$mobile)){
            return response()->json(array('ret' => self::RET_FAIL, 'msg' =>Lang::get('comm.mobile_error'), 'data' =>(object)array()));
        }


        $UserBase = UserBase::whereMobile($mobile)->first();
        //check 手机号
        if(!is_null($UserBase) && $UserBase['is_guide'] == UserBase::is_guide_yes && $UserBase['state'] != UserBase::state_zp){
            return response()->json(array('ret' => self::RET_FAIL, 'msg' =>Lang::get('comm.mobile_exist'), 'data' =>(object)array()));
        }

        if(is_null($UserBase)){
            $UserBase = new UserBase();
            $UserBase->mobile = $mobile;
        }

        $UserBase->salt = self::setSalt();
        $UserBase->is_guide = UserBase::is_guide_yes;
        $UserBase->password = self::passwdEncode($password,$UserBase->salt);
        $UserBase->created_at = date('Y-m-d H:i:s');
        $UserBase->state = UserBase::state_checking;
        $UserBase->save();

        
        $GuideBase = GuideBase::whereUid($UserBase->id)->first();
        if(is_null($GuideBase)){
            $GuideBase = new GuideBase();
            $GuideBase->uid = $UserBase->id;
        }

        $GuideBase->invite_code = $invite_code;
        if($firstLetter == 's'){
            $GuideBase->sale_id = $TaBaseOrUser['id'];
        }else{
            $GuideBase->ta_id = $TaBaseOrUser['id'];
        }
        $GuideBase->save();

        //旅行社邀请进来给加帮定关系
        $GuideTum = GuideTum::whereUid($GuideBase->uid)->whereGuideId($GuideBase->id)->whereTaId($GuideBase->ta_id)->first();
        if(is_null($GuideTum) && $GuideBase->ta_id && $GuideBase->ta_id > 0){
            $GuideTum = new GuideTum();
            $GuideTum->guide_id = $GuideBase->id;
            $GuideTum->ta_id = $GuideBase->ta_id;
            $GuideTum->uid = $GuideBase->uid;
            $GuideTum->mobile = $UserBase->mobile;
            $GuideTum->name = $UserBase->nick_name;
            $GuideTum->save();
        }

        SmsVerificationCode::whereMobile($mobile)->whereCode($recode)->whereIsValid(SmsVerificationCode::IS_VALID_YES)->update(array('is_valid'=>SmsVerificationCode::IS_VALID_PASS));
        UserBase::whereId($UserBase->id)->update(array('token'=>self::createToken($UserBase->mobile,$UserBase->id)));

        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => self::getGuideUserInfo($GuideBase->id));
        return response()->json($result);
    }


    /**
     * @SWG\Post(path="/v1/user/change_password",
     *   tags={"user"},
     *   summary="修改密码",
     *   description="",
     *   operationId="changePassword",
     *   @SWG\Parameter(
     *     name="mobile",
     *     in="query",
     *     description="手机号",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="#/definitions/string")
     *   ),
     *   @SWG\Parameter(
     *     name="password",
     *     in="query",
     *     description="密码",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="#/definitions/string")
     *   ),
     *  @SWG\Parameter(
     *     name="recode",
     *     in="query",
     *     description="验证码",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="#/definitions/string")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function changePassword(Request $request)
    {
        $mobile = $request->input('mobile');
        $password = $request->input('password');
        $recode = $request->input('recode');


        $num = SmsVerificationCode::whereMobile($mobile)->whereCode($recode)->whereType(SmsVerificationCode::TYPE_FORGET_PASSWORD)->whereIsValid(SmsVerificationCode::IS_VALID_YES)->count();
        if ($num == 0) {
            return response()->json(array('ret' => self::RET_FAIL, 'msg' => Lang::get('comm.recode_error'), 'data' => ''));
        }

        //check 手机号
        $UserBase = UserBase::whereMobile($mobile)->first();
        if(is_null($UserBase)){
            return response()->json(array('ret' => self::RET_FAIL, 'msg' =>Lang::get('comm.mobile_error'), 'data' =>(object)array()));
        }else{
            if($UserBase->state == UserBase::state_zp){
                $UserBase->is_guide = UserBase::is_guide_yes;
                $UserBase->state = UserBase::state_checking;
            }
            $UserBase->password = self::passwdEncode($password,$UserBase->salt);
            $UserBase->token = self::createToken($UserBase->mobile,$UserBase->id);
            $UserBase->save();
        }

        SmsVerificationCode::whereMobile($mobile)->whereCode($recode)->whereIsValid(SmsVerificationCode::IS_VALID_YES)->update(array('is_valid'=>SmsVerificationCode::IS_VALID_PASS));

        $GuideBase = GuideBase::whereUid($UserBase->id)->first();
        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => self::getGuideUserInfo($GuideBase->id));
        return response()->json($result);
    }


    /**
     * @SWG\Post(path="/v1/user/recode",
     *   tags={"user"},
     *   summary="发送验证码",
     *   description="",
     *   operationId="postRecode",
     *   @SWG\Parameter(
     *     name="mobile",
     *     in="query",
     *     description="手机号",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="#/definitions/string")
     *   ),
     *   @SWG\Parameter(
     *     name="type",
     *     in="query",
     *     description="1.注册，2忘记密码",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="#/definitions/string")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function postRecode(Request $request)
    {
        $mobile = $request->input('mobile');
        $type = $request->input('type',1);
        $ip = ip2long($request->getClientIp());
        Log::alert('orderAdd request:' . print_r($request->input(), true));


        //check 手机号
        if(!preg_match("/(^(13\\d|14[57]|15[^4,\\D]|17[13678]|18\\d)\\d{8}|170[^346,\\D]\\d{7})$/",$mobile)){
            return response()->json(array('ret' => self::RET_FAIL, 'msg' =>Lang::get('comm.mobile_error'), 'data' =>(object)array()));
        }

        $UserBase = UserBase::whereMobile($mobile)->first();

        //忘记密码时，填写的手机号不存在
        if($type == 2 && is_null($UserBase)){
            return response()->json(array('ret' => self::RET_FAIL, 'msg' =>Lang::get('comm.mobile_not_exist'), 'data' =>(object)array()));
        }

        //check 手机号
        if(!is_null($UserBase) && $UserBase['is_guide'] == UserBase::is_guide_yes && $type == 1 && $UserBase['state'] != UserBase::state_zp){
            return response()->json(array('ret' => self::RET_FAIL, 'msg' =>Lang::get('comm.mobile_exist'), 'data' =>(object)array()));
        }


        return self::smsVerification($mobile, $ip, $type);

    }


    /**
     * @SWG\Get(path="/v1/user/recode_register",
     *   tags={"user"},
     *   summary="检查用户注册验证码及邀请码",
     *   description="",
     *   operationId="checkRegisterRecodeInviteCode",
     *  @SWG\Parameter(
     *     name="mobile",
     *     in="query",
     *     description="手机号",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="#/definitions/string")
     *   ),
     *  @SWG\Parameter(
     *     name="recode",
     *     in="query",
     *     description="短信验证码",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="#/definitions/string")
     *   ),
     *   @SWG\Parameter(
     *     name="invite_code",
     *     in="query",
     *     description="邀请码",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="#/definitions/string")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function checkRegisterRecodeInviteCode(Request $request)
    {
        $mobile = $request->input('mobile');
        $recode = $request->input('recode');
        $invite_code = strtolower($request->input('invite_code',''));

        //recode
        $num = SmsVerificationCode::whereMobile($mobile)->whereCode($recode)->whereType(SmsVerificationCode::TYPE_REGISTER)->whereIsValid(SmsVerificationCode::IS_VALID_YES)->count();
        if ($num == 0) {
            return response()->json(array('ret' => self::RET_FAIL, 'msg' => Lang::get('comm.recode_error'), 'data' => ''));
        }

        //invite_code
        $firstLetter = substr($invite_code,0,1);
        if($firstLetter == 's'){
            $TaBaseOrUser = User::whereInviteCode($invite_code)->first();
        }else{
            $TaBaseOrUser = TaBase::whereSelfInviteCode($invite_code)->first();
        }
        if(is_null($TaBaseOrUser)){
            return response()->json(array('ret' => self::RET_FAIL, 'msg' => Lang::get('comm.invite_code_error'), 'data' => ''));
        }

        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => array('is_pass'=>'1'));
        return response()->json($result);
    }


    /**
     * @SWG\Get(path="/v1/user/recode_forget_password",
     *   tags={"user"},
     *   summary="检查验证码",
     *   description="",
     *   operationId="checkRegisterRecodeInviteCode",
     *   @SWG\Parameter(
     *     name="mobile",
     *     in="query",
     *     description="手机号",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="#/definitions/string")
     *   ),
     *  @SWG\Parameter(
     *     name="recode",
     *     in="query",
     *     description="短信验证码",
     *     required=true,
     *     type="string",
     *     @SWG\Schema(ref="#/definitions/string")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function checkForgetPasswordRecode(Request $request)
    {

        $mobile = $request->input('mobile');
        $recode = $request->input('recode');

        //recode
        $num = SmsVerificationCode::whereMobile($mobile)->whereCode($recode)->whereType(SmsVerificationCode::TYPE_FORGET_PASSWORD)->whereIsValid(SmsVerificationCode::IS_VALID_YES)->count();
        if ($num == 0) {
            return response()->json(array('ret' => self::RET_FAIL, 'msg' => Lang::get('comm.recode_error'), 'data' => ''));
        }

        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => array('is_pass'=>'1'));
        return response()->json($result);

    }



    /**
     * @SWG\Post(path="/v1/user/device",
     *   tags={"user"},
     *   summary="设备信息",
     *   description="",
     *   operationId="conf",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="uid",
     *     in="query",
     *     description="用户id",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="string")
     *   ),
     *   @SWG\Parameter(
     *     name="app_name",
     *     in="query",
     *     description="(ios或android)",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="string")
     *   ),
     *  @SWG\Parameter(
     *     name="app_version",
     *     in="query",
     *     description="版本号",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="string")
     *   ),
     *  @SWG\Parameter(
     *     name="device_token",
     *     in="query",
     *     description="device_token",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="string")
     *   ),
     *  @SWG\Parameter(
     *     name="push_badge",
     *     in="query",
     *     description="(1接收，-1禁止)",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="string")
     *   ),
     *  @SWG\Parameter(
     *     name="push_alert",
     *     in="query",
     *     description="(1接收，-1禁止)",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="string")
     *   ),
     * @SWG\Parameter(
     *     name="push_sound",
     *     in="query",
     *     description="(1接收，-1禁止)",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="string")
     *   ),
     * @SWG\Parameter(
     *     name="status",
     *     in="query",
     *     description="(1接收，-1禁止消息推送)",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="string")
     *   ),
     *   @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function postDevice(Request $request)
    {
        $uid = $request->input('uid', 0);
        $app_name = $request->input('app_name', 0);
        $app_version = $request->input('app_version', 0);
        $device_token = $request->input('device_token', 0);
        $push_badge = $request->input('push_badge', 0);
        $push_alert = $request->input('push_alert', 0);
        $push_sound = $request->input('push_sound', 0);
        $status = $request->input('status', 1);

        $Device = Device::whereUid($uid)->first();
        if(!is_null($Device)){
            $Device->app_name = $app_name;
            $Device->app_version = $app_version;
            $Device->device_token = $device_token;
            $Device->push_badge = $push_badge;
            $Device->push_alert = $push_alert;
            $Device->push_sound = $push_sound;
            $Device->status = $status;
            $Device->save();

        }else{
            //添加
            $Device = new Device();
            $Device->uid = $uid;
            $Device->app_name = $app_name;
            $Device->app_version = $app_version;
            $Device->device_token = $device_token;
            $Device->push_badge = $push_badge;
            $Device->push_alert = $push_alert;
            $Device->push_sound = $push_sound;
            $Device->status = $status;
            $Device->save();

        }
        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => array('id' => strval($Device->id)));
        return response()->json($result);
    }




    /**
     * @SWG\Get(path="/v1/user/pavilion",
     *   tags={"user"},
     *   summary="获取当前所属馆",
     *   description="",
     *   operationId="user",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="uid",
     *     in="query",
     *     description="用户id",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="string")
     *   ),
     *   @SWG\Parameter(
     *     name="lng",
     *     in="query",
     *     description="经度",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="string")
     *   ),
     *  @SWG\Parameter(
     *     name="lat",
     *     in="query",
     *     description="纬度",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="string")
     *   ),
     * @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function getPavilion(Request $request)
    {
        $uid = $request->input('uid', 0);
        $lng = $request->input('lng', '116.32298699999993');
        $lat = $request->input('lat', '39.98342407140365');

        $ConfPavilion = self::getPavilionBase($uid,$lng,$lat);

        $tmp = array();
        $tmp['id'] = strval($ConfPavilion['id']);
        $tmp['province'] = $ConfPavilion['province'];
        $tmp['name'] = $ConfPavilion['name'];
        $tmp['cover'] = $ConfPavilion['cover'] != '' ? env('IMAGE_DISPLAY_DOMAIN').$ConfPavilion['cover'] : '';
        $tmp['newCover'] = $ConfPavilion['new_cover'] != '' ? env('IMAGE_DISPLAY_DOMAIN').$ConfPavilion['new_cover'] : '';
        $tmp['background'] = $ConfPavilion['background'] != '' ? env('IMAGE_DISPLAY_DOMAIN').$ConfPavilion['background'] : '';
        $tmp['description'] = $ConfPavilion['description'];

        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => $tmp);
        return response()->json($result);
    }


    /**
     * @SWG\Get(path="/v1/user/pavilion_list",
     *   tags={"user"},
     *   summary="获取当前所属馆列表",
     *   description="",
     *   operationId="user",
     *   produces={"application/json"},
     *   @SWG\Parameter(
     *     name="uid",
     *     in="query",
     *     description="用户id",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="string")
     *   ),
     *   @SWG\Parameter(
     *     name="lng",
     *     in="query",
     *     description="经度",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="string")
     *   ),
     *  @SWG\Parameter(
     *     name="lat",
     *     in="query",
     *     description="纬度",
     *     required=false,
     *     type="integer",
     *     @SWG\Schema(ref="string")
     *   ),
     * @SWG\Response(response=200,description="successful operation"),
     * )
     */
    public function getPavilionList(Request $request)
    {
        $result = array();

        $uid = $request->input('uid', 0);
        $lng = $request->input('lng', '116.32298699999993');
        $lat = $request->input('lat', '39.98342407140365');

        $ConfPavilion = self::getPavilionBase($uid,$lng,$lat);

        $tmp = array();
        $tmp['id'] = strval($ConfPavilion['id']);
        $tmp['name'] = $ConfPavilion['name'];
        $tmp['cover'] = $ConfPavilion['new_cover'] != '' ? env('IMAGE_DISPLAY_DOMAIN').$ConfPavilion['new_cover'] : '';
        $tmp['newCover'] = $ConfPavilion['new_cover'] != '' ? env('IMAGE_DISPLAY_DOMAIN').$ConfPavilion['new_cover'] : '';
        $tmp['background'] = $ConfPavilion['background'] != '' ? env('IMAGE_DISPLAY_DOMAIN').$ConfPavilion['background'] : '';
        $tmp['description'] = $ConfPavilion['description'];

        $result['province'] = $ConfPavilion['province'];
        $result['currentPavilion'] = $tmp;
        $result['otherPavilion']  = array();

        $ConfPavilion = ConfPavilion::whereState(ConfPavilion::state_online)->orderBy('display_order','desc')->get();
        foreach($ConfPavilion as $pavilion){
            if($pavilion['id'] != $result['currentPavilion']['id']){
                $tmp = array();
                $tmp['id'] = strval($pavilion['id']);
                $tmp['name'] = $pavilion['name'];
                $tmp['cover'] = $pavilion['new_cover'] != '' ? env('IMAGE_DISPLAY_DOMAIN').$pavilion['new_cover'] : '';
                $tmp['newCover'] = $pavilion['new_cover'] != '' ? env('IMAGE_DISPLAY_DOMAIN').$pavilion['new_cover'] : '';
                $tmp['background'] = $pavilion['background'] != '' ? env('IMAGE_DISPLAY_DOMAIN').$pavilion['background'] : '';
                $tmp['description'] = $pavilion['description'];
                $result['otherPavilion'][] = $tmp;
            }
        }


        $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => $result);
        return response()->json($result);
    }


    private function getPavilionBase($uid,$lng,$lat){
        $cityName = self::getCityByLngLat($lng,$lat);
        $city = ConfCity::whereName($cityName)->first();
        $ConfPavilionCity = ConfPavilionCity::whereCityId($city['id'])->first();
        if(!is_null($ConfPavilionCity)){
            $ConfPavilion = ConfPavilion::whereId($ConfPavilionCity['pavilion_id'])->first();
            UserBase::whereId($uid)->update(array('lng'=>$lng,'lat'=>$lng));
            $ConfPavilion['province'] = $cityName;
        }else{
            $pavilionName = '乡亲直供馆';
            $ConfPavilion = ConfPavilion::whereName($pavilionName)->first();
            $ConfPavilion['province'] = '无法定位';
        }
        return $ConfPavilion;
    }



    private function getCityByLngLat($lng,$lat){

        $url = 'http://api.map.baidu.com/geocoder/v2/?location='.$lat.','.$lng.'&output=json&pois=1&ak=h3etG2rzAhgQ7kwsayVh2np1ZmES5I0v';

        $data = json_decode(file_get_contents($url),true);

        //Log::alert('baidu map'.print_r($data,true));

        if(isset($data['result']['addressComponent']['province'])){
           return str_replace('市','',$data['result']['addressComponent']['province']);
        }
        return '';
    }

}

