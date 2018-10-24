@extends('wx.layout')
@section('title')
    收藏列表
@endsection
<style>
	.delete{text-align: center;border-top:1px solid #EEEEEE;color: #999;}
</style>
@section('content')
<div class="headerBg">
	<div class="back" onclick="javascript:history.go(-1)"></div>
	<div class="title">收藏列表</div>
</div>
    @if($goodslists->isEmpty())
        <div class="ShoppingCart">
            <img src="/wx/images/collect_null.png">
            <h3 class="info">您还没有收藏的宝贝</h3>
            <p>可以去看看有哪些想买的~ </p>
        </div>
    @endif
<div class="goodsListBox swiper-container">
    @foreach($goodslists as $goodslist)
    <div class="goodsList goodsList{{$goodslist->id}}">
    	<a href="/goods/{{$goodslist->id}}">
	        <dl>
	            <dt>
	                @if(isset($goodslist->cover_image))
                        @if($goodslist->state == 2)
                            <div class="goodsState">已下架</div>
                        @elseif($goodslist->num <= 0)
                            <div class="goodsState">已售罄</div>
                        @endif
                        <img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$goodslist->cover_image}}?imageslim">
                    @endif
	            </dt>
	            <dd>
	                <p class="description">{{$goodslist->title}}</p>
	                <p class="price">
	                	<b>￥{{$goodslist->price}}</b>
	                	<a href="javascript:void(0)" class="addCart btnIcon {{$goodslist->cartState}}"></a>
	                	<input type="hidden" value="{{$goodslist->id}}" name="good_id">
	                </p>
	            </dd>
	            <dd class="delete" data-id="{{$goodslist->id}}" onclick="popupShow({{$goodslist->id}})">删除</dd>
	        </dl>
         </a>
    </div>
    @endforeach
</div>
<div id="loding" style="text-align: center;margin:15px 0;width: 100%;display: none;">正在加载。。。</div>

<input type="hidden" id="page" value="1">
<div class="popupBg"></div>
<div class="popupWrap confirmPopup">
    <p>删除收藏</p>
    <p>确定需要移除该商品？</p>
    <div class="bottomButtonGroup lineT lineR">
        <input type="hidden" value="" class="good_id">
        <button class="close button cancel">取消</button>
        <button class="close button done">确定</button>
    </div>
</div>
@endsection
@section('javascript')
<script type="text/javascript">
	$(".done").click(function(){
        var id = $(".good_id").val();
        $.post('/collection',{id:id},function(msg){
            var info = '已删除';
            information(info);
            $(".goodsList"+id).remove();
        })
   })
    function popupShow(id){
    	var box = 'confirmPopup';
        popupHide();
        $(".popupBg,.popupWrap").hide();
        $(".popupBg").show();
        $(".good_id").val(id);
        $("."+box).css("margin-top",-$("."+box).height()/2+"px").show();
    }

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
        $.ajax({
            url:"/collectLimit",
            dataType:"json",
            async:false,
            data:{"pageNum":pageNum},    //参数值
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
                                        '<dt>'+state+'<img src="'+imgurl+val.cover_image+'"></dt>' +
                                        '<dd><p class="description">'+val.title+'</p>' +
                                            '<p class="price"><b>￥'+val.goodsspec[0].price+'</b><a href="javascript:void(0)" class="addCart btnIcon '+val.cartState+'"></a><input type="hidden" value="'+val.id+'" name="good_id"></p>'+
                                        '</dd>'+
                                        '<dd class="delete" data-id="'+val.id+'" onclick="popupShow('+val.id+')">删除</dd>'+
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

</script>
@endsection