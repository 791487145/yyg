@extends('wx.layout')
@section('title')
    查看评价
@endsection
@section('content')
<link rel="stylesheet" type="text/css" href="{{ asset('lib/jQuery.filer/css/jquery.filer.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('lib/jQuery.filer/css/themes/jquery.filer-dragdropbox-theme.css') }}">
<style>
	.myOrder .total{padding:14px  0;}
	.myOrder .fileUpload label{background: url(../wx/images/addIcon.png)no-repeat;background-size: 62px 62px;}
	.linkArrow .arrowicon {
	    display: inline-block;
	    line-height: 44px;
	    height: 44px;
	    color: #999;
	    padding-right: 10px;
	    background: url(../wx/images/right_arrow.png) no-repeat center;
	    background-size: 6px 12px;
	    position: absolute;
	    left:70px;
	    top: 0px;
	}
	.imgBox{overflow: hidden;}
	.imgBox img{width:80px;height:80px;float: left;margin-right: 10px;}
</style>
	<div class="headerBg">
		<div class="back" onclick="javascript:history.go(-1)"></div>
		<div class="title">我的评价</div>
	</div>
    <div class="content myOrder">
        <div class="mb-6">
            <ul class="goodsSpec lineB">
	            <li class="lineB">
	                <a href="/supplier/1" class="linkArrow">
	                    <!-- <span class="yyg-color3">小米001店铺</span> -->
	                    <span class="arrowicon"></span>
	                </a>
	            </li>    
	        </ul>
	    </div>  
	    @foreach($ordergoods as $good)  
        <div class="orderGoods lineB">
            <a href="/detail/{{$good->order_no}}">
            <dl class="info lineB">
                <dt><img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$good->cover_image}}?imageslim"></dt>
                <dd>
                    <p class="name">{{$good->goods_title}}</p>
                    <p>规格：{{$good->spec_name}}<span class="amount">数量x{{$good->num}}</span></p>
                    <p>价格：<span class="price">￥{{$good->price}}</span></p>
                </dd>
            </dl>
            </a>
            <!--  <div class="total">共1件商品<b style="margin-left:20px;" class="price">￥30.00(包邮)</b></div>-->
        </div>
        
        <div class="mb-6">
            <ul class="goodsSpec lineB">
	            <li class="lineB">
	                <a href="/supplier/1" class="linkArrow">
	                    <span class="yyg-color3">评价内容</span>
	                    <span class="fr yyg-color9">{{$good->comments->created_at}}</span>
	                </a>
	            </li>    
	        </ul>
            <div class="lineB commentBox" style="padding:0 14px 20px 14px;background: #fff;">
            	<div class="contentText" style="padding:10px 0;">{{$good->comments->comment}}</div>
            	<div class="commentImgBox imgPopup">
            	    @if(!empty($good->images))
            	       @foreach($good->images as $key=>$img)
            		      <img data-index="{{$key}}" src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$img['image_name']}}?imageslim">
            		   @endforeach
            		@endif
            	</div>
            </div>
        </div>
        @endforeach
    </div>
@endsection