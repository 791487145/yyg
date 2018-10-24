@extends('wx.layout')
@section('title')
 {{$goods['title']}}
@endsection
@section('content')
<style>
	.goodsDetailCon p{padding:0;}
	.sellerInfo img {
    width: 50px;
    height:50px;
    border-radius: 50%;
    margin-right: 16px;
    margin: 0 auto;
    display: block;
}

    .footNavState{position: fixed;
        left: 0;
        right: 0;
        bottom: 0;
        overflow: hidden;
        z-index: 99;
        line-height: 40px;
        text-align: center;
        color: #fff;
        background: #999;
    }
    .buyPopup .radioGroup label.numNo{background: #e8e8e8;}
    .buyPopup .noClickBg{background: #999;}
    .limitNum{float: right;margin-right: 20px;}

    .moreComment{padding: 10px 0;text-align: center;display: inline-block;width: 100%;color: #999;}

</style>
<div class="headerBg">
	<div class="back" onclick="javascript:history.go(-1)"></div>
	<div class="title"><?php echo $goods['title']?></div>
</div>
<a href="/" class="indexIcon">首页</a>
<div class="content goodsDetail tabBoxGroup">
    <div class="tabBox active">
        <div class="banner">
            <div class="swiper-container">
                <div class="swiper-wrapper">
                    @foreach($goodsImgLists as $goodsImgList)
                    <div class="swiper-slide"><img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$goodsImgList['name']}}"/></div>
                    @endforeach
                </div>
                <div class="pagination"></div>
            </div>
        </div>
        <div class="goodsInfo lineB mb-6">
            <p class="description" style="min-height:40px;"><?php echo $goods['title']?><span class="lineL favor ">
                    {{--判断是否收藏--}}
                    @if(!is_null($userFavorites))<a class="z-collect" onclick="collect(<?php echo $goods['id']?>)">已收藏</a>
                    @else<a class="z-collect z-click" onclick="collect(<?php echo $goods['id']?>)">收藏</a>
                    @endif
                </span></p>
            <div class="bottom lineT">
                {{--商品价格--}}
                <span class="price">￥<b class="newprice">{{$goodsSpecs['0']['price']}}</b></span>
                <span style="margin-left:20px;">原价：￥<span class="text-line market">{{$goodsSpecs['0']['price_market']}}</span></span>
            </div>
            <div class="bottom lineT" style="text-align: center;">
                <span style="float: left;">{{$supplier->total_amount}}</span>
                <span>已售出<?php echo $goods['num_sold']?>件</span>
                <span class="right">{{$supplier->is_pick_up}}</span>
            </div>
        </div>
        <ul class="goodsSpec mb-6 lineB">
            <li class="lineB">
                <a href="javascript:void(0)" class="linkArrow" onclick="bottomPopupShow('addCartPopup')">
                    <dl>
                        <dt>已选：</dt>
                        <dd class="newname"><?php echo $goodsSpecs[0]['name']?></dd>
                    </dl>
                    <span class="arrow"></span>
                </a>
            </li>
            @if(empty($goodsGift))
            @else

                    @foreach($goodsGift as $k=>$goodGift)
                    <li class="lineB">
                        <a href="{{url('goods',$goodGift['id'])}}" class="linkArrow">
                            <dl class="oh">
                                <dt style="text-overflow: ellipsis;white-space:nowrap;overflow: hidden;width: 95%">
                                    @if($k==0)
                                    活动赠品：
                                    @else
                                    <span style="margin-left: 62px;"></span>
                                    @endif
                                    <?php echo $goodGift['title']?>
                                </dt>
                                {{--判断有无赠品--}}
                            </dl>
                            <span class="arrow"></span>
                        </a>
                    </li>
                    @endforeach

            @endif

        </ul>

        {{--供应商店铺--}}
        @if($guideBase['id'] == 0)
            <div class="sellerInfo lineB mb-6 goodsSpec">
                <a href="/supplier/<?php echo $supplier['id']?>" class="linkArrow">
                    <dl><dt><img src="@if($supplier['store_logo']){{env('IMAGE_DISPLAY_DOMAIN')}}<?php echo $supplier['store_logo']?>@else /images/user.png @endif"></dt><dd>&nbsp;<?php echo $supplier['store_name']?></dd></dl>
                    <span class="arrow" style="top:14px;">进入店铺</span>
                </a>
            </div>
        @else
            <div class="sellerInfo lineB mb-6 goodsSpec">
                <a href="/guide/{{$guideBase['id']}}" class="linkArrow">
                    <dl><dt><img src="@if($guideBase['avatar']){{env('IMAGE_DISPLAY_DOMAIN')}}{{$guideBase['avatar']}}@else /images/user.png @endif"></dt><dd>&nbsp;{{$guideBase['real_name']}}</dd></dl>
                    <span class="arrow" style="top:14px;">进入店铺</span>
                </a>
            </div>
        @endif
        <div class="goodsInfoList lineB mb-6">
            {{--商品属性--}}
            <h3 class="lineB">购买须知：</h3>

            {{--产品规格--}}
            <dl class="lineB"><dt>产品规格</dt><dd class="goodsspec_name">{{$goodsSpecs['0']['name']}}</dd></dl>
            <dl class="lineB"><dt>重量</dt><dd class="goodsspec_weight">{{$goodsSpecs['0']['weight']}}</dd></dl>
            <dl class="lineB"><dt>净含量</dt><dd class="goodsspec_weight_net">{{$goodsSpecs['0']['weight_net']}}</dd></dl>

            {{--产品说明--}}
            @if(!empty( $goodsExt['send_out_address']))
                <dl class="lineB"><dt>发货地</dt><dd><?php echo $goodsExt['send_out_address']?></dd></dl>
            @endif
            @if(!empty( $goodsExt['product_area']))
                <dl class="lineB"><dt>原产地</dt><dd><?php echo $goodsExt['product_area']?></dd></dl>
            @endif
            @if(!empty($goodsExt['shelf_life']))
            <dl class="lineB"><dt>保质期</dt><dd><?php echo $goodsExt['shelf_life']?></dd></dl>
            @endif
            @if(!empty( $goodsExt['store']))
                <dl class="lineB"><dt>储藏</dt><dd><?php echo $goodsExt['store']?></dd></dl>
            @endif
            @if(!empty( $goodsExt['pack']))
            <dl class="lineB"><dt>包装</dt><dd><?php echo $goodsExt['pack']?></dd></dl>
            @endif
            @if(!empty( $goodsExt['express_desc']))
            <dl class="lineB"><dt>快递说明</dt><dd><?php echo $goodsExt['express_desc']?></dd></dl>
                @endif
            @if(!empty( $goodsExt['pack']))
                <dl class="lineB"><dt>发货说明</dt><dd><?php echo $goodsExt['send_out_desc']?></dd></dl>
            @endif
            @if(!empty( $goodsExt['sold_desc']))
                <dl class="lineB"><dt>售后说明</dt><dd><?php echo $goodsExt['sold_desc']?></dd></dl>
            @endif
            @if(!empty( $goodsExt['food_addiitive']))
                <dl class="lineB"><dt>食品添加剂</dt><dd><?php echo $goodsExt['food_addiitive']?></dd></dl>
            @endif
            @if(!empty( $goodsExt['product_license']))
                <dl class="lineB"><dt>生产许可证</dt><dd><?php echo $goodsExt['product_license']?></dd></dl>
            @endif
            @if(!empty( $goodsExt['level']))
                <dl class="lineB"><dt>等级</dt><dd><?php echo $goodsExt['level']?></dd></dl>
            @endif
            @if(!empty( $goodsExt['company']))
                <dl class="lineB"><dt>制造商/公司</dt><dd><?php echo $goodsExt['company']?></dd></dl>
            @endif
            @if(!empty( $goodsExt['food_burden']))
                <dl class="lineB"><dt>配料表</dt><dd><?php echo $goodsExt['food_burden']?></dd></dl>
            @endif
            @if(!empty( $goodsExt['dealer']))
                <dl class="lineB"><dt>经销商</dt><dd><?php echo $goodsExt['dealer']?></dd></dl>
            @endif
            @if(!empty( $goodsExt['address']))
                <dl class="lineB"><dt>地址</dt><dd><?php echo $goodsExt['address']?></dd></dl>
            @endif

        </div>
        @if(!empty($comments))
        <div class="goodsInfoList lineB mb-6">
            @if($comments->total()>0)
            <h3 class="lineB">商品评价：</h3>
            <div class="commentBox">
            	<div class="commentUser">
            		<img class="userImg" src="@if($comments['0']['headimg']) {{env('IMAGE_DISPLAY_DOMAIN')}}{{$comments['0']['headimg']}} @else /images/user.png @endif "/>
            		<span class="userName">{{$comments['0']['nicknake']}}</span>
            		<span class="yyg-color9 fr">{{$comments['0']['created_at']}}</span>
            	</div>
            	<p class="contentText">{{$comments['0']['comment']}}</p>
            	<div class="commentImgBox imgPopup">
            	@if(!empty($comments['0']['comment_img']))
            		@foreach($comments['0']['comment_img'] as $key=>$comment_img)
            			<img data-index="{{$key}}" src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$comment_img['image_name']}}"/>
        			@endforeach
            	@endif
            	</div>
            	@if(!empty($comments['0']['reply_comment']))
            	<p>店家回复：&emsp;&emsp;{{$comments['0']['updated_at']}}</p>
            	<p class="contentText">{{$comments['0']['reply_comment']}}</p>
            	@endif
            </div>
            @endif
            @if($comments->total()>1)
            <div class="commentBox">
            	<div class="commentUser">
            		<img class="userImg" src="@if($comments['0']['headimg']) {{env('IMAGE_DISPLAY_DOMAIN')}}{{$comments['0']['headimg']}} @else /images/user.png @endif "/>
            		<span class="userName">{{$comments['1']['nicknake']}}</span>
            		<span class="yyg-color9 fr">{{$comments['1']['created_at']}}</span>
            	</div>
            	<p class="contentText">{{$comments['1']['comment']}}</p>
            	<div class="commentImgBox imgPopup">
            	@if(!empty($comments['1']['comment_img']))
            		@foreach($comments['1']['comment_img'] as $key=>$comment_img)
            			<img data-index="{{$key}}" src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$comment_img['image_name']}}"/>
        			@endforeach
        		@endif	
            	</div>
            	@if(!empty($comments['1']['reply_comment']))
            	<p>店家回复：&emsp;&emsp;{{$comments['1']['updated_at']}}</p>
            	<p class="contentText">{{$comments['1']['reply_comment']}}</p>
            	@endif
            </div>
            @endif
            @if($comments->total()>2)
            <a class="moreComment" href="/goods/comment/<?php echo $goods['id']?>">查看更多评价</a>
            @endif
        </div>
        @endif
        
        {{--商品详情内容--}}
        <div class="goodsInfoList lineB">
            <h3 class="lineB">商品详情：</h3>
        </div>
        <div class="goodsDetailCon" style="background: #fff;">
		    <?php echo $goodsExt['description']?>
		</div>
    </div>
</div>
@if($goods->state == 2)
    <div class="footNavState">
        该商品已下架
    </div>
@elseif($goodsSpecs[0]['num'] <= 0)
    <div class="footNav goodsFoot lineT lineR">
        <a href="javascript:void(0)" class="serviceButton" onclick="popupShow('customeService')"><i></i>客服</a>
        <a href="/cart" class="shopCartButton"><i></i>购物车<b>{{$count}}</b></a>
        <div class="buttonGroup">
            <label><input type="button" value="立即购买" class="btnOrange" onclick="bottomPopupShow('buyNowPopup')"></label>
            <label><input type="button" value="加入购物车" class="btnYellow" onclick="bottomPopupShow('addCartPopup')"></label>
        </div>
    </div>
@else
    <div class="footNav goodsFoot lineT lineR">
        <a href="javascript:void(0)" class="serviceButton" onclick="popupShow('customeService')"><i></i>客服</a>
        <a href="/cart" class="shopCartButton"><i></i>购物车<b>{{$count}}</b></a>
        <div class="buttonGroup">
            <label><input type="button" value="立即购买" class="btnOrange" onclick="bottomPopupShow('buyNowPopup')"></label>
            <label><input type="button" value="加入购物车" class="btnYellow" onclick="bottomPopupShow('addCartPopup')"></label>
        </div>
    </div>
@endif


<div class="popupBg"></div>
<div class="popupWrap customeService">
    <h3>拨打客服</h3>
    <p>400-9158-971</p>
    <div class="bottomButtonGroup lineT lineR">
        <a href="javascript:void(0)" class="close button">取消</a>
        <a href="tel:400-9158-971" class="button">确定</a>
    </div>
</div>

@if(isset($goods['important_tips']))
<div class="popupBgs"></div>
<div class="popupWraps customeServiceS">
    <h3>重要提示</h3>
    <p>{{$goods['important_tips']}}</p>
    <br/>
    <div class="bottomButtonGroup lineT lineR">
        <a href="javascript:popupHide()" class="close button">知道了</a>
    </div>
</div>
@endif
<div class="bottomPopup buyPopup addCartPopup">
    <dl class="info box">
        <dt><img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$goods['cover_image']}}"></dt>
        <dd>
            <p class="name"><?php echo $goods['title']?></p>
            <p>库存：<span class="newNum"><?php echo $goodsSpecs['0']['num']?>件</span></p>
            <p>
            	￥<span class="price newprice">{{$goodsSpecs['0']['price']}}</span>
            	<span style="margin-left:15px;font-size: 12px;">原价： ￥<span class="text-line market">{{$goodsSpecs['0']['price_market']}}</span></span>
            </p>
            
        </dd>
    </dl>
    <dl class="lineT box spec">
        <dt>规格选择</dt>
        <dd class="radioGroup" style="height:90px;overflow:scroll">
            @foreach($goodsSpecs as $key => $goodsname)
             @if($goodsname['num'] <= 0)
            <span>
                <input type="radio" disabled="disabled" class="zm" name="radio1" value="{{$goodsname['id']}}" id="radio1-{{$key+1}}"/>
                <label class="numNo"  for="radio1-{{$key+1}}">{{$goodsname['name']}}</label>
            </span>
            @else
            <span>
                <input type="radio" class="zm" name="radio1" {{($key == 0) ? 'checked' : ''}} value="{{$goodsname['id']}}" id="radio1-{{$key+1}}"/>
                <label  for="radio1-{{$key+1}}">{{$goodsname['name']}}</label>
            </span>
            @endif
           @endforeach
        </dd>
    </dl>
    <dl class="lineT box amount">
        <dt>数量选择</dt>
        <dd class="amountInput">
            <input type="button" class="minus">
            <input type="number" onkeyup="this.value=this.value.replace(/^[0]\d*$/,'')"   value="1" class="number" name = "good_num">
            <input type="button" class="add">
        </dd>
    </dl>
    <input type="hidden" name="taid" value="{{$taid}}">
    <input type="hidden" name="gid" value="{{$guideBase['id']}}">
    @if($goodsSpecs['0']['num_limit'] > 0)
        <span class="limitNum yyg-color">每个ID限制购买<span>{{$goodsSpecs['0']['num_limit']}}</span>件</span>
    @else
        <span class="limitNum yyg-color" style="display: none;">每个ID限制购买<span></span>件</span>
    @endif
    @if($goodsSpecs['0']['num']<=0)
        <input type="button" class="bottomButton close addCart noClickBg" value="该规格已售罄">
    @else
        <input type="button" class="bottomButton close addCart" value="加入购物车" onclick="cart('{{$goods['id']}}','cart')">
    @endif
</div>



<div class="bottomPopup buyPopup buyNowPopup">
    <dl class="info box">
        <dt><img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$goods['cover_image']}}"></dt>
        <dd>
            <p class="name"><?php echo $goods['title']?></p>
            <p>库存：<span class="newNum"><?php echo $goodsSpecs['0']['num']?>件</span></p>
            <p>
            	￥<span class="price newprice" style="font-size: 16px;">{{$goodsSpecs['0']['price']}}</span>
            	<span style="margin-left:15px;font-size: 12px;">原价： ￥<span class="text-line market">{{$goodsSpecs['0']['price_market']}}</span></span>
            </p>
        </dd>
    </dl>
    <dl class="lineT box spec">
        <dt>规格选择</dt>
        <dd class="radioGroup" style="height:90px;overflow:scroll">
            @foreach($goodsSpecs as $key => $goodsname)
                @if($goodsname['num'] <= 0)
                    <span>
                        <input type="radio" disabled="disabled" class="zm" name="radio2" value="{{$goodsname['id']}}" id="radio2-{{$key+1}}">
                        <label class="numNo"  for="radio2-{{$key+1}}" >{{$goodsname['name']}}</label>
                    </span>
                @else
                    <span>
                        <input type="radio" class="zm" data-milit="{{$goodsname['num_limit']}}" name="radio2" {{($key == 0) ? 'checked' : ''}} value="{{$goodsname['id']}}" id="radio2-{{$key+1}}">
                        <label for="radio2-{{$key+1}}" >{{$goodsname['name']}}</label>
                    </span>
                @endif
            @endforeach
        </dd>
    </dl>
    <dl class="lineT box amount">
        <dt>数量选择</dt>
        <dd class="amountInput">
            <input type="button" class="minus">
            <input type="number" value="1" class="number" onkeyup="this.value=this.value.replace(/^[0]\d*$/,'')" name="goods_name">
            <input type="button" class="add">
        </dd>
    </dl>
    @if($goodsSpecs['0']['num_limit'] > 0)
        <span class="limitNum yyg-color">每个ID限制购买<span>{{$goodsSpecs['0']['num_limit']}}</span>件</span>
    @else
        <span class="limitNum yyg-color" style="display: none;">每个ID限制购买<span></span>件</span>
    @endif
    @if($goodsSpecs['0']['num']<=0)
        <input type="button" class="bottomButton noClickBg" value="该规格已售罄">
    @else
        <input type="button" class="bottomButton" value="立即购买" onclick="cart('{{$goods['id']}}','shop')">
    @endif
</div>

</div>
@endsection
@section('javascript')
<script type="text/javascript">

    //微信返回按钮
    /*pushHistory()
    window.addEventListener("popstate", function(e) {
        window.location = '/';
    }, false);
    function pushHistory() {
        var state = {
            title: "title",
            url: "#"
        };
        window.history.pushState(state, "title", "#");
    }*/
	judge_id();
	function judge_id(){
		var display = $(".popupBgs").css("display");
		if(display == "block"){
	    	var get_id = localStorages("","localStorages_good");
	    	var localStorages_good = "{{$goods['id']}}";
	   		if(localStorages_good == get_id){
	   			$(".popupBgs").hide();
	   			$(".customeServiceS").hide();
	   		}else{
	   			localStorages("add","localStorages_good",localStorages_good);
	   		}
   		}
    }
    $(function(){
        $("input[name = 'good_num']").blur(function(){
            var good_num = $("input[name = 'good_num']").val();
            if(good_num == ''){
                $("input[name = 'good_num']").val(1);
            }
        })
    })

    function cart(id,cart){
        var good_num = $("input[name = 'good_num']").val();
        if(cart == 'shop'){
            var good_num = $("input[name = 'goods_name']").val();
            var spec_id = $("input[name = 'radio2']:checked").val();
        }else{
            var spec_id = $("input[name = 'radio1']:checked").val();
        }

        var gid = $("input[name = 'gid']").val();
        var taid = $("input[name = 'taid']").val();
        if(spec_id !=undefined && good_num != '') {

            $.post('/carts', {good_id:id, num: good_num, spec_id: spec_id,gid:gid,taid:taid,cart:cart,open_id:"{{Cookie::get('openid')}}"}, function (msg) {
                if (msg.ret == 'yes') {
                    cartNum(msg.count);
                    information('加入购物车成功');
                }
                if (msg.ret == 'no') {
                    information('商品库存不足，无法购买');
                }
                if (msg.ret == 'not') {
                    information('至少选择一件商品');
                }
                if(msg.ret == 'shop'){
                    location.href = '/order/cart/'+id;
                }
                if(msg.ret == 'down'){
                    information('商品已下架,不能进行相关操作');
                }
            })
        }else{
            information( '请选择商品数量与规格');
        }
    }


    $(".zm").click(function(){
       var id = $(this).val();
       var limitNum = $(this).attr("data-milit");
       $.ajax({
            type:'post',
            url:'/goods/'+id,
           dataType:'json',
            success:function(data){
                $(".newprice").html(data.price);
                $(".newname").html(data.name);
                $(".newNum").html(data.num);
                $(".goodsspec_name").html(data.name);
                $(".goodsspec_weight").html(data.weight);
                $(".goodsspec_weight_net").html(data.weight_net);
                $(".market").text(data.price_market);
                if(limitNum>0){
                    $(".limitNum").show();
                    $(".limitNum span").text(limitNum);
                }else{
                    $(".limitNum").hide();
                }
                if(data.num>0){
                    var addCart = $(".addCartPopup").is(':visible');
                    if(addCart){
                        $(".addCartPopup .bottomButton").removeClass("noClickBg").attr("onclick","cart('"+data.goods_id+"','cart')");
                        $(".addCartPopup .bottomButton").val("加入购物车");
                    }else{
                        $(".buyNowPopup .bottomButton").removeClass("noClickBg").attr("onclick","cart('"+data.goods_id+"','shop')");
                        $(".buyNowPopup .bottomButton").val("立即购买");
                    }
                }else{
                    $(".bottomButton").text("该规格已售罄");
                }
            }
        });
    })
    //加减
	$(".amountInput .add").click(function(){
		var limitNumVisible = $(".limitNum").is(':visible');
		if(limitNumVisible){
			var boxClass = '';
			var addCart = $(".addCartPopup").is(':visible');
	        if(addCart){
	        	boxClass = ".addCartPopup";
	        }else{
	        	boxClass = ".buyNowPopup";
	        }
			var limitNum = ($(boxClass+" .limitNum span").text())*1;
			var textNum = ($(this).siblings(".number").val())*1;
			if(textNum > limitNum){
				$(this).siblings(".number").val(limitNum);
				information('数量不能超过限购量');
			}
		}
	});
	$(".amountInput .number").keyup(function(){
		var limitNumVisible = $(".limitNum").is(':visible');
		if(limitNumVisible){
			var boxClass = '';
			var addCart = $(".addCartPopup").is(':visible');
	        if(addCart){
	        	boxClass = ".addCartPopup";
	        }else{
	        	boxClass = ".buyNowPopup";
	        }
			var limitNum = ($(boxClass+" .limitNum span").text())*1;
			var textNum = ($(this).val())*1;
			if(textNum > limitNum){
				$(this).val(limitNum);
				information('数量不能超过限购量');
			}
		}
	});

    function collect(goodsid){
        $.ajax({
            type:'get',
            url:'/collect/',
            dataType:'json',
            data:{'goodsid':goodsid},
            success:function(data){
                if(data == 1) {
                    $(".z-collect").removeClass('z-click');
                    $(".z-collect").html('已收藏');
                }
                if(data == 2){
                    $(".z-collect").addClass('z-click');
                    $(".z-collect").html('收藏');
                }
            }
        });
    }
</script>
@endsection