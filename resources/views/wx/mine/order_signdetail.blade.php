@extends('wx.layout')
@section('title')
    订单详情
@endsection
@section('content')
    <div class="content myOrder">
        <div class="myOrderDetailTop">
            <div class="step step-3">
                <p>卖家已发货</p>
                <p>剩余7天自动确认收货</p>
            </div>
        </div>

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
                        <dl class="info lineB">
                            <dt><img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$goods['goodsbase']->cover}}?imageslim"></dt>
                            <dd>
                                <p class="name">{{$goods['goodsbase']->title}}</p>
                                <p>规格：{{$goods['goodsspec']->name}}<span class="amount">数量x{{$goods['goodsinfo']->num}}</span></p>
                                <p>价格：<span class="price">￥{{$goods['goodsspec']->price}}</span></p>
                            </dd>
                        </dl>
                    @endforeach

                    <div class="total">共{{$orderdetail->goodsnum}}件商品<span>合计：<b class="price">￥{{$orderdetail->amount_real}}</b><b class="freight">({{$order->pay_way}})</b></span></div>
                </div>
                <div class="buttonGroup lineB">
                    <input type="button" value="申请售后" onclick="popupShow('confirmPopup')">
                    <input type="button" value="确认收货" class="btnOrange" onclick="location.href='order.html'">
                </div>
            </div>


            <div class="orderInfo">
                <p>订单编号：<span id="copyTxt">{{$orderdetail->order_no}}</span><a href="javascript:void(0)" class="copyBtn" id="copyBtn" data-clipboard-action="copy" data-clipboard-target="#copyTxt">复制</a></p>
                <p>创建时间：<span>{{$orderdetail->created_at}}</span></p>
                <p>物流公司：<span>{{$orderdetail->express_name}}</span></p>
                <p>物流单号：<span class="express">{{$orderdetail->express_no}}</span><a class="copyBtn" href="http://www.baidu.com/s?wd={{$orderdetail->express_name}}+{{$orderdetail->express_no}}">查看物流</a></p>
                <p>物流客服：<span class="express">400-9158-971</span></p>
            </div>
        @endforeach

    </div>

   {{-- <div class="popupBg"></div>
    <div class="popupWrap confirmPopup">
        <p>取消订单</p>
        <p>确定需要取消订单吗？</p>
        <div class="bottomButtonGroup lineT lineR">
            <button class="close button">取消</button>
            <button class="close button">确定</button>
        </div>
    </div>--}}
@endsection


<script>
    var clipboard = new Clipboard('#copyBtn');
    clipboard.on('success', function(e) {
        console.log(e);
    });
    clipboard.on('error', function(e) {
        console.log(e);
    });
</script>

