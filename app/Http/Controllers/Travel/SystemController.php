<?php

namespace App\Http\Controllers\Travel;

use App\Models\ConfCity;
use App\Models\TaBase;
use App\Models\TaSm;
use Illuminate\Http\Request;
use Qiniu\Auth;
use App\Http\Requests;
use App\Http\Controllers\Travel\TravelController;
use Illuminate\Support\Facades\Input;
use Qiniu\Storage\UploadManager;

class SystemController extends TravelController
{
    function set(){
        $user = TaBase::whereId($this->user['id'])->first();
        $citys = ConfCity::whereParentId($user->ta_province_id)->get();
        if ($user->ta_province_id < 1){
            $citys = [];
        }
        $provinces = ConfCity::whereParentId(1)->get();
        return view('travel.system.set')->with(['user'=>$user,'provices'=>$provinces,'citys'=>$citys]);
    }
    function setting(){
       $data = \Input::all();
       TaBase::whereId($this->user['id'])->update($data);
       return $this->getReturnResult('yes','修改成功');
    }

    function upload(){
        $file = $this->uploadToQiniu();
        if($file){
            return $this->getReturnResult('yes',$file);
        }else{
            return $this->getReturnResult('no','上传失败');
        }
    }
    public function uploads(Request $request)
    {
        $uploadMgr = new UploadManager();
        $auth = new Auth(env('IMAGE_ACCESS_KEY'), env('IMAGE_SECRET_KEY'));
        $bucket = env('IMAGE_BUCKET');
        $upToken = $auth->uploadToken($bucket);//获取上传所需的token
        $filePath = $request->imgOne;
        $key = 'tr'.substr(md5_file($filePath), 10) . '.png';
        list($ret, $err) = $uploadMgr->putFile($upToken, $key, $filePath);
        if ($err !== null) {
        } else {
            $picName = $key;
            return $picName;
        }
    }

    function authentication(){
        return view('travel.system.authentication');
    }
    function postAuthentication(){
        $data = Input::all();
        if (!$data['opt_name']){
            return $this->getReturnResult('no','请输入姓名');
        }
        if (!$data['opt_id_card']){
            return $this->getReturnResult('no','请输入身份证号码');
        }
        if (!$data['opt_photo_1']){
            return $this->getReturnResult('no','请上传身份证');
        }
        if (!$data['opt_photo_2']){
            return $this->getReturnResult('no','请上传身份证');
        }
        $data['withdraw_name'] = $data['opt_name'];
        $data['state'] = 2;
        TaBase::whereId($this->user['id'])->update($data);
        return $this->getReturnResult('yes','');
    }
    function password(){
        $user = TaBase::whereId($this->user['id'])->first();
        return view('travel.system.password')->with(['user'=>$user]);
    }

    function authenticate(){
        $user = TaBase::whereId($this->user['id'])->first();
        return view('travel.system.authenticate')->with(['user'=>$user]);
    }

    function postPassword(Request $request){
        $password = Input::get('password');
        $password_confirm = Input::get('password_confirm');
        if (!$password || !$password_confirm){
            return $this->getReturnResult('no','密码不能为空');
        }
        if($password != $password_confirm){
            return $this->getReturnResult('no','两次密码不一致');
        }
        $user = TaBase::whereId($this->user['id'])->first();
        $flag = $this->checkTravelCode($user->mobile,$request->type,$request->mobile_code);
        if (!$flag){
            return $this->getReturnResult('no','验证码错误');
        }
        //修改验证码状态
        TaSm::whereCode($request->mobile_code)->whereMobile($user->mobile)->whereType($request->type)->update(['is_valid'=>-1]);
        $user->password = $this->passwdEncode($password,$user->salt);
        $user->save();
        TaSm::whereCode(Input::get('mobile_code'))->whereMobile($user->mobile)->update(['is_valid'=>-1]);
        return $this->getReturnResult('yes','密码修改成功');
    }

    function getCity($province){
        if ($province > 0){
            return ['ret'=>'yes','data'=>ConfCity::whereParentId($province)->get()];
        }
    }
}
