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
                    <img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$goodBase->cover}}">
                    <p class="description">{{$goodBase->title}}</p>
                </a>
                <p>
                	<span style="margin:0 14px;">原价：￥<span class="text-line market">{{$goodBase->price_market}}</span></span>
                	<span>库存：{{$goodBase->num}}件</span>
                	<span class="price">￥{{$goodBase->price}}</span>
                	<a href="javascript:void(0)" class="addCart btnIcon {{$goodBase->cartState}}" ></a>
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
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
    <script>
        $(function(){
            $('.addCart').bind("click",function(){
               var val = $(this).next().val();
               var thisClass = $(this);
                $.post('/carts',{good_id:val,open_id:"{{Cookie::get('openid')}}"
                },function(msg){
                    if(msg.ret == 'yes'){
                        var info = '加入购物车成功';
                        thisClass.addClass('btnIconChecked');
                        cartNum(msg.count)
                        information(info);
                    }
                    if(msg.ret == 'no'){
                        var info = '数量超过上限';
                        information(info);
                    }
                })
            })
        })

        wx.config({
            debug: false,
            appId: '<?php echo $signPackage["appId"];?>',
            timestamp: '<?php echo $signPackage["timestamp"];?>',
            nonceStr: '<?php echo $signPackage["nonceStr"];?>',
            signature: '<?php echo $signPackage["signature"];?>',
            jsApiList: [
                'checkJsApi',
                'openLocation',
                'getLocation'
            ]
        });

        wx.ready(function(){
            wx.getLocation({
                success: function (res) {
                    var latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
                    var longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。
                    var speed = res.speed; // 速度，以米/每秒计
                    var accuracy = res.accuracy; // 位置精度
                    $.post('/city',{lat:latitude,lon:longitude},function(msg){
                        if(msg.ret == "no"){
                            var conten = "系统定位到您在"+msg.province+"，该地方未开通地方馆，已为您切换至乡亲直供馆！";
                            addCity("乡亲直供馆",conten);
                        }
                        if(msg.ret == "yes"){
                            var conten = "系统定位到您在"+msg.province+"，需要切换至"+msg.content.name+"吗？";
                            $(".pavilion_name").val( msg.content.name);
                            $(".pavilion_id").val( msg.content.id);
                            addCity(msg.province,conten);
                        }
                    })
                },
                cancel: function (res) {
                    var conten = "系统检测到您的定位权限未打开，暂定位不到所属地方馆，已默认为您切换至乡亲直供馆！";
                    addCity("乡亲直供馆",conten);
                },
            });
        });
       function addCity(city,conten){
           alertPopup({"title":"提示","conten":conten},function(){
               var pavilionID = $(".pavilion_id").val();
               location.href='/pavilions/'+pavilionID+'abc';
           });
       }
    </script>
@endsection