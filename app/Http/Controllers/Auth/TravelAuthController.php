<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\GenController;
use App\Models\StoreBase;
use App\Models\StoreUser;
use App\Models\TaBase;
use App\Models\TaSm;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Validator;
use Session;

class TravelAuthController extends GenController
{
    public function getLogin()
    {
        if (Session::get(TaBase::SESSION_TA)) {
            return Redirect::to('/');
        }

        return view('auth.travel_login');
    }

    public function getRegister(Request $request)
    {
        $invite_code = $request->input('code','');
        return view('auth.travel_register',compact('invite_code'));
    }

    public function postLogin(Request $request)
    {

        $account = $request->input('account');
        $password = $request->input('password');
        $user = TaBase::whereMobile($account)->first();
        //dd($user);
        if (empty($user)) {
            $result = $this->getReturnResult('no','无该用户');
            return response()->json($result);
        }
        $password = $this->passwdEncode($password, $user->salt);
        if ($password != $user->password) {
            $result = $this->getReturnResult('no', '密码错误');
            return response()->json($result);
        }
        //对所有的非正常用户进行堵截（即当且仅当state= 1才允许通过）
        if($user->state != 1){
            $result = $this->getReturnResult('no','系统正在升级维护');
            //return response()->json(['ret'=>'no','msg'=>'系统正在升级维护']);
            return response()->json($result);
            //exit();
        }
        
        $s_user = array();
        $s_user['id']            = $user->id;
        $s_user['account']       = $user->mobile;
        $s_user['name']          = TaBase::whereId($user->id)->pluck('ta_name');
        if (mb_strlen($s_user['name']) > 5) {
            $s_user['name'] = mb_substr($s_user['name'], 0, 5).'...';
        }
        Session::put(TaBase::SESSION_TA, $s_user);
        return response()->json(array('ret'=>'yes', 'msg'=>'登录成功'));
    }

    public function getLogout()
    {
        Session::forget(TaBase::SESSION_TA);
        return Redirect::to("/auth/login");
    }

    //发短信
    public function sms(Request $request)
    {
        //用户是否存在
        $user = TaBase::whereMobile($request->mobile)->first();
        if ($request->type == 1){//注册
            if ($user){
                return $this->getReturnResult('no','用户已存在');
            }
        }elseif ($request->type == 2){//找回密码
            if (!$user){
                return $this->getReturnResult('no','手机号码不存在');
            }
        }

        $ret = $this->sendTaSms($request->mobile,ip2long($request->getClientIp()),$request->type);
        $data = json_decode($ret->content());
        if ($data->ret == 0){
            return $this->getReturnResult('yes','短信发送成功');
        }else{
            return $this->getReturnResult('no','短信发送失败');
        }
    }


    public function postRegister(Request $request)
    {
        $mobile = $request->input('mobile','');
        $code = $request->input('code','');
        $password = $request->input('password','');
        $passwords = $request->input('passwords','');
        $invite_code = strtolower($request->input('invite_code',''));
        $type = $request->input('type','');
        if($password != $passwords){
            return response()->json(['ret'=>'no','data'=>'两次密码不一致']);
        }
        if($code == ''){
            return response()->json(['ret'=>'no','data'=>'验证码为空']);
        }

        $User = User::whereInviteCode($invite_code)->first();
        if(is_null($User)){
            return response()->json(['ret'=>'no','data'=>'邀请码不正确,请重新输入']);
        }

        $taBase = new TaBase();
        $user = $taBase->whereMobile($mobile)->first();
        if($user){
            return response()->json(['ret'=>'no','data'=>GenController::MOBILE_EXIST]);
        }
        $sms = $this->codeVerify($mobile,$code,$type);
        if ($sms) {
            $salt = $this->setSalt();
            $password = $this->passwdEncode($password, $salt);
            $taBase->invite_code = $invite_code;
            $taBase->self_invite_code = self::generateInviteCode();
            $taBase->sale_id = intval($User['id']);
            $taBase->mobile = $mobile;
            $taBase->salt = $salt;
            $taBase->password = $password;
            $ret = $taBase->save();
            if ($ret == 1) {
                $s_user = array();
                $s_user['id'] = $taBase->id;
                $s_user['account'] = $mobile;
                $s_user['name'] = TaBase::whereId($taBase->id)->pluck('ta_name');
                if (mb_strlen($s_user['name']) > 5) {
                    $s_user['name'] = mb_substr($s_user['name'], 0, 5) . '...';
                }
                Session::put(TaBase::SESSION_TA, $s_user);
                TaSm::whereCode($request->mobile_code)->whereMobile($request->mobile)->update(['is_valid'=>-1]);
                return response()->json(['ret' => 'yes', 'data' => '注册成功']);
            }
            return response()->json(['ret' => 'no', 'data' => '注册失败']);
            $taSm = TaSm::whereMobile($mobile)->whereCode($code)->whereIsValid(0)->first();
        }
        return response()->json(['ret' => 'no', 'data' => '验证码已失效']);


    }


    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'account' => 'required|max:255|unique:store_user',
            'password' => 'required|confirmed|min:6',
        ]);
    }
    protected function getForget(){
        return view('auth.travel_forget');
    }

    protected function postForget(Request $request){
        if(!$request->mobile){
            return $this->getReturnResult('no','手机号不能为空');
        }
        if(!$request->password || !$request->password_confirm){
            return $this->getReturnResult('no','密码不能为空');
        }
        if($request->password != $request->password_confirm){
            return $this->getReturnResult('no','密码不一致');
        }

        $user = TaBase::whereMobile($request->mobile)->first();
        if (!$user){
            return $this->getReturnResult('no','手机号码不存在');
        }
        $flag = $this->codeVerify($request->mobile,$request->mobile_code,$request->type);
        if (!$flag){
            return $this->getReturnResult('no','验证码错误');
        }
        //修改密码，修改验证码状态
        TaSm::whereCode($request->mobile_code)->whereMobile($request->mobile)->whereType($request->type)->update(['is_valid'=>-1]);
        $user->password = $this->passwdEncode($request->password,$user->salt);
        $user->save();
        return $this->getReturnResult('yes','密码修改成功');
    }

    /* 旅行社 发送验证码 *
     * @param $mobile
     * @param $ip
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    protected function sendTaSms($mobile, $ip, $type)
    {
        $today = strtotime(date('Y-m-d'));
        $sms_num = TaSm::whereMobile($mobile)->whereType($type)->where('created_at', '>', $today)->count();
        if ($sms_num < 100) {
            $code = rand(100000, 999999);
            $tpl_value = "【易游购】您的验证码是" . $code . "。如非本人操作，请忽略本短信";
            $sms_result = json_decode(self::sendSms($tpl_value, $mobile), true);
            $sms_result['msg'] = urlencode($sms_result['msg']);
            if(isset($sms_result['detail'])){
                $sms_result['detail'] = urlencode($sms_result['detail']);
            }
            $TaSms = new TaSm();
            $TaSms->is_valid = -1;
            if ($sms_result['code'] == 0){
                $TaSms->whereMobile($mobile)->whereType($type)->update(['is_valid'=>-1]);
                $TaSms->is_valid = 0;
            }
            $TaSms->type = $type;
            $TaSms->mobile = $mobile;
            $TaSms->code = $code;
            $TaSms->ip = $ip;
            $ret = $TaSms->save();
            if ($ret == 1) {
                Log::alert('旅行社SMS发送返回数据:' . print_r($sms_result, true));
                $TaSms->whereId($TaSms->id)->update(array('sid' => urldecode(json_encode($sms_result))));
            }
            $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => array('mobile' => $mobile, 'recode' => strval($TaSms->code)));
            return response()->json($result);
        }
        $result = array('ret' => self::RET_FAIL, 'msg' => self::OVER_REQUEST_NUM, 'data' => (object)array());
        return response()->json($result);
    }
    // 旅行社 验证短信验证码
    protected function codeVerify($mobile,$code,$type){
        $taSm = TaSm::whereMobile($mobile)->whereType($type)->whereCode($code)->whereIsValid(0)->first();
        if (!$taSm){
            return false;
        }
        return true;
    }
}
