@extends('wx.layout')

@section('title')
    {{$pavilion->name}}
@endsection

@section('content')
    <div class="headerBg">
        <div class="back" onclick="javascript:history.go(-1)"></div>
        <div class="title">{{$pavilion->name}}</div>
    </div>
    <div class="content">
        <div class="banner">
            <div class="swiper-container">
                <div class="swiper-wrapper">
                    @foreach($pavilion->banners as $ConfBanner)
                        @if($ConfBanner->url_type == 1)
                            <div class="swiper-slide"><a href="/goods/{{$ConfBanner->url_content}}"><img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$ConfBanner->cover}}"/></a></div>
                        @endif
                        @if($ConfBanner->url_type == 0)
                            <div class="swiper-slide"><a href="{{$ConfBanner->url_content}}"><img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$ConfBanner->cover}}"/></a></div>
                        @endif
                    @endforeach
                </div>
                <div class="pagination"></div>
            </div>
        </div>
        @if(!$pavilion->themes->isEmpty())
            <div class="wellChosen mb-6">
                <h2>精选专题</h2>
                @foreach($pavilion->themes as $confTheme)
                    <div class="box lineB">
                        @if($confTheme->url_type == 1)
                            <div class="swiper-slide"><a href="/goods/{{$confTheme->url}}"><img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$confTheme->cover}}"/></a></div>
                        @endif
                        @if($confTheme->url_type == 0)
                            <div class="swiper-slide"><a href="{{$confTheme->url}}"><img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$confTheme->cover}}"/></a></div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
        <div class="goodsListBox">
            <h2>精选推荐</h2>
            @foreach($GoodBases as $GoodBase)
                <div class="goodsList">
                    <a href="/goods/{{$GoodBase->id}}">
                        <dl>
                            <dt>
                                @if(isset($GoodBase->cover_image))
                                    @if($GoodBase->spec_num <= 0)
                                        <div class="goodsState">已售罄</div>
                                    @endif
                                    <img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$GoodBase->cover_image}}?imageslim">
                                @endif
                            </dt>
                            <dd>
                                <p class="description">{{$GoodBase->title}}</p>
                                <p class="price">
                                    <b>￥{{$GoodBase->price}}</b>
                                    @if($GoodBase->spec_num <= 0)
                                    <a href="javascript:void(0)" class="btnIcon {{$GoodBase->cartState}}"></a>
                                    @else
                                    <a href="javascript:void(0)" class="addCart btnIcon {{$GoodBase->cartState}}"></a>
                                    @endif
                                    <input type="hidden" value="{{$GoodBase->id}}" name="good_id">
                                </p>
                            </dd>
                        </dl>
                    </a>
                </div>
            @endforeach
        </div>
    </div>


@endsection
@section("javascript")
    <script>
        $(function(){
            $('.addCart').bind("click",function(){
                var val = $(this).next().val();
                var thisClass = $(this);
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
                            var info = '库存不足';
                            information(info);
                        }
                    }
                });
            })
        })
    </script>
@endsection