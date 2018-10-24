@extends('wx.layout')
<?php
/*require_once(app_path().'/Lib/Wx/jssdk.php');
$jssdk = new JSSDK(env("WX_APPID"), env("WX_APPSECRET"));
$signPackage = $jssdk->GetSignPackage();
*/?>
@section('title')
    {{$ConfPavilion->name}}
@endsection

@section('content')
<div class="headerSearch headerBg" style="position: fixed;top: 0;left: 0;right:0;z-index: 9999;">
    @include('wx.search_punblic')
</div>
<div class="content" style="margin-top: 40px;">
    <div class="banner">
        <div class="swiper-container">
            <div class="swiper-wrapper">
                @foreach($ConfBanners as $ConfBanner)
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
    <div class="branchGroup lineB mb-6">
        @foreach($ConfPavilions as $confPavilion)
        <dl><a href="/pavilion/{{$confPavilion->id}}"><dt><img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$confPavilion->cover}}"></dt><dd>{{$confPavilion->name}}</dd></a></dl>
        @endforeach
        @if(count($ConfPavilions) == 9)
        <dl><a href="/pavilions"><dt><img src="/wx/images/more.png"></dt><dd>更多</dd></a></dl>
        @endif
    </div>
    @if(!$ConfThemes->isEmpty())
    <div class="wellChosen mb-6">
        <h2>精选专题</h2>
        @foreach($ConfThemes as $confTheme)
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
    @if(!$GoodBases->isEmpty())
    <div class="wellChosen mb-6">
        <h2>精选推荐</h2>
        @foreach($GoodBases as $goodBase)
            <div class="box lineB">
                <a href="/goods/{{$goodBase->id}}">
                	<div class="yyg-position-r">
	                	@if($goodBase->num <= 0)
	                        <span class="goodsState">已售罄</span>
	                    @endif
	                    <img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$goodBase->cover}}">
                    </div>
                    <p class="description">{{$goodBase->title}}</p>
                </a>
                <p>
                	<span style="margin:0 14px;">原价：￥<span class="text-line market">{{$goodBase->price_market}}</span></span>
                	<span>库存：{{$goodBase->num}}件</span>
                	<span class="price">￥{{$goodBase->price}}</span>
                	@if($goodBase->num <= 0)
                        <a href="javascript:void(0)" class="btnIcon {{$goodBase->cartState}}" ></a>
                    @else
                    	<a href="javascript:void(0)" class="addCart btnIcon {{$goodBase->cartState}}" ></a>
                    @endif
                	<input type="hidden" value="{{$goodBase->id}}" name="good_id">
                </p>
            </div>
        @endforeach
    </div>

    @endif
    <div style="text-align: center;padding:20px;"><a style="padding: 4px 10px;border-radius: 5px;border: 1px solid #ED6B09; color: #ED6B09;" href="/pavilion/{{$ConfPavilion->id}}">查看更多</a></div>
</div>
<input type="hidden" value="{{$ConfPavilion->id}}" class="pavilion_id">
<input type="hidden" value="" class="pavilion_name">

@endsection
@section('bottom_bar')
@include('wx.bottom_bar')
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