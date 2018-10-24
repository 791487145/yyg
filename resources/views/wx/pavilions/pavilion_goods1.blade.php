@extends('wx.layout')
@section('title')
    易游购-地方馆
@endsection
@section('content')
<div class="headerBg">
	<div class="back" onclick="javascript:history.go(-1)"></div>
	<div class="title">{{$pavilion->name}}</div>
</div>
<div>
    <img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$pavilion->background}}" width="100%">
    <p style="padding: 13px;color: #999;">{{$pavilion->description}}</p>
</div>
<div class="goodsListBox">
    @foreach($GoodBases as $GoodBase)
    <div class="goodsList">
    	<a href="/goods/{{$GoodBase->id}}">
	        <dl>
	            <dt>
	                @if(isset($GoodBase->cover_image))
                        <img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$GoodBase->cover_image}}?imageslim">
                    @endif
	            </dt>
	            <dd>
	                <p class="description">{{$GoodBase->title}}</p>
	                <p class="price">
	                	<b>￥{{$GoodBase->price['price']}}</b>
	                	<a href="javascript:void(0)" class="addCart btnIcon {{$GoodBase->cartState}}"></a>
	                	<input type="hidden" value="{{$GoodBase->id}}" name="good_id">
	                </p>
	            </dd>
	        </dl>
         </a>
    </div>
    @endforeach
</div>
@endsection
@section("javascript")
    <script>
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
