<?php

namespace App\Http\Controllers\Wx;

use App\Models\ConfCity;
use App\Models\ConfPavilionCity;
use App\Models\ConfWx;
use App\Models\GoodsSpec;
use App\Models\UserCart;
use App\Models\WxReply;
use Cookie;
use App\Http\Requests;
use App\Models\GoodsBase;
use App\Models\ConfTheme;
use App\Models\ConfBanner;
use App\Models\ConfPavilion;
use Log;
use zgldh\QiniuStorage\QiniuStorage;
use CURLFile;
use EasyWeChat\Foundation\Application;

class WxLocationController extends WxController
{
    public static $OK = 0;
    public static $IllegalAesKey = -41001;
    public static $IllegalIv = -41002;
    public static $IllegalBuffer = -41003;
    public static $DecodeBase64Error = -41004;
    public static $block_size = 16;

    public function index()
    {
        //$id = 24;
        //$ret = self::responseMsg();
        //$param = self::shortUrl("http://www.baidu.com");
        //$param = '1500279619';
        //$param = time() - 3600;
        //dd( date('Y-m-d H:i:s',$param));
        //dd($param);
        //$signPackage = $this->signature();
        $a = self::paramToDecrypData();

        return view("wx.location");
    }

    //"{"type":"image","media_id":"I9alXibsZx6Na_EkDbQHoRCH9jOv2kjW4MScB92tdR6vZHntzvVcQX-Ejj5FpGsH","created_at":1501072839}"
    static function responseMsg()
    {
        $access_token = self::accessToken();
        $media_id = "I9alXibsZx6Na_EkDbQHoRCH9jOv2kjW4MScB92tdR6vZHntzvVcQX-Ejj5FpGsH";
        $url = "https://api.weixin.qq.com/cgi-bin/media/get?access_token=".$access_token."&media_id=".$media_id;
        //$ret = self::curlInt($url);
        echo "<img src = ".$url.">";
    }

    public function mediaPic()
    {
        $dir = public_path().'/images';
        //dd($_FILES);
        $filename = $_FILES['Filedata']['tmp_name'];
        $destination = $dir.'/'.$_FILES['Filedata']['name'];
        //echo $destination;
        move_uploaded_file($filename,$destination);
        $path = realpath($destination);
        $data = new CURLFile($path);
        $data = ['media' => $data];
        $access_token = self::accessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/media/upload?access_token=".$access_token."&type=image";
        $ret = json_decode(self::postCurl($url,$data));
        $wxReply = new WxReply();
        $wxReply->create_time = $ret->created_at;
        $wxReply->media_id = $ret->media_id;
        $wxReply->type = $ret->type;
        $wxReply->save();
        unlink($destination);
        dd($ret);

    }

    //长连接转短链接
    static function shortUrl($longUrl)
    {
        $access_token = self::accessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/shorturl?access_token=".$access_token;
        $data = '{"action":"long2short","long_url":"'.$longUrl.'"}';
        $res = json_decode(self::postCurl($url,$data));
        //dd($res);
        $short_url = $res->short_url;
        return $short_url;
    }

    //微信二位码
    static function wxQrCode($gid)
    {
        $access_token = self::accessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$access_token;
        $data = '{"expire_seconds": 604800, "action_name": "QR_SCENE", "action_info": {"scene": {"gid": '.$gid.'}}}';
        $res = json_decode(self::postCurl($url,$data));
        $ticket = urlencode($res->ticket);
        $qrUrl = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$ticket;
        echo "<img src = ".$qrUrl.">";
    }
    /*static function shortUrl($longUrl)
    {
        $access_token = self::accessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/shorturl?access_token=".$access_token;
        $data = '{"action":"long2short","long_url":'.$longUrl.'}';
        $res = json_decode(self::postCurl($url,$data));
        dd($res);
        $short_url = $res->short_url;
        return $short_url;
    }*/

    static public function signature()
    {
        $jsapiTicket = self::getJsApiTicket();
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $timestamp = time();
        $nonceStr = self::nonceStr();
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
        $signature = sha1($string);

        $signPackage = array(
            "appId"     => env("WX_APPID"),
            "nonceStr"  => $nonceStr,
            "timestamp" => $timestamp,
            "url"       => $url,
            "signature" => $signature,
            "rawString" => $string
        );
        return $signPackage;
    }

    //accessToken
    static function accessToken()
    {
        $param = ConfWx::first();
        $data = json_decode($param->access_token);
        if($data->expire_time < time()){
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".env('WX_APPID')."&secret=".env('WX_APPSECRET');
            $res = json_decode(self::curlInt($url));
            $data->access_token = $res->access_token;
            //dd($data->access_token);
            $data->expire_time = time() + 3600;
            $access_token = $data->access_token;
            $data = json_encode($data);
            ConfWx::whereId($param->id)->update(['access_token'=>$data]);
        }else{
            $access_token = $data->access_token;
        }

        return $access_token;
    }

    //get请求
    static function curlInt($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_URL, $url);

        $res = curl_exec($curl);
        curl_close($curl);
        return $res;
    }

    //post请求
    static function postCurl($url,$date)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $date);

        $res = curl_exec($curl);
        curl_close($curl);
        return $res;
    }

    //获取签名
    static private function getJsApiTicket()
    {
        $param = ConfWx::first();
        $data = json_decode($param->jsapiticket);
        $accessToken = self::accessToken();
        if($data->expire_time < time()){
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=".$accessToken."&type=jsapi";
            $res = json_decode(self::curlInt($url));

            $data->jsapiticket = $res->ticket;
            $data->expire_time = time() + 3600;
            $JsApiTicket = $data->jsapiticket;
            $data = json_encode($data);
            ConfWx::whereId($param->id)->update(['jsapiticket'=>$data]);
        }else{
            $JsApiTicket = $data->jsapiticket;
        }
        return $JsApiTicket;
    }

    static private function nonceStr($length = 16)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    static function getSessionKeyUrl($appid,$secret,$js_code,$grant_type)
    {
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=".$appid."&secret=".$secret."&js_code=".$js_code."&grant_type=".$grant_type;
        //Log::alert('$_FILE数据:' . print_r($url, true));
        $result = self::curlInt($url);
        return $result;
    }

    //加密算法
    public function paramToDecrypData()
    {
        $appid = 'wx918363e91a8ca3e9';
        $sessionKey = 'cPlH7U52ps8AQckxChbkUA==';

        $encryptedData="3bj5al/hoM6708PydW7ESNTSDXhSwH7+FTU8aeOj4+Virv242oH0pl8GbLc5Y5yWBdgEYD9yopG2wJUmxYEzuqlNJs73YMOHU3JblHMxUwIzbRBZ0HxeIFuiBRuZSvq0/lF3VS1eDboA/q/HnKyywgOwMqa5OFzW1CJDCvqIro2GHoq/IGTiEyMP099xU06DMMqRiTv3ww9pwN5HAyKZQ4P/zxVlDNp/ZAX9jQgZjLhAUQArHyqfqk8Y2RfRUtnhhkH8a/dIsStSUM5INb+wERl0leBTHOVBUjQX/TzNZ8APdZMAVFjyr6EdqEWzyKbOtizVKTmSTHQQ9Sgeqd+aYW37Kpuaf68MT8kBtY4mSLRgsGXeKrQLwLOs+qpyyURGJSWduyx0IvwiYXCntpwUxzDGng96AR2/0m3EgjNpP/RSya6yaV0/aXA4M5pcI4pBA6y2AfZd3P3Ii8px6lHFUCt2MuTehgB1EyTip912hUM=";

        $iv = 'PLUF/1r+w3qQTwtrv02O1w==';
        $a = self::decryptData($appid,$sessionKey,$encryptedData, $iv, $data);
        dd($a);
    }

    static function decryptData($appid,$secret,$encryptedData, $iv, &$data)
    {
        if (strlen($secret) != 24) {
            return self::$IllegalAesKey;
        }
        $aesKey=base64_decode($secret);


        if (strlen($iv) != 24) {
            return self::$IllegalIv;
        }
        $aesIV=base64_decode($iv);

        $aesCipher=base64_decode($encryptedData);

        //$pc = new Prpcrypt($aesKey);
        $result = self::decrypt($aesKey,$aesCipher,$aesIV);

        if ($result[0] != 0) {
            return $result[0];
        }

        $dataObj=json_decode( $result[1] );
        if( $dataObj  == NULL )
        {
            return self::$IllegalBuffer;
        }
        if( $dataObj->watermark->appid != $appid )
        {
            return self::$IllegalBuffer;
        }
        $data = $result[1];
        return $data;//openid,地理位置
    }

    static function decrypt($aesKey, $aesCipher, $aesIV )
    {
        try {
            $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');

            mcrypt_generic_init($module, $aesKey, $aesIV);

            //解密
            $decrypted = mdecrypt_generic($module, $aesCipher);
            mcrypt_generic_deinit($module);
            mcrypt_module_close($module);
        } catch (Exception $e) {
            return array(self::$IllegalBuffer, null);
        }

        try {
            //去除补位字符
            //$pkc_encoder = new PKCS7Encoder;
            $result = self::decode($decrypted);

        } catch (Exception $e) {
            //print $e;
            return array(self::$IllegalBuffer, null);
        }
        return array(0, $result);
    }

    static function encode( $text )
    {
        $block_size = self::$block_size;
        $text_length = strlen( $text );
        //计算需要填充的位数
        $amount_to_pad = self::$block_size - ( $text_length % self::$block_size );
        if ( $amount_to_pad == 0 ) {
            $amount_to_pad = self::$block_size;
        }
        //获得补位所用的字符
        $pad_chr = chr( $amount_to_pad );
        $tmp = "";
        for ( $index = 0; $index < $amount_to_pad; $index++ ) {
            $tmp .= $pad_chr;
        }
        return $text . $tmp;
    }

    static function decode($text)
    {

        $pad = ord(substr($text, -1));
        if ($pad < 1 || $pad > 32) {
            $pad = 0;
        }
        return substr($text, 0, (strlen($text) - $pad));
    }


}
