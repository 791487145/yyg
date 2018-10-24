@extends('wx.layout')
@section('title')
    搜索
@endsection
@section('content')
<style>
	.searchList .keyWords{display: inline-block;border: 0;width: 100%;margin: 0;text-align: left;}
</style>
    <div class="fixedHead">
        <div class="headerSearch headerBg">
            <div class="searchInput">
                <form>
                    <label class="search"><input type="search" class="searchBut" placeholder="输入您想搜索的内容..." name="title" value="{{$param['name']}}"></label>
                    <label class="button"><input type="submit" value="搜索"></label>
                </form>
            </div>
        </div>
        
       @if($param['name'] != '')
        <div class="rankButton lineB">
            <form>
                <input type="hidden" name="title" value="{{$param['name']}}">
                <input type="hidden" name="display_state" value="{{$display_state}}">
                <span onclick="a()"  class="{{($display_state == 0) ? 'active' : ''}}"><input type="submit" value="按照销量高低"><i></i></span>
                <span onclick="b()" class="{{($display_state == 1) ? 'active' : ''}}"><input type="submit" value="按人气高低"><i></i></span>
            </form>
        </div>
        @endif
    </div>
@if($param['name'] == '')
    <div class="content searchList" style="margin-top:40px;">
    	@if($param['name'] == '')
        <div >
            <ul>
                @foreach($keyWords as $keyWord)
                    <li class="lineB"><a class="keyWords" href="goods/{{$keyWord->url}}">{{$keyWord->name}}</a></li>
                @endforeach
            </ul>
        </div>
        @endif
        <div class="lineB">
            <h2 class="lineB">历史搜索</h2>
            <ul>
                @if(!empty($q))
                    @foreach($q as $val)
                        <li class="lineB">{{$val}}</li>
                    @endforeach
                @endif
            </ul>
            <a href="javascript:void(0)" onclick="del()">清空历史记录</a>
        </div>
    </div>
@endif
    @if($param['name'] != '')
		<div class="goodsListBox" style="margin-top: 80px;">
		    @foreach($GoodBases as $GoodBase)
		    <div class="goodsList">
		    	<a href="/goods/{{$GoodBase->id}}">
			        <dl>
			            <dt>
                            @if($GoodBase->spec_num <= 0)
                                <div class="goodsState">已售罄</div>
                            @endif
                            <img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$GoodBase->cover_image}}?imageslim">
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
        <div class="winTip addCartTip"><span>加入购物车成功</span></div>

@endif



@endsection

@section("javascript")
    <script>
        function a(){
            $("input[name = 'display_state']").val(0);
        }
        function b(){
            $("input[name = 'display_state']").val(1);
        }
        function del(){
            $.get('/search',{data:1},function(mag){
                $('ul').remove();
            })
        }
    </script>

    <script>
        $(function(){
            $('.addCart').bind("click",function(){
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
    </script>
@endsection