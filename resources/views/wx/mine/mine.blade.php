@extends('wx.layout')
@section('title')
    个人中心
@endsection
@section('content')
    <link rel="stylesheet" type="text/css" href="{{ asset('lib/uploadify/uploadify.css') }}">
<div class="content mineIndex">
    <div class="mineTop mb-6">
        <a href="javascipt:void(0)" onclick="popupShow('customeService')" class="left">客服</a>
        <a href="/setting/" class="right"></a>
        <dl class="info">
            <dt><img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{env('DEFAULT_AVATAR')}}"></dt>
            <dd>
                @if(empty($username['nick_name']))
                    {{$username['mobile']}}
                @else
                   {{$username['nick_name']}}
                @endif
            </dd>
        </dl>
    </div>
   {{-- <div class="balance lineB mb-6"><a href="balance.html">可用余额<span class="price">￥24.78</span></a></div>--}}
   {{-- <div class="income lineB lineL">
        <a href="income1.html"><dl><dt>待入账收益</dt><dd>￥<span>244.50</span></dd></dl></a>
        <a href="income2.html"><dl><dt>累计收益</dt><dd>￥<span>1450.21</span></dd></dl></a>
    </div>--}}
    <ul class="mt-10 mineLink">
        <li class="lineB"><a href="/order/" class="linkArrow">我买入的订单<span class="arrow"></span></a></li>
        <li class="lineB"><a href="/aftersales/" class="linkArrow">退款／售后<span class="arrow"></span></a></li>
        <li class="lineB"><a href="javascipt:void(0)" onclick="popupShow('customeService')" class="linkArrow">我的客服<span class="arrow"></span></a></li>
        <li class="lineB"><a href="/collection/" class="linkArrow">我的收藏<span class="arrow"></span></a></li>
        <li class="lineB"><a href="/coupon/0" class="linkArrow">我的优惠券&emsp;@if($couponNum != 0)<span class="yyg-num-radius">{{$couponNum}}</span>@endif<span class="arrow"></span></a></li>
		<li class="lineB"><a href="{{url('/mine/list')}}" class="linkArrow">我的评价<span class="arrow"></span></a></li>

    </ul>
</div>
<div class="popupBg"></div>
<div class="popupWrap customeService">
    <h3>拨打客服</h3>
    <p>400-9158-971</p>
    <div class="bottomButtonGroup lineT lineR">
        <a href="javascript:void(0)" class="close button">取消</a>
        <a href="tel:400-9158-971" class="button">确定</a>
    </div>
</div>
@section('bottom_bar')
    @include('wx.bottom_bar')
@endsection