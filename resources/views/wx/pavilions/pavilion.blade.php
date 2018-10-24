@extends('wx.layout')
@section('title')
    更多分馆
@endsection
@section('content')
<div class="headerBg fixedHead text-c">
	<p class="padding-5">
	    <a href="/category" class="button-main">分类</a>
	    <a href="/pavilions" class="button-default">地方馆</a>
	</p>
</div>
<div class="banner" style="margin-top:40px;">
    <div class="swiper-container">
        <div class="swiper-wrapper">
            @foreach($pavilionBanners as $pavilionBanner)
                <div class="swiper-slide">
                    @if($pavilionBanner->url_type == 1)
                        <a href="/goods/{{$pavilionBanner->url_content}}">
                            <img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$pavilionBanner->cover}}?imageslim">
                        </a>
                    @endif
                    @if($pavilionBanner->url_type == 0)
                        <a href="{{$pavilionBanner->url_content}}">
                            <img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$pavilionBanner->cover}}?imageslim">
                        </a>
                    @endif
                </div>
            @endforeach
        </div>
        <div class="pagination"></div>
    </div>
</div>
<div class="branchList">
    @foreach($Pavilions as $Pavilion)
    <h3 class="lineT"><span>{{$Pavilion->name}}</span></h3>
    <a href="/pavilion/{{$Pavilion->id}}"><img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$Pavilion->new_cover}}?imageslim"></a>
    <ul>
            @foreach($Pavilion->tag as $tag)
                <li><a href="/goods/{{$tag->goods_id}}">{{$tag->name}}</a></li>
            @endforeach

    </ul>
    @endforeach
</div>
@endsection
