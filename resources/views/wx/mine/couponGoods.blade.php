@extends('wx.layout')
@section('title')
    促销指定商品
@endsection
<style>
	
</style>
@section('content')
<div class="headerBg">
	<div class="back" onclick="javascript:history.go(-1)"></div>
	<div class="title">促销指定商品</div>
</div>
<div class="goodsListBox swiper-container">
    @foreach($GoodsBases as $GoodsBase)
    <div class="goodsList goodsList">
    	<a href="/goods/{{$GoodsBase->id}}">
	        <dl>
	            <dt>
	                @if(isset($GoodsBase->cover_image))
                        @if($GoodsBase->num < 1)
                        <div class="goodsState">已售罄</div>
                        @endif
                        <img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$GoodsBase->cover_image}}?imageslim">
                    @endif
	            </dt>
	            <dd>
	                <p class="description">{{$GoodsBase->title}}</p>
	                <p class="price">
	                	<b>￥{{$GoodsBase->price}}</b>
	                	<a href="javascript:void(0)" class="addCart btnIcon {{$GoodsBase->cartState}}"></a>
	                	<input type="hidden" value="{{$GoodsBase->id}}" name="good_id">
	                </p>
	            </dd>
	        </dl>
         </a>
    </div>
    @endforeach
</div>

@endsection
@section('javascript')
<script type="text/javascript">
    $('.addCart').click(function(){
        var thisClass = $(this);
        var val = $(this).next().val();
        $.post('/carts',{good_id:val,open_id:"{{Cookie::get('openid')}}"},function(msg){
            if(msg.ret == 'yes'){
                thisClass.addClass("btnIconChecked");
                cartNum(msg.count)
                information('加入购物车成功');
            }
            if(msg.ret == 'no'){
                information('数量超过上限');
            }
        })
    })
</script>
@endsection