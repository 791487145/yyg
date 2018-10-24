@extends('wx.layout')
@section('title')

@endsection
@section('content')
<div class="sellerWrap">
    <div class="mb-6" style="position: relative;">
    	<img class="bgImg" src="/wx/images/storebg.jpg"/>
    	<div class="sellerInfo">
			<img class="name" src="@if($guidesstore['avatar']){{env('IMAGE_DISPLAY_DOMAIN')}}<?php echo $guidesstore['avatar']?>@else /images/user.png @endif ">
			<div class="title"><?php echo $guidesstore['real_name']?></div>
		</div>
	</div>
    <div>
        <h2 style="background: none;margin: 10px 14px;">店铺精选</h2>
        <div class="goodsListBox swiper-container">
        @if(empty($goodslists))
        @else
        
            @foreach($goodslists as $goodsList)
                <div class="goodsList">
			    	<a href="/goods/{{$goodsList->id}}">
				        <dl>
				            <dt>
                                @if($goodsList->state == 2)
                                    <div class="goodsState">已下架</div>
                                @elseif($goodsList->num <= 0)
                                    <div class="goodsState">已售罄</div>
                                @endif
			                    <img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$goodsList->first_image}}?imageslim">
				            </dt>
				            <dd>
				                <p class="description">{{$goodsList->title}}</p>
				                <p class="price" style="float:none;">
				                	<b>￥{{$goodsList->price}}</b>
				                	<a href="javascript:void(0)" class="addCart btnIcon {{$goodsList->cartState}}"></a>
				                	<input type="hidden" value="{{$goodsList->id}}" name="good_id">
				                </p>
				            </dd>
				        </dl>
			         </a>
			    </div>
            @endforeach
        @endif
		</div>
	    <div id="loding">正在加载。。。</div>
	    <input type="hidden" id="page" value="1">
        <input type="hidden" id="ids" value="{{$id}}">
    </div>
</div>
@endsection
@section("javascript")
    <script>

        addCart();
        function addCart(){
            $('.addCart').unbind().click(function(){
                var thisClass = $(this);
                var val = $(this).next().val();
                $.post('/carts',{good_id:val,open_id:"{{Cookie::get('openid')}}"},function(msg){
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
                    if(msg.ret == 'down'){
                        var info = '商品已下架';
                        information(info);
                    }
                })
            })
        };

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
            var id = $("#ids").val();;
            $.ajax({
                url:"/guideGoodPage",
                dataType:"json",
                async:false,
                data:{"pageNum":pageNum,"id":id,"open_id":"{{Cookie::get('openid')}}"},    //参数值
                type:"POST",   //请求方式
                success:function(msg){
                    $("#loding").hide();
                    if(msg.ret == 'no'){
                        return false;
                    }
                    var html = '';
                    $(msg.GoodBases).each(function(i,val){
                        var state = '';
                        if(val.num <= 0){
                            state = '<div class="goodsState">已售罄</div>';
                        }
                        if(val.state == 2){
                            state = '<div class="goodsState">已下架</div>';
                        }
                        html +='<div class="goodsList">' +
                                    '<a href="/goods/'+val.id+'">' +
                                        '<dl>' +
                                            '<dt>'+state+'<img src="'+imgurl+val.first_image+'?imageslim"></dt>' +
                                            '<dd><p class="description">'+val.title+'</p>' +
                                                 '<p class="price" style="float:none;"><b>￥'+val.price+'</b><a href="javascript:void(0)" class="addCart btnIcon '+val.cartState+'"></a><input type="hidden" value="'+val.id+'" name="good_id"></p>'+
                                            '</dd>'+
                                        '<dl>' +
                                    '</a>' +
                                '</div>'
                    })
                    pageNum = msg.page_num*1 + 1;
                    $("#page").val(pageNum);
                    $(".goodsListBox").append(html);
                    addCart();
                }
            });
        }

    </script>
@endsection

