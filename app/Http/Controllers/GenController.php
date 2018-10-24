<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\CouponController;
use App\Models\ConfCategory;
use App\Models\ConfCity;
use App\Models\ConfPavilion;
use App\Models\CouponBase;
use App\Models\CouponGood;
use App\Models\CouponUser;
use App\Models\GoodsBase;
use App\Models\GoodsSpec;
use App\Models\GuideBase;
use App\Models\GuideBilling;
use App\Models\OrderBase;
use App\Models\OrderGood;
use App\Models\OrderReturn;
use App\Models\PlatformSm;
use App\Models\SmsVerificationCode;
use App\Models\SupplierBase;
use App\Models\TaGroup;
use App\Models\UserBase;
use App\Models\UserWx;
use App\Models\WxGuide;
use Illuminate\Http\Request;
use Log;
use App\Http\Controllers\Controller;
use zgldh\QiniuStorage\QiniuStorage;

class GenController extends Controller
{

    const RET_SUCCESS = '0';
    const RET_FAIL = '-1';
    const RET_USER_FAIL = '-2';
    const RET_SING_ERROR = '-3';

    const OVER_REQUEST_NUM = '超出调用上限';
    const PASSWORD_OR_USER_ERROR = '账号或密码错误';
    const RECODE_ERROR = '验证码错误';
    const REGISTER_ERROR = '注册失败';
    const SIGN_ERROR = '请重新登陆';
    const MOBILE_EXIST = '手机号码已经注册';
    const MOBILE_ERROR = '手机号码有误';
    const MOBILE_NOT_EXIST = '该用户不存在';
    const CODE_ERROR = '验证码不正确';
    const PARAMETER_ERROR = '参数错误';
    const ORDER_STATE_NO_RETURN = '订单状态不能修改';
    const ORDER_GOODS_NUM_ERROR = '商品数量不足';
    const ORDER_GOODS_LIMIT_NUM_OVER = '超出限购数量';
    const ORDER_GOODS_STATE_ERROR = '商品已下架';
    const USER_ID_NOT_EXIT = '请先登陆';
    const SIGN_TIME_OUT = '签名超时';



    protected function passwdEncode($passwd, $salt)
    {
        $keya = md5(substr($passwd, 0, 16));
        $keyb = md5(substr($passwd, 16, 16));
        return md5($keya . $salt . $keyb);
    }

    protected function createToken($account, $user_id)
    {
        return md5($account . $user_id . time());
    }

    protected function setSalt()
    {
        return chr(mt_rand(65,122)).chr(mt_rand(65,122)).chr(mt_rand(65,122)).chr(mt_rand(65,122));
    }
    /**
     * 七牛图片上传
     */
    function uploadToQiniu(){
        Log::alert('$_FILE数据:' . print_r($_FILES, true));

        if (isset($_FILES['Filedata']['tmp_name'])) {

            //ext name
            $image_info = getimagesize($_FILES['Filedata']['tmp_name']);
            $file_name_arr = explode("/", $image_info['mime']);
            $file_name_ext = $file_name_arr[1];

            $disk = QiniuStorage::disk('qiniu');
            $contents = file_get_contents($_FILES['Filedata']['tmp_name']);

            //name
            $file_name = substr(md5_file($_FILES['Filedata']['tmp_name']), 12) . '.' . $file_name_ext;
            $disk->put($file_name, $contents);

        }
        return $file_name;
    }
    

    /**
     * plupload新插件图片上传
     */
    function uploadPlupLoadToQu(){
        Log::alert('$_FILE数据:' . print_r($_FILES, true));
    
        if (isset($_FILES['file']['tmp_name'])) {
    
            //ext name
            $image_info = getimagesize($_FILES['file']['tmp_name']);
            $file_name_arr = explode("/", $image_info['mime']);
            $file_name_ext = $file_name_arr[1];
    
            $disk = QiniuStorage::disk('qiniu');
            $contents = file_get_contents($_FILES['file']['tmp_name']);
    
            //name
            $file_name = substr(md5_file($_FILES['file']['tmp_name']), 12) . '.' . $file_name_ext;
            $disk->put($file_name, $contents);
    
        }
        return $file_name;
    }

    /**
     * 正向替换description 用于匹配ueditor中的src的路径 替换成 {{IMAGE_DISPLAY_DOMAIN}}
     */
    protected function getPushDescription($description)
    {
        $image = 'http://'.env("IMAGE_DOMAIN").'/';
        $replace = "{{IMAGE_DISPLAY_DOMAIN}}";
        $new = str_replace($image, $replace, $description);
        return $new;
    }

    /**
     * 反向替换description 用于正常显示图片 替换成 正常路径名
     */
    protected function getFetchDescription($description)
    {
        $replace = 'http://'.env("IMAGE_DOMAIN").'/';
        $patterns = "{{IMAGE_DISPLAY_DOMAIN}}";
        $new = str_replace($patterns, $replace, $description);
        return $new;
    }

    /**取商品价格最大值与最小值及分成比例**/
    function getPrice($goodsId){
        $price['min'] = GoodsSpec::whereGoodsId($goodsId)->min('price');
        $price['max'] = GoodsSpec::whereGoodsId($goodsId)->max('price');
        $price['price_market'] = GoodsSpec::whereGoodsId($goodsId)->pluck('price_market');
        $specRadioFree = GoodsSpec::whereGoodsId($goodsId)->first();

        if($specRadioFree){
            $price['guide_rate'] = $specRadioFree->guide_rate;
            $price['travel_agency_rate'] = $specRadioFree->travel_agency_rate;
        }
        if($price['min'] == $price['max']){
            $price['price'] = $price['max'];
        }else{
            $price['price'] = $price['min'].'~'.$price['max'];
        }
        $price_buying['min'] = GoodsSpec::whereGoodsId($goodsId)->min('price_buying');
        $price_buying['max'] = GoodsSpec::whereGoodsId($goodsId)->max('price_buying');
        if($price_buying['min'] == $price_buying['max']){
            $price['price_buying'] = $price_buying['max'];
        }else{
            $price['price_buying'] = $price_buying['min'].'~'.$price_buying['max'];
        }
        return $price;
    }

    /**
     * 通用接口发短信
     * apikey 为云片分配的apikey
     * text 为短信内容
     * mobile 为接受短信的手机号
     */
    static public function sendSms($text, $mobile, $apikey = "4602529ee4f348677e5c3eefe325ce2b")
    {
        $url = "http://yunpian.com/v1/sms/send.json";
        $encoded_text = urlencode("$text");
        $post_string = "apikey=$apikey&text=$encoded_text&mobile=$mobile";
        return self::sockPost($url, $post_string);
    }

    /**
     * url 为服务的url地址
     * query 为请求串
     */
    static public function sockPost($url, $query)
    {
        $data = "";
        $info = parse_url($url);
        $fp = fsockopen($info["host"], 80, $errno, $errstr, 30);
        if (!$fp) {
            return $data;
        }
        $head = "POST " . $info['path'] . " HTTP/1.0\r\n";
        $head .= "Host: " . $info['host'] . "\r\n";
        $head .= "Referer: http://" . $info['host'] . $info['path'] . "\r\n";
        $head .= "Content-type: application/x-www-form-urlencoded\r\n";
        $head .= "Content-Length: " . strlen(trim($query)) . "\r\n";
        $head .= "\r\n";
        $head .= trim($query);
        $write = fputs($fp, $head);
        $header = "";
        while ($str = trim(fgets($fp, 4096))) {
            $header .= $str;
        }
        while (!feof($fp)) {
            $data .= fgets($fp, 4096);
        }
        return $data;
    }


    /**
     * @param $mobile
     * @param $ip
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    protected function smsVerification($mobile, $ip, $type)
    {
        $today = strtotime(date('Y-m-d'));
        $sms_num = SmsVerificationCode::whereMobile($mobile)->whereType($type)->where('created_at', '>', $today)->count();
        if ($sms_num < 100) {
            $SmsVerificationCode = new SmsVerificationCode();
            $SmsVerificationCode->type = $type;
            $SmsVerificationCode->mobile = $mobile;
            $SmsVerificationCode->code = rand(100000, 999999);
            $SmsVerificationCode->ip = $ip;
            $ret = $SmsVerificationCode->save();

            if ($ret == 1) {
                //todo call send sms api
                $tpl_value = "【易游购】您的验证码是" . $SmsVerificationCode->code . "。如非本人操作，请忽略本短信";
                $sms_result = json_decode(self::sendSms($tpl_value, $mobile), true);
                Log::alert('SMS发送返回数据:' . print_r($sms_result, true));

                $sms_result['msg'] = urlencode($sms_result['msg']);
                if(isset($sms_result['detail'])){
                    $sms_result['detail'] = urlencode($sms_result['detail']);
                }
                
                SmsVerificationCode::whereId($SmsVerificationCode->id)->update(array('sid' => urldecode(json_encode($sms_result))));
            }

            $result = array('ret' => self::RET_SUCCESS, 'msg' => '', 'data' => array('mobile' => $mobile, 'recode' => strval($SmsVerificationCode->code)));
            return response()->json($result);
        }

        $result = array('ret' => self::RET_FAIL, 'msg' => self::OVER_REQUEST_NUM, 'data' => (object)array());
        return response()->json($result);
    }

    static public function getGuideAmountSendSms($mobile,$ip,$type,$tpl_value,$code){
        $PlatformSm = new PlatformSm();
        $PlatformSm->type = $type;
        $PlatformSm->mobile = $mobile;
        $PlatformSm->code = $code;
        $PlatformSm->ip = $ip;
        $ret = $PlatformSm->save();
        $result = 0;
        if ($ret == 1) {
            $sms_result = json_decode(self::sendSms($tpl_value, $mobile), true);
            $sms_result['msg'] = urlencode($sms_result['msg']);
            if(isset($sms_result['detail'])){
                $sms_result['detail'] = urlencode($sms_result['detail']);
            }
            PlatformSm::whereId($PlatformSm->id)->update(array('sid' => urldecode(json_encode($sms_result))));
            $result  = isset($sms_result['result']['sid']) ? intval($sms_result['result']['sid']) : 0;
        }
        return $result;
    }


    protected function getGuideUserInfo($id){
        $GuideBase = GuideBase::where('id',$id)->first();
        $UserBase = UserBase::whereId($GuideBase['uid'])->first();

        $result = array();
        $result['id'] = strval($GuideBase['id']);
        $result['uid'] = strval($UserBase['id']);
        $result['rank'] = strval($UserBase['id']+1000);
        $result['nick_name'] = $UserBase['nick_name'] == '' ? '昵称未设置' : $UserBase['nick_name'];
        $result['avatar'] =  $GuideBase['avatar'] != '' ? $GuideBase['avatar'] : 'avatar_def.png';
        $result['avatar'] =  env('IMAGE_DISPLAY_DOMAIN').$result['avatar'];
        $result['store_cover'] =  $GuideBase['store_cover'] != '' ? env('IMAGE_DISPLAY_DOMAIN').$GuideBase['store_cover'] : '';
        $result['mobile'] = $UserBase['mobile'];
        $result['token'] = $UserBase['token'];
        $result['state'] = strval($UserBase['state']);
        $result['state_cn'] = UserBase::getStateCnByState($UserBase['state']);

        //是旅行社邀请的就审核导游证
        $result['audit_guide_no'] = substr($GuideBase['invite_code'],0,1) == 's' ? '0' : '1';

        $result['city_version'] = '1.1';
        $result['service_tel'] = '400-9158-971';

        $result['today_order_num'] = strval(self::todayOrderNum($id));
        $result['total_order_num'] = strval(self::totalOrderNum($id));
        $result['month_order_amount'] = strval(self::monthOrderAmount($id));

        $result['today_user_num'] = strval(self::todayUserNum($id));
        $result['total_user_num'] = strval(self::totalUserNum($id));
        $result['today_wx_subscribe_num'] = strval(self::todayWxSubscribeNum($id));
        $result['total_wx_subscribe_num'] = strval(self::totalWxSubscribeNum($id));

        $result['total_amount'] = strval(self::totalAmount($id));
        $result['pending_amount'] = strval(self::pendingAmount($id));
        $result['balance'] = strval($UserBase['amount']);
        $result['taid'] = strval(self::getCurrentTaGroup($id));
        $result['qrcode'] = $GuideBase['qrcode'] == '' ? '' : env('IMAGE_DISPLAY_DOMAIN').$GuideBase['qrcode'];
        $result['wx_qrcode'] = $GuideBase['qrcode'] == '' ? '' : 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$GuideBase['wx_qrcode'];

        return $result;
    }

    static private function getCurrentTaGroup($guide_id){
        $TaGroup = TaGroup::whereGuideId($guide_id)->whereState(TaGroup::STATE_START)->first();
        $ta_id = 0;
        if(!is_null($TaGroup)){
            $ta_id = $TaGroup['ta_id'];
        }
        return $ta_id;
    }

    static private function todayOrderNum($guide_id){
        $today = date("Y-m-d").' 00:00:00';
        $num = OrderBase::whereGuideId($guide_id)->where('created_at','>',$today)
            ->whereIn('state',array(OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED))->count();
        return $num;
    }

    static private function totalOrderNum($guide_id){
        $num = OrderBase::whereGuideId($guide_id)
            ->whereIn('state',array(OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED))->count();
        return $num;
    }

    static private function monthOrderAmount($guide_id){
        $month_first_date = date("Y-m-d H:i:s",mktime(0, 0 , 0,date("m")-1,1,date("Y")));
        $amount = OrderBase::whereGuideId($guide_id)->where('created_at','>',$month_first_date)
            ->whereIn('state',array(OrderBase::STATE_PAYED,OrderBase::STATE_SEND,OrderBase::STATE_FINISHED))
            ->sum('amount_goods');
        return $amount;
    }
    
    static private function todayUserNum($guide_id){
        $today = date("Y-m-d 00:00:00",time());
        $num = UserWx::whereGuideId($guide_id)->where('created_at','>',$today)->count();
        return $num;
    }

    static private function totalUserNum($guide_id){
        //->where('uid','>',0)
        $num = UserWx::whereGuideId($guide_id)->count();
        return $num;
    }

    static private function todayWxSubscribeNum($guide_id){
        $today = date("Y-m-d 00:00:00",time());
        $num = UserWx::whereGuideId($guide_id)->whereSubscribe(1)->where('created_at','>',$today)->count();
        return $num;
    }

    static private function totalWxSubscribeNum($guide_id){
        //->where('uid','>',0)
        $num = UserWx::whereGuideId($guide_id)->whereSubscribe(1)->count();
        return $num;
    }


    static public function totalAmount($guide_id){
        $guide_billing = GuideBilling::whereGuideId($guide_id)->whereInOut(GuideBilling::in_income)->sum('amount');
        $return_amount = GuideBilling::whereGuideId($guide_id)->whereInOut(GuideBilling::in_income)->sum('return_amount');
        $guide_amount = number_format($guide_billing-$return_amount,2);
        return $guide_amount;
    }

    static private function pendingAmount($guide_id){
        $guide_billing = GuideBilling::whereGuideId($guide_id)->whereInOut(GuideBilling::in_income)->whereState(GuideBilling::state_nofund)->sum('amount');
        $guide_return = GuideBilling::whereGuideId($guide_id)->whereInOut(GuideBilling::in_income)->whereState(GuideBilling::state_nofund)->sum('return_amount');
        $guide_amount = number_format($guide_billing-$guide_return,2);
        return $guide_amount;
    }



    protected function formatOrderData($orders){
        $result = array();
        foreach($orders as $order){
            $tmp = array();
            $tmp['is_comment'] = $order->iscomment;
            $UserBase = UserBase::whereId($order['uid'])->first();
            $tmp['id'] = strval($order['id']);
            $tmp['order_no'] = strval($order['order_no']);
            $tmp['created_at'] = date('Y.m.d H:i',strtotime($order['created_at']));
            $tmp['created_timestamp'] = strval(strtotime($order['created_at']));

            $tmp['order_from'] = $UserBase['nick_name'] != '' ? $UserBase['nick_name'] : substr($UserBase['mobile'],0,3).'****'.substr($UserBase['mobile'],7);
            $tmp['receiver_name'] = $order['receiver_name'];
            $tmp['receiver_mobile'] = $order['receiver_mobile'];

            $receiver_info = json_decode($order['receiver_info'],true);
            $tmp['receiver_address'] = '';
            if(isset($receiver_info['province'])){
                $tmp['receiver_address'] = $tmp['receiver_address'].$receiver_info['province'];
            }
            if(isset($receiver_info['city'])){
                $tmp['receiver_address'] = $tmp['receiver_address'].$receiver_info['city'];
            }
            if(isset($receiver_info['district'])){
                $tmp['receiver_address'] = $tmp['receiver_address'].$receiver_info['district'];
            }
            if(isset($receiver_info['address'])){
                $tmp['receiver_address'] = $tmp['receiver_address'].$receiver_info['address'];
            }

            $tmp['express_type'] = strval($order['express_type']);
            $tmp['state_cn'] = OrderBase::getStateCN($order['state']);
            $tmp['state_description'] =  OrderBase::getStateDescription($order['state'],strtotime($order['created_at']),strtotime($order['express_time']));
            $tmp['total_amount'] = strval($order['amount_goods'] + $order['amount_express'] - $order['amount_coupon']);
            $tmp['total_amount_origin'] = strval($order['amount_goods_origin'] + $order['amount_express'] - $order['amount_coupon']);
            $tmp['express_amount'] = strval($order['amount_express']);
            $tmp['coupon_amount'] = strval($order['amount_coupon']);
            $tmp['rebate_amount'] = $order['guide_amount'];
            $tmp['total_num'] = OrderGood::whereOrderNo($order['order_no'])->sum('num');

            $tmp['goods'] = array();
            $tmp['gift'] = array();
            $OrderGood = OrderGood::whereOrderNo($order['order_no'])->get();
            foreach($OrderGood as $v){
                $tt = array();
                $GoodsBase = GoodsBase::whereId($v['goods_id'])->first();

                $SupplierBase = SupplierBase::whereId($GoodsBase['supplier_id'])->first();
                $tt['goods_id'] = strval($v['goods_id']);
                $tt['spec_id']  = strval($v['spec_id']);
                $tt['title'] = strval($v['goods_title']);
                $tt['cover'] = env('IMAGE_DISPLAY_DOMAIN').$GoodsBase['first_image'].'?imageslim';
                $tt['spec'] = strval($v['spec_name']);
                $tt['price'] = strval($v['price']);
                $tt['num'] = strval($v['num']);
                if($v['is_gift'] == 0){
                    $tmp['goods'][] = $tt;
                }else{
                    $tt['price'] = '0.00';
                    $tmp['gift'][] = $tt;
                }
                $tmp['store_name'] = $SupplierBase['store_name'] == '' ? $SupplierBase['name'] : $SupplierBase['store_name'];
            }

            $tmp['return_no'] = '';
            $tmp['return_state'] = '';
            $OrderReturn = OrderReturn::whereOrderNo($order['order_no'])->orderBy('id','desc')->first();
            if(!is_null($OrderReturn)){
                $tmp['return_no'] = $OrderReturn['return_no'];
                $tmp['return_state'] = $OrderReturn['state'];
            }

            $result[] = $tmp;
        }
        return $result;
    }

    protected function getCityName($id)
    {
        $City = ConfCity::whereId($id)->first();
        return $City['name'] ? $City['name'] : '';
    }

    /* 验证手机验证码 */
    function checkMobileCode($mobile,$ip,$type,$code){
        $sms = SmsVerificationCode::whereMobile($mobile)->whereType($type)->whereIsValid(0)->whereIp(ip2long($ip))->orderBy('created_at','desc')->first();
        if (!$sms){
            return false;
        }else{
            if($sms->code != $code){
                return false;
            }
        }
        return true;
    }

    /* 组装json组数 */
    function getReturnResult($ret,$msg){
        return $data = ['ret'=>$ret,'msg'=>$msg];
    }

    static public function generateInviteCode($length = 5)
    {
        $chars = "abcdefghijkmnpqruvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    static public function bindWxGuideId($openId,$guideId,$ref = 0){
        if($guideId > 0 ){
            $WxGuide = WxGuide::whereOpenId($openId)->whereState(WxGuide::STATE_YES)->first();
            if (is_null($WxGuide)) {
                $WxGuide = new WxGuide();
                $WxGuide->open_id = $openId;
                $WxGuide->ref = $ref;
                $WxGuide->state = WxGuide::STATE_YES;
            }
            $WxGuide->guide_id = $guideId;
            $WxGuide->save();
        }
    }

    static public function sendCouponToUser($supplier_id,$uid,$order_no){
        $CouponBases = CouponBase::whereSupplierId($supplier_id)->whereState(CouponBase::state_normal)->get();
        $goodsIdArray = OrderGood::whereOrderNo($order_no)->lists('goods_id')->toArray();
        $CouponGoods = CouponGood::whereSupplierId($supplier_id)->whereState(CouponBase::state_normal)->lists('goods_id')->toArray();

        foreach($goodsIdArray as $goodsId){
            if(in_array($goodsId,$CouponGoods)){
                foreach($CouponBases as $CouponBase){
                    CouponController::couponUser($uid,$CouponBase['id'],$order_no,$CouponBase['supplier_id']);
                }
            }
        }
    }

    static public function getIsCouponGoods($goods_id){
        $num = CouponGood::whereGoodsId($goods_id)->whereState(CouponGood::STATE_NORMAL)->count();
        if($num > 0 ){
            return '1';
        }
        return '0';
    }

}
