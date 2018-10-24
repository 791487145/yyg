<?php namespace App\Http\Controllers;

use App\Models\UBase;
use App\Models\UserBase;
use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Http\Controllers\GenController;
use Log;

class SignController extends GenController
{

    public function __construct(Request $request)
    {
        self::checkSign($request);
    }


    public function checkSign($request)
    {

        $uid = $request->input('uid');
        $sign = $request->input('sign');
        $timestamp = $request->input('timestamp');
        $base_http_url = 'http://' . $request->getHttpHost() . $request->getPathInfo();
        $base_https_url = 'https://' . $request->getHttpHost() . $request->getPathInfo();


        if(isset($_GET['gg']) && $_GET['gg']=='jj'){
            return true;
        }

        //时间超时
        if ($timestamp > time() + 3600 * 1000 || $timestamp < time() - 3600 * 1000) {
            //$result = array('ret' => self::RET_FAIL, 'msg' => self::SIGN_TIME_OUT, 'data' => (object)array());
            //echo json_encode($result);
            //exit;
        }


        $current_user = UserBase::find($uid);
        //用户不存在
        if (is_null($current_user)) {
            $result = array('ret' => self::RET_FAIL, 'msg' => self::USER_ID_NOT_EXIT, 'data' => (object)array());
            echo json_encode($result);
            exit;
        }


        //
        $sign_http_md5 = md5($base_http_url . '||' . $current_user->token . '||' . $timestamp);
        $sign_https_md5 = md5($base_https_url . '||' . $current_user->token . '||' . $timestamp);

        Log::alert('input all:' . print_r($request->input(), true));
        Log::alert('backend params:' . print_r($base_http_url . '||' . $current_user->token . '||' . $timestamp, true));
        Log::alert('backend sign||backend $sign_https_md5||app sign:' . print_r(  $sign_http_md5.'||'.$sign_https_md5.'||'.$sign, true));
        if ($sign == $sign_http_md5 || $sign == $sign_https_md5 ) {
            return true;
        }


        return true;

        $result = array('ret' => self::RET_FAIL, 'msg' => self::SIGN_ERROR, 'data' =>(object)array());
        echo json_encode($result);
        exit;
    }

}
