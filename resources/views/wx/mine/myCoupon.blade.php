@extends('wx.layout')
@section('title')
    我的优惠券
@endsection
@section('content')
<style type="text/css">
	a {color: #666;text-decoration: none;}
	a:hover,a:focus {color: #2a6496;text-decoration: underline;text-decoration: none;}
	a:focus {outline: thin dotted;outline: 5px auto -webkit-focus-ring-color;outline-offset: -2px;}
	.a{text-align:center;line-height: 40px;position: fixed;top: 0;left: 0;width: 100%;z-index: 10;border-bottom: 1px #ccc solid; background: #f50;color: #fff;}
	.tab{display: flex;line-height: 40px;position: fixed;top: 40px;width: 100%;z-index: 10;border-bottom: 1px #ccc solid;}
	.tab a{width: 33.333333%;background: #fff;text-align: center;}
	.tab .active{border-bottom: 1px #f50 solid; color: #f50;}
	.refreshtip {position: absolute;left: 0;width: 100%;margin: 10px 0;text-align: center;color: #999;}
	.swiper-container1{overflow: visible;padding-top:80px;height: calc(100vh - 120px);}
	.loadtip { display: block;width: 100%;line-height: 40px; height: 40px;text-align: center;color: #999;border-top: 1px solid #ddd;}
	.swiper-slide{height: auto;width: 100%;}
	.init-loading{display: none;background-color: #fff;padding: 10px 15px;text-align: center;}
</style>
<div class="headerBg" style="position: fixed;width: 100%;z-index: 9999;">
	<div class="back" onclick="javascript:history.go(-1)"></div>
	<div class="title">我的优惠券</div>
</div>
<div class="tab">
    <a class="@if($state == 0) active @endif" href="/coupon/0">未使用({{$CouponUsersNum[0]}})</a>
    <a class="@if($state == 1) active @endif" href="/coupon/1">已使用({{$CouponUsersNum[1]}})</a>
    <a class="@if($state == 2) active @endif" href="/coupon/2">已过期({{$CouponUsersNum[2]}})</a>
</div>
<div class="swiper-container1">
	<!--<div class="refreshtip">下拉可以刷新</div>-->
	<div class="swiper-wrapper">
		<!--<div class="init-loading">下拉可以刷新</div>-->
		<div class="swiper-slide">
			@foreach($CouponUsers as $CouponUser)
		    <div class="couponBox">
			    <div class="subLeft">
			    	<span class="price">￥<span class="PriceNum">{{number_format($CouponUser->amount_coupon,0)}}</span></span><br />
			        <span>满{{$CouponUser->amount_order}}可用</span>
			    </div>
			    <div class="decorate">
			    	<span style="top:-13px;"></span>
			    	<span style="bottom:-20px;"></span>
			    </div>
		        <div class="subRight">
		        	<div class="title">
		        		仅指定商品<span style="font-size: 16px;">可用</span>
		        		@if($state == 0)
		        			<a class="but" href="/CouponGoods/{{$CouponUser->coupon_id}}">立即使用</a>
		        		@elseif($state == 1)
		        			<span class="coupon-end"></span>
		        		@else
		        			<span class="coupon-overdue"></span>
		        		@endif
		        	</div>
		        	<div class="yyg-color9"><span>有效期</span>{{date("Y.m.d",strtotime($CouponUser->start_time))}}-{{date("Y.m.d",strtotime($CouponUser->end_time))}}</div>
		        </div>
	        </div>
		    @endforeach
		</div>
	</div>
	<!--<div class="loadtip">上拉加载更多</div>
	<div class="swiper-scrollbar"></div>-->
</div>
@endsection
@section('javascript')
<script type="text/javascript">
//	var loadFlag = true;
//	var mySwiper1 = new Swiper('.swiper-container1',{
//		direction: 'vertical',
//		scrollbar: '.swiper-scrollbar',
//		slidesPerView: 'auto',
//		mousewheelControl: true,
//		freeMode: true,
//		onTouchEnd: function(swiper) {
//			var _viewHeight = document.getElementsByClassName('swiper-wrapper')[0].offsetHeight;
//          var _contentHeight = document.getElementsByClassName('swiper-slide')[0].offsetHeight;
//           // 上拉加载
//          if(mySwiper1.translate <= _viewHeight - _contentHeight - 50 && mySwiper1.translate < 0) {
//          	
//              if(loadFlag){
//              	$(".loadtip").html('正在加载...');
//              }else{
//              	$(".loadtip").html('没有更多啦！');
//              }
//              setTimeout(function() {
//                  for(var i = 0; i <5; i++) {
//                  	$(".list-group").append('<li class="list-group-item">我是加载出来的</li>');
//                  }
//                  $(".loadtip").html('上拉加载更多...');
//                  mySwiper1.update(); // 重新计算高度;
//              }, 800);
//          }
//          // 下拉刷新
//          if(mySwiper1.translate >= 50) {
//              $(".init-loading").html('正在刷新...').show();
//              $(".loadtip").html('上拉加载更多');
//              loadFlag = true;
//              setTimeout(function() {
//                  $(".refreshtip").show(0);
//                  $(".init-loading").html('刷新成功！');
//                  setTimeout(function(){
//                  	$(".init-loading").html('').hide();
//                  },800);
//                  $(".loadtip").show(0);
//                  //刷新操作
//                  mySwiper1.update(); // 重新计算高度;
//              }, 1000);
//          }else if(mySwiper1.translate >= 0 && mySwiper1.translate < 50){
//          	$(".init-loading").html('').hide();
//          }
//          return false;
//		}
//	});
//  var imgurl = "{env('IMAGE_DISPLAY_DOMAIN')}}";
//  function getpage(){
//      $("#loding").show();
//      var pageNum = $("#page").val();
//      $.post('/collectLimit',{pageNum:pageNum},function(msg){
//          $("#loding").hide();
//          if(msg.ret == 'no'){
//          	$("#loding").show().text("已经加载全部");
//              return false;
//          }
//          var html = '';
//          $(msg.GoodBases).each(function(i,val){
//              html +='<div class="goodsList">' +
//                          '<a href="/goods/'+val.id+'">' +
//                              '<dl>' +
//                                  '<dt><img src="'+imgurl+val.cover_image+'"></dt>' +
//                                  '<dd><p class="description">'+val.title+'</p>' +
//                                      '<p class="price"><b>￥'+val.goodsspec[0].price+'</b><a href="javascript:void(0)" class="addCart btnIcon '+val.cartState+'"></a><input type="hidden" value="'+val.id+'" name="good_id"></p>'+
//                                  '</dd>'+
//                                  '<dd class="delete" data-id="'+val.id+'" onclick="popupShow('+val.id+')">删除</dd>'+
//                              '<dl>' +
//                          '</a>' +
//                      '</div>'
//          })
//          pageNum = msg.page_num*1 + 1;
//          $("#page").val(pageNum);
//          $(".goodsListBox").append(html);
//      })
//  }
</script>
@endsection