<?php

namespace App\Http\Controllers\Admin;

use Log;
use CURLFile;
use App\Models\WxReply;
use Illuminate\Http\Request;
use App\Http\Controllers\Wx\WxLocationController;
use App\Http\Controllers\Admin\BaseController;

class WxController extends BaseController
{
    public function replyPic(Request $request,$category = 1)
    {
        $tmp['keyword'] = $request->input('key_word','');
        $WxReplys = new WxReply();
        if(!is_null($tmp['keyword'])){
            $WxReplys = $WxReplys->where('key_word', 'like', '%' . $tmp['keyword'] . '%');
        }
        $WxReplys = $WxReplys->whereIn('state',[WxReply::StateNormal,WxReply::StateStop])->whereCategory($category)->paginate($this->page);
        $access_token = WxLocationController::accessToken();
        foreach($WxReplys as $wxReply){
            $time = time();
            $num = floor(($time - $wxReply->create_time)/86400);
            if($num >= 3){
                $wxReply->state = 0;
            }
            $wxReply->img = "https://api.weixin.qq.com/cgi-bin/media/get?access_token=".$access_token."&media_id=".$wxReply->media_id;
            $wxReply->state = WxReply::getStateDescription($wxReply->state);
        }
        return view('boss.wx.wx_list',compact('WxReplys','tmp'));
    }

    public function replyPicAdd(Request $request)
    {
        if($request->isMethod('post')){
            $data = $request->all();
            $keyword = $request->input('keyword');
            $count = WxReply::whereKeyWord($keyword)->whereState(WxReply::StateNormal)->count();
            if($count != 0){
                return response()->json(['ret'=>'no']);
            }
            $create_time = $request->input('create_time');
            $media_id  = $request->input('media_id');
            $type = $request->input('type');
            $remark = $request->input('remark');

            $wxReply = new WxReply();
            $wxReply->create_time = $create_time;
            $wxReply->media_id = $media_id;
            $wxReply->type = $type;
            $wxReply->key_word = $keyword;
            $wxReply->remark = $remark;
            $wxReply->save();
            return response()->json(['ret'=>'yes']);
        }

        return view('boss.wx.wx_add');
    }

    public function replyPicDel($id)
    {
        WxReply::whereId($id)->delete();
        return $id;
    }

    public function replyUpload()
    {
        $dir = public_path().'/uploads';
        $filename = $_FILES['file']['tmp_name'];
        $destination = $dir.'/'.$_FILES['file']['name'];
        move_uploaded_file($filename,$destination);
        $path = realpath($destination);
        $data = new CURLFile($path);
        $data = ['media' => $data];
        $access_token = WxLocationController::accessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/media/upload?access_token=".$access_token."&type=image";
        $ret =json_decode(WxLocationController::postCurl($url,$data));
        $img = "https://api.weixin.qq.com/cgi-bin/media/get?access_token=".$access_token."&media_id=".$ret->media_id;
        unlink($destination);
        Log::alert('$ret:' . print_r($ret, true));
        return response()->json(['remark'=>$_FILES['file']['name'],'type'=>$ret->type,'create_time'=>$ret->created_at,'media_id'=>$ret->media_id,'img'=>$img]);
    }

    public function replyPicUpdate(Request $request,$id)
    {
        $WxReply = WxReply::whereId($id)->first();
        if($request->isMethod('post')){
            $data = $request->all();
            $count = WxReply::whereState(WxReply::StateNormal)->whereKeyWord($data['keyword'])->count();
            //dd($count);
            if(($data['keyword'] != $WxReply->key_word) && $data['state'] == 1 && $count >= 1){
                return response()->json(['ret'=>'no','msg'=>"关键字重复"]);
            }
            if(($data['keyword'] == $WxReply->key_word) && $data['state'] == 1 && $WxReply->state == 0 && $count >= 1){
                return response()->json(['ret'=>'no','msg'=>"关键字重复"]);
            }

            $time = time();
            $num = floor(($time - $data['create_time'])/86400);
            if($num >= 3){
                return response()->json(['ret'=>'no','msg'=>"该图片超过3天，请重新添加"]);
            }

            $WxReply->state = $data['state'];
            $WxReply->type = $data['type'];
            $WxReply->remark = $data['remark'];
            $WxReply->media_id = $data['media_id'];
            $WxReply->key_word = $data['keyword'];
            $WxReply->create_time = $data['create_time'];
            $WxReply->save();
            return response()->json(['ret'=>'yes']);
        }
        $access_token = WxLocationController::accessToken();

        $WxReply->img = "https://api.weixin.qq.com/cgi-bin/media/get?access_token=".$access_token."&media_id=".$WxReply->media_id;
        return view('boss.wx.wx_edit',compact('WxReply'));
    }


}
