@extends('wx.layout')
@section('title')
    申请售后
@endsection
@section('content')
<div class="headerBg">
	<div class="back" onclick="javascript:history.go(-1)"></div>
	<div class="title">申请售后</div>
</div>
<div class="myOrder">
    @foreach($items as $orderdetails)
        @foreach($orderdetails as $orderdetail)
    <div class="lineB mb-6">
        @foreach($orderdetail->supplierinfo as $supplierinfo)

        <h3>{{$supplierinfo->store_name}}
            @if($orderdetail->state == 0)
            <span>待审核</span>
            @elseif($orderdetail->state == 1)
                <span>待退款</span>
            @elseif($orderdetail->state == 3)
                <span>已退款</span>
            @elseif($orderdetail->state == 4)
                <span>已驳回</span>
            @endif
        </h3>

        @endforeach

        <div class="orderGoods lineB">
            @foreach($orderdetail->data as $goods)
            <dl class="info lineB">
                <dt><img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$goods['goodsbase']->cover_image}}"></dt>
                <dd>
                    <p class="name">{{$goods['goodsbase']->title}}</p>
                    <p>规格：{{$goods['goodsinfo']->spec_name}}<span class="amount">数量x{{$goods['goodsinfo']->num}}</span></p>
                    <p>价格：<span class="price">￥{{$goods['goodsinfo']->price}}</span></p>
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
            <div class="total">共{{$orderdetail->goodsnum}}件商品<span>合计：<b class="price">￥{{$orderdetail->sumprice}}</b><b class="freight">({{$orderdetail->pay_way}})</b></span></div>
        </div>

        <div class="buttonGroup lineB">
            <input type="button" value="查看售后详情" class="btnOrange bigBtn" onclick="window.location.href='/aftersalesDetail/{{$orderdetail->order_no}}'">
        </div>
    </div>
        @endforeach
    @endforeach
</div>
@endsection