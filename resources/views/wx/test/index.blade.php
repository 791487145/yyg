@extends('wx.layout')
@section('title')
   商品的评价
@endsection
@section('content')
<style type="text/css">
	body{background: #f8f8f8;}
	.refreshtip {position: absolute;left: 0;width: 100%;margin: 10px 0;text-align: center;color: #999;}
	.swiper-container1{overflow: visible;padding-top:80px;height: calc(100vh - 120px);}
	.loadtip {position: absolute;bottom: 0; display: block;width: 100%;line-height: 40px; height: 40px;text-align: center;color: #999;}
	.swiper-slide{height: auto;width: 100%;background:#f8f8f8;}
	.init-loading{display: none;background-color: #f8f8f8;padding: 10px 15px;text-align: center;}
	.commentBox:last-child{border-bottom:1px solid #fff;}
	
	.couponBg{position: fixed;z-index: 9000;background: rgba(0,0,0,0.5);top: 0;left: 0;right: 0;bottom: 0;}
	.couponBoxBg{width:330px;height: 382px;background: url(/wx/images/couponBg.png) center center no-repeat;position: absolute; 
		background-size:100%;-webkit-transform: translate(-50%, -50%);
		transform: translate(-50%, -50%);left:50%;top: 50%;
	}
	.couponNum{color: #fff;text-align: center;margin-top: 50px;}
	.couponBoxBut{height:100px;width: 50%; background: url(/wx/images/couponBut.png) bottom center no-repeat; background-size:100%;position: absolute;bottom:20px;left: 25%;}
	.couponBoxBg .couponBoxAll{width: 220px;margin:0 auto;margin-top:22px;}
	.couponBoxBg .couponBoxAll span{font-size: 12px;}
	.couponBoxBg .couponBox1{background: #fff;margin:8px 0;padding: 1px;padding-bottom:4px;}
	.couponBoxBg .couponBox1 .subLeft{display: inline-block;width:34%;text-align:center;color: #999;line-height:24px;}
	.couponBoxBg .couponBox1 .subLeft .price{color: #ca352c;font-size:18px;line-height:45px;}
	.couponBoxBg .couponBox1 .subLeft .price .PriceNum{font-size:34px;font-weight: bold;}
	.couponBoxBg .couponBox1 .decorate{display: inline-block;width:0;height:63px;border-right: 1px dashed #eee;margin:0 1px;position: relative;}
	.couponBoxBg .couponBox1 .decorate span{background: #ffeeb9;width:10px;height: 10px;border-radius:50%;display: inline-block;position: absolute;left:-4px;}
	.couponBoxBg .couponBox1 .subRight{display: inline-block;width:61%;line-height:24px;}
	.couponBoxBg .couponBox1 .subRight .title{font-size:16px;color:#333;line-height:50px;font-weight: bold;}
	.couponBoxBg .couponBox1 .subRight .but{font-size: 12px;padding: 2px 4px;color: #ED6B09;border: 1px solid #ED6B09;border-radius: 3px;font-weight:normal;}
	.couponBoxBg .couponBox1 .subRight .coupon-end{background: url(../images/coupon_end.png) center no-repeat;display: inline-block;width:55px;height:55px;background-size: 90%;margin:0 0 -20px 10px;}
	.couponBoxBg .couponBox1 .subRight .coupon-overdue{background: url(../images/coupon_overdue.png) center no-repeat;display: inline-block;width:55px;height:55px;background-size: 90%;margin:0 0 -20px 10px;}




</style>
<div class="fixedHead classifyHead">
	<div class="headerBg">
		<div class="back" onclick="javascript:history.go(-1)"></div>
		<div class="title">漓江手撕鱼</div>
	</div>
	<div class="tabButton">
		<ul class="lineT lineB" style="width: 582px;">
		    <li class="active"><a href="/test/html"><span>名优特产</span></a></li>
		    <li class=""><a href="/category/7"><span>休闲零食</span></a></li>
		    <li class=""><a href="/category/1"><span>滋补养生</span></a></li>
		    <li class=""><a href="/category/16"><span>茶饮酒水</span></a></li>
		    <li class=""><a href="/category/13"><span>厨房美食</span></a></li>
		</ul>
	</div>
</div>
<div class="swiper-container1">
	<div class="refreshtip">下拉可以刷新</div>
	<div class="swiper-wrapper">
		<div class="init-loading">下拉可以刷新</div>
		<div class="swiper-slide">
			<div class="goodsListBox" style="margin-top: 10px;">
			
	            <div class="goodsList">
			    	<a href="/goods/84">
				        <dl>
				            <dt>
				                <img src="https://img2.yyougo.com/8327736eecc1ef95b564.jpeg?imageslim">
				            </dt>
				            <dd>
				                <p class="description">测试商品测试商品测试商品测试商品测试商品测试商品测试商品</p>
				                <p class="price">
				                	<b>￥200.00</b>
				                	<a href="javascript:void(0)" class="addCart btnIcon"></a>
				                	<input type="hidden" value="84" name="good_id">
				                </p>
				            </dd>
				        </dl>
			        </a>
			    </div>
			    <div class="goodsList">
			    	<a href="/goods/84">
				        <dl>
				            <dt>
				                <img src="https://img2.yyougo.com/8327736eecc1ef95b564.jpeg?imageslim">
				            </dt>
				            <dd>
				                <p class="description">测试商品测试商品测试商品测试商品测试商品测试商品测试商品</p>
				                <p class="price">
				                	<b>￥200.00</b>
				                	<a href="javascript:void(0)" class="addCart btnIcon"></a>
				                	<input type="hidden" value="84" name="good_id">
				                </p>
				            </dd>
				        </dl>
			        </a>
			    </div>
			    <div class="goodsList">
			    	<a href="/goods/84">
				        <dl>
				            <dt>
				                <img src="https://img2.yyougo.com/8327736eecc1ef95b564.jpeg?imageslim">
				            </dt>
				            <dd>
				                <p class="description">测试商品测试商品测试商品测试商品测试商品测试商品测试商品</p>
				                <p class="price">
				                	<b>￥200.00</b>
				                	<a href="javascript:void(0)" class="addCart btnIcon"></a>
				                	<input type="hidden" value="84" name="good_id">
				                </p>
				            </dd>
				        </dl>
			        </a>
			    </div>
			    <div class="goodsList">
			    	<a href="/goods/84">
				        <dl>
				            <dt>
				                <img src="https://img2.yyougo.com/8327736eecc1ef95b564.jpeg?imageslim">
				            </dt>
				            <dd>
				                <p class="description">测试商品测试商品测试商品测试商品测试商品测试商品测试商品</p>
				                <p class="price">
				                	<b>￥200.00</b>
				                	<a href="javascript:void(0)" class="addCart btnIcon"></a>
				                	<input type="hidden" value="84" name="good_id">
				                </p>
				            </dd>
				        </dl>
			        </a>
			    </div>
			    <div class="goodsList">
			    	<a href="/goods/84">
				        <dl>
				            <dt>
				                <img src="https://img2.yyougo.com/8327736eecc1ef95b564.jpeg?imageslim">
				            </dt>
				            <dd>
				                <p class="description">测试商品测试商品测试商品测试商品测试商品测试商品测试商品</p>
				                <p class="price">
				                	<b>￥200.00</b>
				                	<a href="javascript:void(0)" class="addCart btnIcon"></a>
				                	<input type="hidden" value="84" name="good_id">
				                </p>
				            </dd>
				        </dl>
			        </a>
			    </div>
			    <div class="goodsList">
			    	<a href="/goods/84">
				        <dl>
				            <dt>
				                <img src="https://img2.yyougo.com/8327736eecc1ef95b564.jpeg?imageslim">
				            </dt>
				            <dd>
				                <p class="description">测试商品测试商品测试商品测试商品测试商品测试商品测试商品</p>
				                <p class="price">
				                	<b>￥200.00</b>
				                	<a href="javascript:void(0)" class="addCart btnIcon"></a>
				                	<input type="hidden" value="84" name="good_id">
				                </p>
				            </dd>
				        </dl>
			        </a>
			    </div>
			    
		    </div>
		</div>
	</div>
	<div class="loadtip">上拉加载更多</div>
	<div class="swiper-scrollbar"></div>
</div>

<div class="couponBg">
	
	<div class="couponBoxBg">
		<div class="couponNum">恭喜您，已经成功获取2张优惠卷</div>
		<div class="couponBoxAll">
			<div class="couponBox1">
			    <div class="subLeft">
			    	<span class="price">￥<span class="PriceNum">3</span></span><br>
			        <span>满10.00可用</span>
			    </div>
			    <div class="decorate">
			    	<span style="top:-13px;"></span>
			    	<span style="bottom:-15px;"></span>
			    </div>
		        <div class="subRight">
		        	<div class="title">
		        		仅指定商品
		        		<!--<span style="font-size: 16px;">可用</span>-->
			        	<a class="but" href="/CouponGoods/5">已领取</a>
			        </div>
		        	<div class="yyg-color9 " style="font-size: 12px;">
		        		<!--<span>有效期</span>-->
		        		2017.08.03-1970.01.01
		        	</div>
		        </div>
		    </div>
		    <div class="couponBox1">
			    <div class="subLeft">
			    	<span class="price">￥<span class="PriceNum">3</span></span><br>
			        <span>满10.00可用</span>
			    </div>
			    <div class="decorate">
			    	<span style="top:-13px;"></span>
			    	<span style="bottom:-15px;"></span>
			    </div>
		        <div class="subRight">
		        	<div class="title">
		        		仅指定商品
		        		<!--<span style="font-size: 16px;">可用</span>-->
			        	<a class="but" href="/CouponGoods/5">已领取</a>
			        </div>
		        	<div class="yyg-color9 " style="font-size: 12px;">
		        		<!--<span>有效期</span>-->
		        		2017.08.03-1970.01.01
		        	</div>
		        </div>
		    </div>
	    </div>
	    <div class="couponBoxBut"></div>
	</div>
	
</div>
@endsection
@section('javascript')
<script type="text/javascript">
	var loadFlag = true;
	var mySwiper1 = new Swiper('.swiper-container1',{
		direction: 'vertical',
		scrollbar: '.swiper-scrollbar',
		slidesPerView: 'auto',
		mousewheelControl: true,
		freeMode: true,
		onTouchEnd: function(swiper) {
			var _viewHeight = document.getElementsByClassName('swiper-wrapper')[0].offsetHeight;
            var _contentHeight = document.getElementsByClassName('swiper-slide')[0].offsetHeight;
             // 上拉加载
            if(mySwiper1.translate <= _viewHeight - _contentHeight - 50 && mySwiper1.translate < 0) {
            	
                if(loadFlag){
                	$(".loadtip").html('正在加载...');
                }else{
                	$(".loadtip").html('没有更多啦！');
                }
                setTimeout(function() {
                    for(var i = 0; i <5; i++) {
                    	$(".list-group").append('<li class="list-group-item">我是加载出来的</li>');
                    }
                    $(".loadtip").html('上拉加载更多...');
                    mySwiper1.update(); // 重新计算高度;
                }, 800);
            }
            // 下拉刷新
            if(mySwiper1.translate >= 50) {
                $(".init-loading").html('正在刷新...').show();
                $(".loadtip").html('上拉加载更多');
                loadFlag = true;
                setTimeout(function() {
                    $(".refreshtip").show(0);
                    $(".init-loading").html('刷新成功！');
                    setTimeout(function(){
                    	$(".init-loading").html('').hide();
                    },800);
                    $(".loadtip").show(0);
                    //刷新操作
                    mySwiper1.update(); // 重新计算高度;
                }, 1000);
            }else if(mySwiper1.translate >= 0 && mySwiper1.translate < 50){
            	$(".init-loading").html('').hide();
            }
            return false;
		}
	});
    var imgurl = "{env('IMAGE_DISPLAY_DOMAIN')}}";
    function getpage(){
        $("#loding").show();
        var pageNum = $("#page").val();
        $.post('/collectLimit',{pageNum:pageNum},function(msg){
            $("#loding").hide();
            if(msg.ret == 'no'){
            	$("#loding").show().text("已经加载全部");
                return false;
            }
            var html = '';
            $(msg.GoodBases).each(function(i,val){
                html +='<div class="goodsList">' +
                            '<a href="/goods/'+val.id+'">' +
                                '<dl>' +
                                    '<dt><img src="'+imgurl+val.cover_image+'"></dt>' +
                                    '<dd><p class="description">'+val.title+'</p>' +
                                        '<p class="price"><b>￥'+val.goodsspec[0].price+'</b><a href="javascript:void(0)" class="addCart btnIcon '+val.cartState+'"></a><input type="hidden" value="'+val.id+'" name="good_id"></p>'+
                                    '</dd>'+
                                    '<dd class="delete" data-id="'+val.id+'" onclick="popupShow('+val.id+')">删除</dd>'+
                                '<dl>' +
                            '</a>' +
                        '</div>'
            })
            pageNum = msg.page_num*1 + 1;
            $("#page").val(pageNum);
            $(".goodsListBox").append(html);
        })
    }
</script>
@endsection