@extends('wx.layout')
@section('title')
    优惠卷商品
@endsection
@section('content')
<style type="text/css">
</style>
<div class="padding-15">恭喜您，已成功获取2张优惠券！请在我的-<span class="yyg-color">我的优惠券</span>查看！</div>
<div class="">
	
	<div class="couponBox">
	    <div class="subLeft">
	    	<span class="price">￥<span class="PriceNum">3</span></span><br>
	        <span>满10.00可用</span>
	    </div>
	    <div class="decorate">
	    	<span style="top:-13px;"></span>
	    	<span style="bottom:-20px;"></span>
	    </div>
        <div class="subRight">
        	<div class="title">
        		仅指定商品<span style="font-size: 16px;">可用</span>
	        	<a class="but" href="/CouponGoods/5">立即使用</a>
	        </div>
        	<div class="yyg-color9"><span>有效期</span>2017.08.03-1970.01.01</div>
        </div>
    </div>
    <div class="couponBox">
	    <div class="subLeft">
	    	<span class="price">￥<span class="PriceNum">3</span></span><br>
	        <span>满10.00可用</span>
	    </div>
	    <div class="decorate">
	    	<span style="top:-13px;"></span>
	    	<span style="bottom:-20px;"></span>
	    </div>
        <div class="subRight">
        	<div class="title">
        		仅指定商品<span style="font-size: 16px;">可用</span>
	        	<a class="but" href="/CouponGoods/5">立即使用</a>
	        </div>
        	<div class="yyg-color9"><span>有效期</span>2017.08.03-1970.01.01</div>
        </div>
    </div>
</div>
<h2 class="text-c padding-15 yyg-bgf">优惠券商品</h2>
<div class="goodsListBox mt-10">
	
    <div class="goodsList">
    	<a href="/goods/84">
	        <dl>
	            <dt>
	            	<div class="goodsState">已售罄</div>
	                <img src="https://img2.yyougo.com/8327736eecc1ef95b564.jpeg?imageslim">
	            </dt>
	            <dd>
	                <p class="description">测试商品测试商品测试商品测试商品测试商品测试商品测试商品</p>
	                <p class="price">
	                	<b>￥200.00</b>
	                	<a href="javascript:void(0)" class="addCart btnIcon"></a>
	                	<input type="hidden" value="84" name="good_id">
	                </p>
	            </dd>
	        </dl>
        </a>
    </div>
    <div class="goodsList">
    	<a href="/goods/84">
	        <dl>
	            <dt>
	                <img src="https://img2.yyougo.com/8327736eecc1ef95b564.jpeg?imageslim">
	            </dt>
	            <dd>
	                <p class="description">测试商品测试商品测试商品测试商品测试商品测试商品测试商品</p>
	                <p class="price">
	                	<b>￥200.00</b>
	                	<a href="javascript:void(0)" class="addCart btnIcon"></a>
	                	<input type="hidden" value="84" name="good_id">
	                </p>
	            </dd>
	        </dl>
        </a>
    </div>
    <div class="goodsList">
    	<a href="/goods/84">
	        <dl>
	            <dt>
	                <img src="https://img2.yyougo.com/8327736eecc1ef95b564.jpeg?imageslim">
	            </dt>
	            <dd>
	                <p class="description">测试商品测试商品测试商品测试商品测试商品测试商品测试商品</p>
	                <p class="price">
	                	<b>￥200.00</b>
	                	<a href="javascript:void(0)" class="addCart btnIcon"></a>
	                	<input type="hidden" value="84" name="good_id">
	                </p>
	            </dd>
	        </dl>
        </a>
    </div>
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