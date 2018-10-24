@extends('wx.layout_sub')
@section('title')
    订单详情
@endsection
@section('content')
<div class="headerBg">
	<div class="back" onclick="javascript:history.go(-1)"></div>
	<div class="title">订单详情</div>
</div>
<div class="content myOrder">
    @foreach($orderdetails as $orderdetail)
        @if($orderdetail->state == 0)
            <div class="myOrderDetailTop">
                <div class="step step-1">
                    <p>等待买家付款</p>
                    <p>剩余<span class="D-minute">{{$orderdetail->receiveGoodTime}}</span>分钟自动关闭</p>
                </div>
            </div>
        @elseif($orderdetail->state == 1)
            <div class="myOrderDetailTop">
                <div class="step step-2">
                    <p>买家已付款</p>
                    <p>您的包裹整装待发</p>
                </div>
            </div>
        @elseif($orderdetail->state == 2)

            <div class="myOrderDetailTop">
                <div class="step step-3">
                    <p>卖家已发货</p>
                    @if($orderdetail->receiveGoodTime >= 0)
                        <p>剩余<span class="D-day">{{$orderdetail->receiveGoodTime}}</span>天自动确认收货</p>
                    @endif
                </div>
            </div>
        @elseif($orderdetail->state == 5)
            <div class="myOrderDetailTop">
                <div class="step step-4">
                    <p>订单已完成</p>
                    <p>期待您的下次光临</p>
                </div>
            </div>
        @elseif($orderdetail->state == 12)
            <div class="myOrderDetailTop">
                <div class="step step-4">
                    <p>订单已取消</p>
                    <p>请选购其他商品</p>
                </div>
            </div>
        @endif

    @endforeach
    @foreach($orderdetails as $orderdetail)
    <div class="addressLink lineB pt-6">
        <div class="linkArrow"><dl><dt>{{$orderdetail->receiver_name}} {{$orderdetail->receiver_mobile}}</dt><dd>{{$orderdetail->receiver_info['province']}}{{$orderdetail->receiver_info['city']}}{{$orderdetail->receiver_info['district']}}{{$orderdetail->receiver_info['address']}}</dd></dl></div>
    </div>


    <div class="lineB mb-6">
        @foreach( $orderdetail->supplierinfo as $supplierinfo)
        <h3>{{$supplierinfo['store_name']}}</h3>
        @endforeach
        <div class="orderGoods lineB">
	
            @foreach($orderdetail->data as $goods)
            <a href="/goods/{{$goods['goodsbase']->id}}">
	            <dl class="info lineB">
	                <dt><img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$goods['goodsbase']->cover_image}}?imageslim"></dt>
	                <dd>
	                    <p class="name">{{$goods['goodsinfo']->goods_title}}</p>
	                    <p>规格：{{$goods['goodsinfo']->spec_name}}<span class="amount">数量x{{$goods['goodsinfo']->num}}</span></p>
	                    <p>价格：<span class="price">￥{{$goods['goodsinfo']->price}}</span></p>
	                </dd>
	            </dl>
            </a>
            @endforeach
                @if(isset($orderdetail->gift))
                    @foreach($orderdetail->gift as $gift)
                        <a href="/goods/{{$goods['goodsbase']->id}}">
                        <dl class="info lineB">
                            <dt class="lineT br4">
                                <span class="gifts">活动赠品</span>
                                <img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$gift['cover_image']}}">
                            </dt>
                            <dd>
                                <p class="name">{{$gift['title']}}</p>
                                <p>规格：{{$gift['spec_name']}}<span class="amount">数量{{$gift['num']}}</span></p>
                                <p>价格：<span class="price">￥{{$gift['price']}}</span></p>
                            </dd>
                        </dl>
                        </a>
                    @endforeach
                @endif

            <div class="total">共{{$orderdetail->goodsnum}}件商品<span>合计：<b class="price">￥{{$orderdetail->sumprice}}</b><b class="freight">({{$orderdetail->pay_way}})</b></span></div>
        </div>






                @if($orderdetail->state == 0)
                    <div class="buttonGroup lineB">
                        <input type="button" value="取消订单" onclick="popupShow('confirmPopup')">
                        <input type="button" value="继续付款" class="btnOrange" onclick="window.location.href='/order/pay?orderno={{$orderdetail->order_no}}&'">
                    </div>
                @elseif($orderdetail->state == 1)

                @elseif($orderdetail->state == 2)
                        <div class="buttonGroup lineB">
                            @if(empty($orderdetail->returnState))
                                <input type="button" value="申请售后" onclick="window.location.href='/aftersales/{{$orderdetail->order_no}}'">
                            @elseif(!empty($orderdetail->returnState))
                                <input type="button" value="查看售后" onclick="window.location.href='/aftersalesDetail/{{$orderdetail->order_no}}'">
                            @endif
                                <input type="button" value="确认收货" class="btnOrange" onclick="finished({{$orderdetail->order_no}},{{$orderdetail->uid}})">
                        </div>
                @elseif($orderdetail->state == 5)

                @endif



    </div>


    <div class="orderInfo">
        <p>订单编号：<span id="copyTxt">{{$orderdetail->order_no}}</span><a href="javascript:void(0)" id="copyBtn" class="copyBtn"  data-clipboard-action="copy" data-clipboard-target="#copyTxt">复制</a></p>
        <p>创建时间：<span class="create-time">{{$orderdetail->created_at}}</span></p>
        <input type="hidden" name="timeSend" class="express-time" value="{{$orderdetail->created_at}}">
        @if($orderdetail->state == 2||$orderdetail->state == 5)
            <p>物流公司：<span>{{$orderdetail->express_name}}</span></p>
            <p>物流单号：<span class="express">{{$orderdetail->express_no}}</span><a class="btnOrange" style="width: 80px;border:0;color:#fff;line-height: 32px; text-align: center;" href="http://www.baidu.com/s?wd={{$orderdetail->express_name}}+{{$orderdetail->express_no}}">查看物流</a></p>
            <p>物流客服：<span class="express"><a style="border: 0;float: none;color: #5DB3FF;padding: 0;" href="tel:{{$orderdetail->expressTel}}">{{$orderdetail->expressTel}}</a></span></p>
            @if(isset($orderdetail->express_more))


                <span class="expressBox">
                    <span style="color: #5DB3FF;padding: 10px 0;display: inline-block;">查看更多物流单号</span>
                </span>
                <div class="expressList" style="display: none;">
                @foreach($orderdetail->express_more as $expressMore)
                    <p style="line-height: 32px;margin-bottom: 10px;">物流单号：<span class="express">{{$expressMore->express_no}}</span><a class="btnOrange" style="width: 80px;border:0;color:#fff;line-height: 32px; text-align: center;" href="http://www.baidu.com/s?wd={{$expressMore->express_name}}+{{$expressMore->express_no}}">查看物流</a></p>
                @endforeach
                </div>
            @endif
        @endif
    </div>
    @endforeach

</div>

<div class="popupBg"></div>
<div class="popupWrap confirmPopup">
    <p>取消订单</p>
    <p>确定需要取消订单吗？</p>
    <div class="bottomButtonGroup lineT lineR">
        <button class="close button">取消</button>
        <button class="close button" onclick="ceshi(<?php echo $order_no?>)">确定</button>
    </div>
</div>
@endsection
@section('javascript')
    <script type="text/javascript" src="/wx/js/clipboard.min.js"></script>

    <script>
        $(".expressBox").click(function(){
            $(".expressList").slideToggle();
        });
        function a(){
            information("已申请售后");
        }

        $(".copyBtn").click(function(){
            var info = '复制成功';
            information(info);
        })

        function finished(orderNo ,uid){
            $.ajax({
                type:'get',
                url:'/finished/'+orderNo,
                dataType:'json',
                data:{
                    'uid':uid,
                },
                success:function(data){
                    if(data.ret == -1){
                        information(data.msg)
                    }else{
                        location.href = '/order/';
                    }

                }
            });
        }

        /*DateDisparity();
        function DateDisparity() {
            var date = new Date();
            var text = $(".create-time").text();
            var text = text.replace(/-/g,'/');
            var CreateTime = new Date(text);
            /!*时间差*!/
            var Disparity = date.getTime() - CreateTime.getTime();
            var Days = 7 - Math.floor(Disparity / (24 * 60 * 60 * 1000));
            var Hours = Math.floor(Disparity / (60 * 60 * 1000));
            var Minute = 30 - Math.floor(Disparity / (60 * 1000));
            $(".D-minute").text(Minute);
            $(".D-day").text(Days);
        }
        var auto = setInterval("DateDisparity()",5000);*/

        function ceshi(orderno){
            $.ajax({
                type:'get',
                url:'/cancelorder/'+orderno,
                datatype:'json',
                success:function(data){
                    window.location.reload();
                }
            });
        }

    </script>
    <script>
        var clipboard = new Clipboard('#copyBtn');
        clipboard.on('success', function(e) {
            console.log(e);
        });
        clipboard.on('error', function(e) {
            console.log(e);
        });
        function payment(orderNo){
            $.ajax({
                type:'post',
                url:'/payment/',
                dataType:'json',
                data:{
                    'orderno':orderNo
                },
                success:function(data){

                }
            });
        }
    </script>
@endsection
