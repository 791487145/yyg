@extends('wx.layout_sub')
@section('title')
    订单列表
@endsection
@section('content')
<div class="headerBg">
	<div class="back" onclick="javascript:history.go(-1)"></div>
	<div class="title">订单列表</div>
</div>
<div class="content myOrder">
    <div class="tabButton lineB">
        <ul>
            <li @if($state == \App\Http\Controllers\Wx\MineController::num)class="active" @endif><a href="/order/"><span>全部订单</span></a></li>
            <li @if($state == 0)class="active" @endif><a href="/order/0"><span>待付款</span></a></li>
            <li @if($state == 1)class="active" @endif><a href="/order/1"><span>待发货</span></a></li>
            <li @if($state == 2)class="active" @endif><a href="/order/2"><span>待收货</span></a></li>
            <li @if($state == 5)class="active" @endif><a href="/order/5"><span>已完成</span></a></li>
        </ul>
    </div>
    @if($orders->isEmpty())
        <div class="ShoppingCart">
            <img src="/wx/images/order_null.png">
            <h3 class="info">您还没有相关的订单！</h3>
            <p>可以去看看有哪些想买的~ </p>
            <a class="btnOrange button" href="/">去首页看看</a>
        </div>
    @endif
    <div class="tabBoxGroup">

        {{--全部订单--}}
        <div class="tabBox active">

            @foreach($orders as $order)
                <div class="lineB mb-6">
                    <h3>{{$order->supplier->store_name}}</h3>
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
                                        <p>规格：{{$goods['orderGoods']->spec_name}}<span class="amount">数量{{$goods['orderGoods']->num}}</span></p>
                                        <p>价格：<span class="price">￥{{$goods['orderGoods']->price}}</span></p>
                                    </dd>
                                </dl>
                            </a>
                        </div>
                    @endforeach
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
                    <div class="total">共{{$order->goodsCount}}件商品<span>合计：<b class="price">￥{{$order->priceSum}}</b><b class="freight">({{$order->pay_way}})</b></span></div>

                    @if($order->state == 0)
                    <div class="buttonGroup lineB">
                        <input type="button" value="继续付款" class="btnOrange" onclick="window.location.href='/order/pay?orderno={{$order->order_no}}&'">
                    </div>
                    @endif

                    @if($order->state == 2)
                    <div class="buttonGroup lineB">
                        <input type="button" value="查看物流" onclick="window.location.href='/detail/{{$order->order_no}}'">
                        @if(empty($order->returnOrder) || $order->returnOrder->state == 4)
                            <input type="button" value="申请售后" onclick="window.location.href='/aftersales/{{$order->order_no}}'">

                        @elseif(!empty($order->returnOrder) && $order->returnOrder->state != 4)
                            <input type="button" value="查看售后" onclick="window.location.href='/aftersalesDetail/{{$order->order_no}}'">
                        @endif
                            <input type="button" value="确认收货" class="btnOrange" onclick="finished('{{$order->order_no}}','{{$order->uid}}')">
                    </div>
                    @endif


                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
@section('bottom_bar')
    @include('wx.bottom_bar')
@endsection
@section('javascript')
    <script>
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
                        location.href = '/order/2';
                    }

                }
            });
        }
    </script>
@endsection