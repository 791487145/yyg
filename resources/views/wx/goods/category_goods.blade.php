@extends('wx.layout')
@section('title')
    分类
@endsection
@section('content')
<div class="fixedHead classifyHead">
    <div class="headerBg text-c">
    	<p class="padding-5">
		    <a href="/category" class="button-default">分类</a>
		    <a href="/pavilions" class="button-main">地方馆</a>
    	</p>
	</div>
    <div class="tabButton">
        <ul class="lineT lineB">
            <li class="{{($param['category_id'] == 0) ? 'active': ''}}"><a href="/category"><span>全部美食</span></a></li>
            @foreach($ConfCategorys as $ConfCategory)
                <li class="{{($param['category_id'] == $ConfCategory->id) ? 'active': ''}}"><a href="/category/{{$ConfCategory->id}}"><span>{{$ConfCategory->name}}</span></a></li>
            @endforeach
        </ul>
    </div>
</div>
<div class="content classifyCon">
    <div class="rankButton lineB">
        <span><a class="{{($param['display_state'] == 0) ? 'active' : ''}}" href="/category/{{$param['category_id']}}/0"> 按照销量高低<i></i></a></span>
        <span><a class="{{($param['display_state'] == 1) ? 'active' : ''}}" href="/category/{{$param['category_id']}}/1">按人气高低 <i></i></a></span>
    </div>
    @if($GoodBases->isEmpty())
        @include('wx.goods.category_null')
    @endif
    <div class="goodsListBox swiper-container">
	    @foreach($GoodBases as $GoodBase)
	    <div class="goodsList">
	    	<a href="/goods/{{$GoodBase->id}}">
		        <dl>
		            <dt>
		                @if(isset($GoodBase->cover_image))
                            @if($GoodBase->num <= 0)
                                <div class="goodsState">已售罄</div>
                            @endif
	                        <img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$GoodBase->cover_image}}?imageslim" />
	                    @endif
		            </dt>
		            <dd>
		                <p class="description">{{$GoodBase->title}}</p>
		                <p class="price">
		                	<b>￥{{$GoodBase->price}}</b>
		                	<a href="javascript:void(0)" class="addCart btnIcon {{$GoodBase->cartState}}"></a>
		                	<input type="hidden" value="{{$GoodBase->id}}" name="good_id">
		                </p>
		            </dd>
		        </dl>
	         </a>
	    </div>
	    @endforeach

    </div>
    <div id="loding" style="text-align: center;margin:15px 0;width: 100%;display: none;">正在加载。。。</div>
    <input type="hidden" id="page" value="1">


</div>
@endsection
@section('bottom_bar')
    @include('wx.bottom_bar')
@endsection
@section("javascript")
    <script>

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
            var categoty = "{{$param['category_id']}}";
            var display_state = "{{$param['display_state']}}";
            var pageNum = $("#page").val();
            $.ajax({
                url:"/category",
                dataType:"json",
                async:false,
                data:{"category_id":categoty,"display_state":display_state,"pageNum":pageNum},    //参数值
                type:"POST",   //请求方式
                success:function(msg){
                    $("#loding").hide();
                    if(msg.ret == 'no'){
                        return false;
                    }
                    var html = '';
                    $(msg.GoodBases).each(function(i,val){
                        if(val.num <= 0){
                         var state = '<div class="goodsState">已售罄</div>';
                         }else{
                         var state = '';
                         }
                        html +='<div class="goodsList">' +
                                    '<a href="/goods/'+val.id+'">' +
                                        '<dl>' +
                                            '<dt>'+state+'<img src="'+imgurl+val.cover_image+'?imageslim"></dt>' +
                                            '<dd><p class="description">'+val.title+'</p>' +
                                                '<p class="price"><b>￥'+val.price+'</b><a href="javascript:void(0)" class="addCart btnIcon '+val.cartState+'"></a><input type="hidden" value="'+val.id+'" name="good_id"></p>'+
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

        addCart();
        function addCart(){
            $('.addCart').unbind().click(function(){
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
        };

    </script>
@endsection
