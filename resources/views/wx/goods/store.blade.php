@extends('wx.layout')
@section('title')
    店铺
@endsection
@section('content')
<div class="headerBg">
	<div class="back" onclick="javascript:history.go(-1)"></div>
	<div class="title"><?php echo $supplierGoodsLists['store_name']?></div>
</div>
<div class="sellerWrap">
	<div style="position: relative;">
		<img class="bgImg" src="/wx/images/storebg.jpg"/>
		<div class="sellerInfo" style="background: none;padding: 0;position: absolute;top:0;left:0;right: 0;top:15px;">
			<img class="name" src="@if($supplierGoodsLists['avatar']){{env('IMAGE_DISPLAY_DOMAIN')}}<?php echo $supplierGoodsLists['avatar']?>@else /images/user.png @endif ">
			<div class="title"><?php echo $supplierGoodsLists['store_name']?></div>
		</div>
	</div>
    <h2 style="background: none;margin: 10px 14px;">店铺精选</h2>
    <div class="goodsListBox swiper-container">
	    @foreach($goodsLists as $goodsList)
	    <div class="goodsList">
	    	<a href="/goods/{{$goodsList->id}}">
		        <dl>
		            <dt>
	                    <img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$goodsList->first_image}}?imageslim">
		            </dt>
		            <dd>
		                <p class="description">{{$goodsList->title}}</p>
		                <p class="price">
		                	<b>￥{{$goodsList->price}}</b>
		                	<a href="javascript:void(0)" class="addCart btnIcon {{$goodsList->cartState}}"></a>
		                	<input type="hidden" value="{{$goodsList->id}}" name="good_id">
		                </p>
		            </dd>
		        </dl>
	         </a>
	    </div>
	    @endforeach
    </div>
    <div id="loding">正在加载。。。</div>
    <input type="hidden" id="page" value="1">
</div>
@endsection
@section("javascript")
    <script>
        $(function(){
            $('.addCart').click(function(){
            	var thisClass = $(this);
                var val = $(this).next().val();
                $.ajax({
                    url:"/carts",    //请求的url地址
                    dataType:"json",   //返回格式为json
                    async:false,//请求是否异步，默认为异步，这也是ajax重要特性
                    data:{"good_id":val,"open_id":"{{Cookie::get('openid')}}"},    //参数值
                    type:"POST",   //请求方式
                    success:function(msg){
                        if(msg.ret == 'yes'){
                            var info = '加入购物车成功';
                            thisClass.addClass("btnIconChecked");
                            cartNum(msg.count)
                            information(info);
                        }
                        if(msg.ret == 'no'){
                            var info = '数量超过上限';
                            information(info);
                        }
                    }
                });
            })
        })
        var mySwiper = new Swiper('.swiper-container',{
            onTouchEnd: function(swiper){
                var scrollTop = $(document).scrollTop();
                var height = $(document).height()-$(window).height();
                if (scrollTop==height||scrollTop>height) {
                    getpage();
                }
            }
        })
        var imgurl = "{{env('IMAGE_DISPLAY_DOMAIN')}}";
        function getpage(){
            $("#loding").show();
            var pageNum = $("#page").val();
            $.post('/supplierGoodPage',{pageNum:pageNum,id:{{$supplierGoodsLists['id']}}},function(msg){
                $("#loding").hide();
                if(msg.ret == 'no'){
                    return false;
                }
                var html = '';
                $(msg.GoodBases).each(function(i,val){
                    html +='<div class="goodsList">' +
                                '<a href="/goods/'+val.id+'">' +
                                    '<dl>' +
                                        '<dt><img src="'+imgurl+val.first_image+'?imageslim"></dt>' +
                                        '<dd><p class="description">'+val.title+'</p>' +
                                            '<p class="price"><b>￥'+val.goodsspec[0].price+'</b><a href="javascript:void(0)" class="addCart btnIcon '+val.cartState+'"></a><input type="hidden" value="'+val.id+'" name="good_id"></p>'+
                                        '</dd>'+
                                    '<dl>' +
                                '</a>' +
                            '</div>'
                })
                pageNum = msg.page_num*1 + 1;
                $("#page").val(pageNum);
                $(".goodsListBox").append(html);
            })
        }

    </script>
@endsection

