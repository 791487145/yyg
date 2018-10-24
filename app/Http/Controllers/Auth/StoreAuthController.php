<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\GenController;
use App\Models\SmsVerificationCode;
use App\Models\StoreBase;
use App\Models\StoreUser;
use App\Models\SupplierBase;
use App\Models\SupplierSm;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Validator;
use Session;

class StoreAuthController extends GenController
{
    public function getLogin()
    {
        if (Session::get(SupplierBase::SESSION_SUPPLIER)) {
            return Redirect::to('/');
        }
        return view('auth.store_login');
    }


    public function postLogin(Request $request)
    {
        $account = $request->input('account');
        $password = $request->input('password');
        $user = SupplierBase::whereMobile($account)->whereState(SupplierBase::STATE_VALID)->first();
        if (empty($user)) {
            $result = $this->getReturnResult('no','无该用户');
            return response()->json($result);
        }
        $password = $this->passwdEncode($password, $user->salt);

        if ($password != $user->password) {
            $result = $this->getReturnResult('no', '密码错误');
            return response()->json($result);
        }

        $s_user = array();
        $s_user['method'] = "";
        $s_user['id']            = $user->id;
        $s_user['account']       = $user->account;
        $s_user['store_base_id'] = $user->id;
        $s_user['store_name']    = SupplierBase::whereId($user->id)->pluck('name');
         $s_user['name']          = $user->name ? $user->name : $user->account;
        if (mb_strlen($s_user['name']) > 3) {
            $s_user['name'] = mb_substr($s_user['name'], 0, 3).'...';
        }
        Session::put(SupplierBase::SESSION_SUPPLIER, $s_user);

        return response()->json(array('ret'=>'yes', 'msg'=>'登录成功'));
    }

    public function getLogout()
    {
        Session::forget(SupplierBase::SESSION_SUPPLIER);

        return Redirect::to("/auth/login");
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
        return view('auth.store_forget');
    }

    //发短信
    public function sms(Request $request)
    {
        //用户是否存在
        $user = SupplierBase::whereMobile($request->mobile)->first();
        if (!$user){
            return $this->getReturnResult('no','手机号码不存在');
        }
        $ret = $this->sendTaSms($request->mobile,ip2long($request->getClientIp()),$request->type);
        $data = json_decode($ret->content());
        if ($data->ret == 0){
            return $this->getReturnResult('yes','短信发送成功');
        }else{
            return $this->getReturnResult('no','短信发送失败');
        }
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

        $user = SupplierBase::whereMobile($request->mobile)->first();
        if (!$user){
            return $this->getReturnResult('no','手机号码不存在');
        }
        $flag = $this->codeVerify($request->mobile,$request->mobile_code,$request->type);
        if (!$flag){
            return $this->getReturnResult('no','验证码错误');
        }
        //修改密码，修改验证码状态
        SupplierSm::whereCode($request->mobile_code)->whereMobile($request->mobile)->update(['is_valid'=>-1]);
        $user->password = $this->passwdEncode($request->password,$user->salt);
        $user->save();
        return $this->getReturnResult('yes','密码修改成功');
    }

    /* 供应商 发送验证码 *
     * @param $mobile
     * @param $ip
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    protected function sendTaSms($mobile, $ip, $type)
    {
        $today = strtotime(date('Y-m-d'));
        $sms_num = SupplierSm::whereMobile($mobile)->whereType($type)->where('created_at', '>', $today)->count();
        if ($sms_num < 100) {
            $code = rand(100000, 999999);
            $tpl_value = "【易游购】您的验证码是" . $code . "。如非本人操作，请忽略本短信";
            $sms_result = json_decode(self::sendSms($tpl_value, $mobile), true);
            $sms_result['msg'] = urlencode($sms_result['msg']);
            if(isset($sms_result['detail'])){
                $sms_result['detail'] = urlencode($sms_result['detail']);
            }
            $supplierSms = new SupplierSm();
            $supplierSms->is_valid = -1;
            if ($sms_result['code'] == 0){
                $supplierSms->whereMobile($mobile)->whereType($type)->update(['is_valid'=>-1]);
                $supplierSms->is_valid = 0;
            }
            $supplierSms->type = $type;
            $supplierSms->mobile = $mobile;
            $supplierSms->code = $code;
            $supplierSms->ip = $ip;
            $ret = $supplierSms->save();
            if ($ret == 1) {
                Log::alert('供应商SMS发送返回数据:' . print_r($sms_result, true));
                $supplierSms->whereId($supplierSms->id)->update(array('sid' => urldecode(json_encode($sms_result))));
            }
            $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => array('mobile' => $mobile, 'recode' => strval($supplierSms->code)));
            return response()->json($result);
        }
        $result = array('ret' => self::RET_FAIL, 'msg' => self::OVER_REQUEST_NUM, 'data' => (object)array());
        return response()->json($result);
    }
    // 旅行社 验证短信验证码
    protected function codeVerify($mobile,$code,$type){
        $supplierSm = SupplierSm::whereMobile($mobile)->whereType($type)->whereCode($code)->whereIsValid(0)->first();
        if (!$supplierSm){
            return false;
        }
        return true;
    }
}
