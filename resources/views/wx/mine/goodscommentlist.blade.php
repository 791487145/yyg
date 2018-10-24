@extends('wx.layout')
@section('title')
    我的评价
@endsection
@section('content')
<link rel="stylesheet" type="text/css" href="{{ asset('lib/jQuery.filer/css/jquery.filer.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('lib/jQuery.filer/css/themes/jquery.filer-dragdropbox-theme.css') }}">
<style>
	.myOrder .total{padding:14px  0;}
	.myOrder .fileUpload label{background: url(../wx/images/addIcon.png)no-repeat;background-size: 62px 62px;}
	.linkArrow .arrowicon {
	    display: inline-block;
	    line-height: 44px;
	    height: 44px;
	    color: #999;
	    padding-right: 10px;
	    background: url(../wx/images/right_arrow.png) no-repeat center;
	    background-size: 6px 12px;
	    position: absolute;
	    top: 0px;
	    margin-left: 10px;
	}
	.myOrder .total{padding: 14px ;}
	.comment{width:80px;line-height:20px;border: 1px solid #ED6B09;text-align: center;border-radius:3px;-webkit-border-radius:3px;
		margin-top: -3px;color: #ED6B09;float: right;
	}
</style>
	<div class="headerBg">
		<div class="back" onclick="javascript:history.go(-1)"></div>
		<div class="title">我的评价</div>
	</div>
    <div class="content myOrder">
            @if(!empty($orders))
            @foreach($orders as $order)
                <div class="mb-6">
                    <ul class="goodsSpec lineB">
        	            <li class="lineB">
        	                <a href="/supplier/{{$order->supplier_id}}" class="linkArrow">
        	                    <span class="yyg-color6">{{$order->supplier->store_name}}</span>
        	                    <span class="arrowicon"></span>
        	                    <span class="fr yyg-color">交易成功</span>
        	                </a>
        	            </li>    
        	        </ul>
        	        @foreach($order->data as $goods)
                        <div class="orderGoods lineB">
                            <a href="/detail/{{$goods['orderGoods']->order_no}}">
                            <dl class="info lineB">
                                <dt>
                                @if(isset($goods['goods']->cover_image))
                                    <img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$goods['goods']->cover_image}}?imageslim">
                                @endif
                                </dt>
                                <dd>
                                    <p class="name">{{$goods['orderGoods']->goods_title}}</p>
                                    <p>规格：{{$goods['orderGoods']->spec_name}}<span class="amount">数量x{{$goods['orderGoods']->num}}</span></p>
                                    <p><span class="price">￥{{$goods['orderGoods']->price}}</span></p>
                                </dd>
                            </dl>
                            </a>
                        </div>
                    @endforeach
                    <!-- 赠品信息 -->
                    @if(isset($order->gift))
                        @foreach($order->gift as $gift)
                        <div class="orderGoods lineB">
                            <a href="/detail/{{$goods['orderGoods']->order_no}}">
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
                            </a>
                        </div>
                        @endforeach
                    @endif
                    <div class="total">共{{$order->goodsCount}}件商品
                    	<b style="margin-left:20px;" class="price">￥{{$order->priceSum}}({{$order->pay_way}})</b>                    	
                    	   @if(!empty($order->hascomment))                                                                     
                             <a href="{{url('/mine/detail',$order->order_no)}}" class="comment">查看评价</a>                                                                    
                           @else                                                                     
                             <a href="{{url('/mine/comment',$order->order_no)}}" class="comment">去评价</a>
                           @endif
                    </div>
                </div>
            @endforeach
        @endif
    </div>
@endsection