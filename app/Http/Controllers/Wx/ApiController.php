<?php namespace App\Http\Controllers\Wx;

use App\Http\Controllers\GenController;
use App\Models\GuideBase;
use App\Models\TaGroup;
use App\Models\UserWx;
use App\Models\WxGuide;
use App\Models\WxReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use zgldh\QiniuStorage\QiniuStorage;

use Log;
use Lang;

class ApiController extends GenController
{
    const TOKEN='1qazYYGxsw2';

    public function index(Request $request)
    {
        Log::alert('wechat request '. print_r($request->all(),true));
        $echo_str = $request->input('echostr');
        if(!empty($echo_str)){
            self::checkSignature($request);
            echo $echo_str;
            exit;
        }else{
            self::responseMsg();
            exit;
        }
    }

    public function responseMsg(){
        $postStr = file_get_contents("php://input");

        if (!empty($postStr)){
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            Log::alert('$postObj '. print_r($postObj,true));
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $time = date("Y-m-d H:i:s");
            /*
            $keyword = trim($postObj->Content);

            if(strlen($keyword) > 0){

            }
            */

            if($postObj->Event == "subscribe") {

                $guideArray = explode('_', $postObj->EventKey);

                $UserWx = UserWx::whereOpenId($postObj->FromUserName)->first();
                if(is_null($UserWx)){
                    $UserWx = new UserWx();
                }
                $UserWx->open_id = $postObj->FromUserName;

                if(isset($guideArray[1]) && $guideArray[1] > 0){
                    $UserWx->guide_id = $guideArray[1];
                    $TaGroup = TaGroup::whereGuideId($UserWx->guide_id)->whereState(TaGroup::STATE_START)->first();
                    if(!is_null($TaGroup)){
                        $UserWx->ta_id = $TaGroup['ta_id'];
                    }else{
                        $GuideBase = GuideBase::whereId($UserWx->guide_id)->first();
                        $UserWx->ta_id = $GuideBase['ta_id'];
                    }
                    $UserWx->created_at = date('Y-m-d H:i:s');
                }

                $UserWx->subscribe = 1;
                $UserWx->ref = 'wx_qrcode';
                $UserWx->save();

                $WxGuide = WxGuide::whereOpenId($postObj->FromUserName)->first();
                if(is_null($WxGuide)){
                    $WxGuide = new WxGuide();
                }
                $WxGuide->open_id = $postObj->FromUserName;
                if(isset($guideArray[1]) && $guideArray[1] > 0){
                    $WxGuide->guide_id = $guideArray[1];
                }
                $WxGuide->ref = 'wx_qrcode';
                $WxGuide->state = WxGuide::STATE_YES;
                $WxGuide->save();

                $url = 'http://'.env('H5_DOMAIN').'/couponGiven?open_id='.$WxGuide->open_id;
                $content = "欢迎关注易游购，点击下方链接领取优惠券～\n$url";
                self::textNew($fromUsername, $toUsername,$content,$time);
                exit;

            }

            if($postObj->MsgType == "text"){
                $WxReply = WxReply::whereState(WxReply::StateNormal)->whereKeyWord($postObj->Content)->first();
                if(!is_null($WxReply)){
                    self::replayPicture($fromUsername, $toUsername,$time,$WxReply);
                    exit;
                }
                exit;
            }


            if($postObj->Event == "unsubscribe"){
                UserWx::whereOpenId($postObj->FromUserName)->update(array('subscribe'=>0));
                exit;
            }

        }else {
            echo "";
            exit;
        }


    }



    public function article($fromUsername, $toUsername,$item_num, $items, $time){
        $itemTpl = "<item>
                        <Title><![CDATA[%s]]></Title>
                        <Description><![CDATA[%s]]></Description>
                        <PicUrl><![CDATA[%s]]></PicUrl>
                        <Url><![CDATA[%s]]></Url>
                    </item>
                    ";

        $itemXml = '';
        foreach($items as $item){
            $itemXml .=  sprintf($itemTpl, $item['title'], $item['desc'], $item['picurl'], $item['url']);
        }
        $textTpl = "
                    <xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[%s]]></MsgType>
                        <ArticleCount>%s</ArticleCount>
                        <Articles>%s</Articles>
                    </xml>
                    ";
        $msgType = "news";
        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType,$item_num,$itemXml);
        echo $resultStr;
    }

    private function textNew($fromUsername, $toUsername,$content, $time){
        $textTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[%s]]></MsgType>
                        <Content><![CDATA[%s]]></Content>
                    </xml>
                    ";
        $mesType = 'text';
        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time,$mesType,$content);
        echo $resultStr;
    }


    private function checkSignature($request)
    {
        $signature = $request->input('signature','');
        $timestamp = $request->input('timestamp','');
        $nonce = $request->input('nonce','');

        $token = self::TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
        Log::alert('$token, $timestamp, $nonce '.$token.'||'.$timestamp.'||'.$nonce);
        Log::alert('$tmpStr '.$tmpStr);
        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }

    private function replayPicture($fromUsername, $toUsername, $time,$wxReply)
    {
         $xml = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[%s]]></MsgType>
                    <Image>
                    <MediaId><![CDATA[%s]]></MediaId>
                    </Image>
                </xml>>";
         //$mesType = 'image';
         $resultStr = sprintf($xml, $fromUsername, $toUsername, $time,$wxReply->type,$wxReply->media_id);

         echo $resultStr;
    }

}