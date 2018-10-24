@extends('wx.layout')
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
            <a href="#" class="linkArrow payLink weixinPay" onclick="callpay()" >
                <dl><dt style="height: 24px;"><i></i></dt><dd>微信安全支付</dd></dl>
                <span class="arrow" style="top: 8px;"></span></a>
        </div>
    </div>

    <div class="popupBg"></div>
    <div class="popupWrap customeService">
        {{--@if($returnWay == 0)
            <h3>确认要离开收银台？</h3>
            <p>超过支付时效后订单将被取消，请尽快完成支付</p>
            <div class="bottomButtonGroup lineT lineR">
                <a href="/cart" class="button">确认离开</a>
                <a href="javascript:void(0)" class="close button">继续支付</a>
            </div>
        @else
            <p>便宜不等人，确认放弃？</p>
            <div class="bottomButtonGroup lineT lineR">
                <a href="/goods/{{$returnWay}}" class="button">确认离开</a>
                <a href="javascript:void(0)" class="close button">继续支付</a>
            </div>

        @endif--}}
    </div>
    <input type="hidden" name="ordersn" id="ordersn" value="{{$data['ordersn']}}">
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
                            <input type="hidden" name="direct" value="1" id="direct">
                            <p>{{$data['receiver_info']['name']}} {{$data['receiver_info']['mobile']}}</p>
                            <p>{{$data['receiver_info']['province']}} {{$data['receiver_info']['city']}}{{$data['receiver_info']['district']}}{{$data['receiver_info']['address']}}</p>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="couponNumShow">
            <p style="color:red;background: #fff;text-align: center;padding: 10px 0;">
                <span class="couponNum"></span>
                <a href="/coupon/0">立即查看</a>
            </p>
        </div>
        <div style="margin-top: 15px;"><img src="/wx/images/orderPay.png" alt="二维码图片" width="100%"></div>
        <!--<div class="conButtonGroup" style="text-align: center;">
            <span class=""><a href="/order/1" class="btnYellow">查看订单</a></span>
            {{--<span class="right"><a href="/" class="btnOrange">返回首页</a></span>--}}
        </div>
        <div class="attention">
            <p>长按扫码识别关注易游购公众号，查看订单详情！</p>
            <img src="/wx/images/attention_code_03.png" alt="二维码图片">
        </div>-->
    </div>
@endsection
@section('javascript')
    <script>
        /*$(".payOrder").hide();
        var ordersn = $('#ordersn').val();
        $.post('/orderCoupon',{ordersn:ordersn},function(ret){
            var i = '恭喜!您获得了'+ret+'张优惠券~';
            $(".couponNum").text(i);
        })
        $(".successWrap").show();*/
        /*var ispay = 0;
        pushHistory();
        window.addEventListener("popstate", function(e) {
            var direct = $("#direct").val();
            if(direct == 1){
                popupShow('customeService');
                if(ispay==1){
                    $(".popupBg,.popupWrap").hide();
                    ispay = 0;
                }
            }else{
                window.location = '/';
            }

        }, false);
        function pushHistory() {
            var state = {
                title: "title",
                url: "#"
            };
            window.history.pushState(state, "title", "#");
        }*/
        /*$(".close,.popupBg").click(function(){
            pushHistory();
        });*/
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
                            $("#direct").val(2);
                            $(".payOrder").hide();
                            $(".payOrder").hide();
                             var ordersn = $('#ordersn').val();
                             $.post('/orderCoupon',{ordersn:ordersn},function(ret){
                                 if(ret != 0){
                                     var i = '恭喜!您获得了'+ret+'张优惠券~';
                                     $(".couponNum").text(i);
                                     $(".couponNumShow").show();
                                 }else{
                                     $(".couponNumShow").hide();
                                 }
                             })
                             $(".successWrap").show();
                        }
                    }
            );
        }
        function callpay()
        {
            ispay = 1;
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