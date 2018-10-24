@extends('wx.layout')
@section('title')
    订单详情
@endsection
@section('content')
    <div class="content myOrder">
        <div class="myOrderDetailTop">
            <div class="step step-2">
                <p>买家已付款</p>
                <p>您的包裹整装待发</p>
            </div>
        </div>

        @foreach($orderDetails as $orderdetail)
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
                        @if(isset($orderdetail->gift))
                            @foreach($orderdetail->gift as $gift)
                                        <dl class="info lineB">
                                            <dt class="lineT br4">
                                                <span class="gifts">活动赠品</span>
                                                <img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$gift['cover_image']}}?imageslim">
                                            </dt>
                                            <dd>
                                                <p class="name">{{$gift['title']}}</p>
                                                <p>规格：{{$gift['spec_name']}}<span class="amount">数量{{$gift['num']}}</span></p>
                                                <p>价格：<span class="price">￥{{$gift['price']}}</span></p>
                                            </dd>
                                        </dl>
                            @endforeach
                        @endif
                    <div class="total">共{{$orderdetail->goodsnum}}件商品<span>合计：<b class="price">￥{{$orderdetail->amount_real}}</b><b class="freight">({{$order->pay_way}})</b></span></div>
                </div>

            </div>


            <div class="orderInfo">
                <p>订单编号：<span id="copyTxt">{{$orderdetail->order_no}}</span><a href="javascript:void(0)" class="copyBtn" id="copyBtn" data-clipboard-action="copy" data-clipboard-target="#copyTxt">复制</a></p>
                <p>创建时间：<span>{{$orderdetail->created_at}}</span></p>
            </div>
        @endforeach

    </div>


@endsection




