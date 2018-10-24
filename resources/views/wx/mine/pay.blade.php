@extends('wx.layout')
<?php
require_once(app_path().'/Lib/Wx/WxPay.JsApiPay.php');
require_once(app_path().'/Lib/Wx/lib/WxPay.Api.php');

//①、获取用户openid
$tools = new JsApiPay();
$openId = $tools->GetOpenid();

//②、统一下单
$input = new WxPayUnifiedOrder();
$input->SetBody("易游购订单:".$data['ordersn']);
$input->SetAttach($data['ordersn']);
$input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
$input->SetTotal_fee("1");
$input->SetTime_start(date("YmdHis"));
$input->SetTime_expire(date("YmdHis", time() + 600));
$input->SetGoods_tag("易游购订单:".$data['ordersn']);
$input->SetNotify_url("http://testpay.yyougo.com/order/notify.php");
$input->SetTrade_type("JSAPI");
$input->SetOpenid($openId);
$order = WxPayApi::unifiedOrder($input);
$jsApiParameters = $tools->GetJsApiParameters($order);

?>
@section('title')
订单支付
@endsection
@section('content')
<div class="payOrder">
    <h2><i></i>订单提交成功，等待买家付款～</h2>
    <div class="payInfo">
        <dl class="lineB">
            <dt>应付金额：</dt>
            <dd><span class="price">￥{{$data['amount']}}</span></dd>
        </dl>
        <dl class="lineB">
            <dt>收货信息：</dt>
            <dd>
                <p class="base"><span>{{$data['receiver_info']['name']}}</span><span>{{$data['receiver_info']['mobile']}}</span></p>
                <p>{{$data['receiver_info']['province']}} {{$data['receiver_info']['city']}}{{$data['receiver_info']['district']}}{{$data['receiver_info']['address']}}</p>
            </dd>
        </dl>
    </div>
    <h3>请选择支付方式<span>请在30分钟之内完成支付</span></h3>
    <div class="paymentLink lineB">
        <a href="#" class="linkArrow payLink weixinPay" onclick="callpay()" ><dl><dt><i></i></dt><dd>微信安全支付</dd></dl><span class="arrow"></span></a>
    </div>
</div>
<div class="successWrap" style="display: none;
">
    <div class="info">
        <div class="lineB successTop">支付完成</div>
        <div class="lineB infoCon">
            <table>
                <tr>
                    <th>订单金额</th>
                    <td>￥{{$data['amount']}}</td>
                </tr>
                <tr>
                    <th>收货信息</th>
                    <td>
                        <p>{{$data['receiver_info']['name']}} {{$data['receiver_info']['mobile']}}</p>
                        <p>{{$data['receiver_info']['province']}} {{$data['receiver_info']['city']}}{{$data['receiver_info']['district']}}{{$data['receiver_info']['address']}}</p>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <img src="/wx/images/orderPay.png" style="width: 100%;margin: 20px 0;" alt="二维码图片">
</div>
@endsection
@section('javascript')
<script>


//调用微信JS api 支付
function jsApiCall()
{
    WeixinJSBridge.invoke(
            'getBrandWCPayRequest',
            <?php echo $jsApiParameters; ?>,
            function(res){
                WeixinJSBridge.log(res.err_msg);
                /*alert(res.err_code+res.err_desc+res.err_msg);*/
                //alert(res.err_code+'||'+res.err_desc+'||'+res.err_msg)
                if(res.err_msg == 'get_brand_wcpay_request:ok'){
                    $(".payOrder").hide();
                    $(".successWrap").show();
                }
            }
    );
}
function callpay()
{
    if (typeof WeixinJSBridge == "undefined"){
        if( document.addEventListener ){
            document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
        }else if (document.attachEvent){
            document.attachEvent('WeixinJSBridgeReady', jsApiCall);
            document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
        }
    }else{
        jsApiCall();
    }
}

</script>
@endsection