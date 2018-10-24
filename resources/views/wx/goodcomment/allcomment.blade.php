@extends('wx.layout')
@section('title')
   商品的评价
@endsection
@section('content')
<style type="text/css">
	body{background: #fff;}
	.refreshtip {position: absolute;left: 0;width: 100%;margin: 10px 0;text-align: center;color: #999;}
	.swiper-container1{overflow: visible;padding-top:40px;height: calc(100vh - 80px);}
	.loadtip {position: absolute;bottom: 0; display: block;width: 100%;line-height: 40px; height: 40px;text-align: center;color: #999;border-top: 1px solid #ddd;}
	.swiper-slide{height: auto;width: 100%;}
	.init-loading{display: none;background-color: #fff;padding: 10px 15px;text-align: center;}
	.commentBox:last-child{border-bottom:1px solid #fff;}
</style>
<div class="headerBg" style="position: fixed;width: 100%;z-index:8999;">
	<div class="back" onclick="javascript:history.go(-1)"></div>
	<div class="title">商品的评价</div>
</div>
<div class="swiper-container1">
	<!--<div class="refreshtip">下拉可以刷新</div>-->
	<div class="swiper-wrapper">
		<!--<div class="init-loading">下拉可以刷新</div>-->
		<div class="swiper-slide">
	        <div class="goodsInfoList">
	            @foreach($comments as $commentsList)
	            <div class="commentBox">
	            	<div class="commentUser">
	            		<img class="userImg" src="@if($commentsList['headimg']) {{env('IMAGE_DISPLAY_DOMAIN')}}{{$commentsList['headimg']}} @else /images/user.png @endif "/>
	            		<span class="userName">{{$commentsList['nicknake']}}</span>
	            		<span class="yyg-color9 fr">{{$commentsList['created_at']}}</span>
	            	</div>
	            	<p class="contentText">{{$commentsList['comment']}}</p>
	            	<div class="commentImgBox imgPopup">
	            	@if(!empty($commentsList['comment_img']))
	            		@foreach($commentsList['comment_img'] as $key=>$comment_img)
	            			<img data-index="{{$key}}" src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$comment_img['image_name']}}"/>
	        			@endforeach
	            	@endif
	            	</div>
	            	@if(!empty($commentsList['reply_comment']))
                	<p>店家回复：&emsp;&emsp;{{$commentsList['updated_at']}}</p>
                	<p class="contentText">{{$commentsList['reply_comment']}}</p>
                	@endif
	            </div>
	            @endforeach
	        </div>
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