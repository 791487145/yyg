@extends('wx.layout')
@section('title')
    优惠券商品
@endsection
@section('content')
<style type="text/css">
</style>
<div class="padding-15">恭喜您，已成功获取{{$num}}张优惠券！请在我的-<span class="yyg-color">我的优惠券 </span>查看！</div>
<div class="">
	@foreach($CouponUsers as $CouponUser)
	<div class="couponBox">
	    <div class="subLeft">
	    	<span class="price">￥<span class="PriceNum">{{number_format($CouponUser->amount_coupon,0)}}</span></span><br>
	        <span>满{{number_format($CouponUser->amount_order,0)}}可用</span>
	    </div>
	    <div class="decorate">
	    	<span style="top:-13px;"></span>
	    	<span style="bottom:-20px;"></span>
	    </div>
        <div class="subRight">
        	<div class="title">
        		仅指定商品<span style="font-size: 16px;">可用</span>
				@if($CouponUser->state == 0)
					<a class="but" href="/CouponGoods/{{$CouponUser->coupon_id}}">立即使用</a>
				@elseif($CouponUser->state == 1)
					<span class="coupon-end"></span>
				@else
					<span class="coupon-overdue"></span>
				@endif
	        </div>
        	<div class="yyg-color9"><span>有效期</span>{{date("Y.m.d",strtotime($CouponUser->start_time))}}-{{date("Y.m.d",strtotime($CouponUser->end_time))}}</div>
        </div>
    </div>
	@endforeach
</div>
<h2 class="text-c padding-15 yyg-bgf">优惠券商品</h2>
<div class="goodsListBox mt-10">
	@foreach($CouponGoodsBases as $v)
		@foreach($v as $couponGoodsBase)
    <div class="goodsList">
    	<a href="/goods/{{$couponGoodsBase->id}}">
	        <dl>
	            <dt>
				@if($couponGoodsBase->num < 1)
	            	<div class="goodsState">已售罄</div>
				@endif
					<img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$couponGoodsBase->cover_image}}?imageslim">
	            </dt>

	            <dd>
	                <p class="description">{{$couponGoodsBase->title}}</p>
	                <p class="price">
	                	<b>￥{{$couponGoodsBase->price}}</b>
	                	<a href="javascript:void(0)" class="addCart btnIcon {{$couponGoodsBase->cartState}}"></a>
	                	<input type="hidden" value="{{$couponGoodsBase->id}}" name="good_id">
	                </p>
	            </dd>
	        </dl>
        </a>
    </div>
		@endforeach
    @endforeach

</div>
@endsection
@section('javascript')
<script type="text/javascript">
$(function(){
    $('.addCart').click(function(){
    	var thisClass = $(this);
        var val = $(this).next().val();
        $.post('/carts',{good_id:val,open_id:"{{Cookie::get('openid')}}"},function(msg){
            if(msg.ret == 'yes'){
                var info = '加入购物车成功';
                thisClass.addClass("btnIconChecked");
                information(info);
            }
        })
    })
})
</script>
@endsection