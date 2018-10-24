@extends('wx.layout')
@section('title')
订单确认
@endsection
@section('content')
    <style>
        .couponBox .subLeft .price{color:#999;}
        .couponBut .subLeft .price{color:#ca352c;}
        .orderGoods .info p.coupon{
            color:#ED6B09;
        }
    </style>
<div class="headerBg">
	<div class="back" onclick="javascript:history.go(-1)"></div>
	<div class="title">订单确认</div>
</div>
<form id="subForm" method="post" action="/order/pay">
    <input type="hidden" value="{{$id}}" name="returnWay">
<div class="content orderDetail">
    <div class="addressLink lineB">
        <a href="/addresses" class="linkArrow">
        @if(!empty($UserAddress))
            <dl>
                <dt>收货人：{{$UserAddress->name}} <span style="float:right;">{{$UserAddress->mobile}}</span></dt>
                <dd>收货地址：{{$UserAddress->province}} {{$UserAddress->city}}{{$UserAddress->district}}</dd>
            </dl>
            <span class="arrow"></span>
        <input type="hidden" name="receiver_id" value="{{$UserAddress->id}}" />
        @else
            <dl>
                <dd style="line-height: 40px;">添加收货地址</dd>
            </dl>
            <input type="hidden" name="receiver_id" value="" />
        @endif
        </a>
    </div>
    @forelse($data['list'] as $k=>$v)
    <input type="hidden" name="supplier_id[]" value="{{$v['supplier_id']}}">
    <div class="lineB">
        <h3>{{$v['store_name']}}</h3>
        <div class="orderGoods lineB mb-6">
            @forelse($v['goods'] as $vv)
            <dl class="info lineB">
                <dt>
                    @if(isset($vv['image']))
                        <img src="{{$vv['image']}}">
                    @endif
                </dt>
                <dd>
                    <p class="name">{{$vv['title']}}</p>
                    <p>规格：{{$vv['spec']}}</p>
                    <p class="price">￥{{$vv['price']}}</p>
                    <span class="amount">数量x{{$vv['num']}}</span>
                    @if(isset($vv['coupon_state']))
                        <p class="description coupon">{{$vv['coupon_state']}}</p>
                    @endif
                </dd>
            </dl>
            @empty
            @endforelse
            @forelse($vv['gift'] as $gift)
            <a href="javascript:void(0)" class="linkArrow">
                <dl><dt>活动赠品：</dt>
                    <dd>{{$gift['title']}}</dd>
                </dl><span class="arrow"></span>
            </a>
            @empty
            @endforelse
        </div>
        @if($v['coupon_exits'] == 1)
            <a href="javascript:void(0)" class="linkArrow couponList" sid="{{$v['supplier_id']}}">
                <dl><dt class="couponOrder{{$v['supplier_id']}}" style="color: #ED6B09">优惠券：满{{$v['coupon_order']}}元减{{$v['coupon_amount']}}</dt></dl>
                <span class="arrow"></span>
            </a>
            <input type="hidden" class="couponId_{{$v['supplier_id']}}"  name="couponId[]" value="{{$v['coupon_user_id']}}"/>
            <input type="hidden" class="coupon_amount_{{$v['supplier_id']}}" name="coupon_amount[]" value="{{$v['coupon_amount']}}"/>
        @endif
        @if($v['coupon_exits'] == 2)
            <a href="javascript:void(0)" class="linkArrow" sid="{{$v['supplier_id']}}">
                <dl><dt class="couponOrder{{$v['supplier_id']}}" style="color: #ED6B09;">暂无可使用的优惠券</dt></dl>
                <input type="hidden" class="couponId_{{$v['supplier_id']}}"  name="couponId[]" value="0"/>
                <input type="hidden" class="coupon_amount_{{$v['supplier_id']}}" name="coupon_amount[]" value="0"/>
                <span class="arrow"></span>
            </a>
        @endif
        @if($v['coupon_exits'] == 0)
            <input type="hidden" class="couponId_{{$v['supplier_id']}}"  name="couponId[]" value="0"/>
            <input type="hidden" class="coupon_amount_{{$v['supplier_id']}}" name="coupon_amount[]" value="0"/>
        @endif
        @if($v['is_pick_up'] == 1)
            <a href="javascript:void(0)" class="linkArrow expressTypeA"  sid="{{$v['supplier_id']}}">
                <dl><dt>配送方式</dt></dl>
                @if($v['express_type_select'] == 2)
                    <input type="hidden" name="express_amount[]" class="express_amount_every{{$v['supplier_id']}}" value="{{$v['express_amount']}} "/>{{--运费--}}
                    <span class="arrow" id="express_type_text_{{$v['supplier_id']}}">快递￥{{$v['express_amount']}}</span>
                @else
                    <span class="arrow" id="express_type_text_{{$v['supplier_id']}}">包邮</span>
                    <input type="hidden" name="express_amount[]" class="express_amount_every{{$v['supplier_id']}}" value="0"/>{{--运费--}}
                @endif
            </a>
            <input type="hidden" name="express_type_{{$v['supplier_id']}}" value="@if(($v['amount'] - $v['total_amount']) < 0) 2 @else 0 @endif"/>
        @endif

        @if($v['is_pick_up'] == 0)
            <a href="javascript:void(0)" class="linkArrow "  sid="{{$v['supplier_id']}}">
                <dl><dt>配送方式</dt></dl>
                @if($v['express_type_select'] == 2)
                    <input type="hidden" name="express_amount[]" value="{{$v['express_amount']}} "/>{{--运费--}}
                    <span class="arrow" id="express_type_text_{{$v['supplier_id']}}">快递￥{{$v['express_amount']}}</span>
                @else
                    <input type="hidden" name="express_amount[]" value="0"/>{{--运费--}}
                    <span class="arrow" id="express_type_text_{{$v['supplier_id']}}">包邮</span>
                @endif
            </a>
            <input type="hidden" name="express_type_{{$v['supplier_id']}}" value="0"/>
        @endif

        @if($v['express_type_select'] == 2)
            <div style="padding: 15px;background: #fff;border-top: 1px solid #eee;color: #ED6B09">全店满{{$v['total_amount']}}元包邮，还差{{bcsub($v['total_amount'],$v['amount'],2)}}元</div>
        @endif

        <dl class="message lineT">
            <dt>买家留言</dt>
            <dd>
                <textarea class="msg" placeholder="选填：对本次交易的说明...100字以内" name="buyer_message_{{$v['supplier_id']}}"></textarea>
            </dd>
        </dl>
        <div class="total lineT">
            <span>共{{$v['num']}}件商品</span>
            <span>小计：
                <b class="price minTotalAmountbox">
                    ￥<span class="minTotalAmount{{$v['supplier_id']}}" style="color: #ED6B09;margin-left: 0;">{{bcsub(bcadd($v['amount'],$v['express_amount_num'],2),$v['coupon_amount_num'],2)}}</span>
                </b>
            </span>
        </div>

    </div>
        <div class="popupBg"></div>
        <div class="bottomPopup deliveryPopup tr_{{$v['supplier_id']}}" aid="tr_{{$v['supplier_id']}}">
            <dl>
                <dt>配送方式：</dt>
                @if($v['express_type_select'] == 2)
                    <dd class="lineT"><input type="radio" name="express_type"  id="radio{{$k}}{{$v['supplier_id']}}" value="2"><label for="radio{{$k}}{{$v['supplier_id']}}" >快递<span class="express{{$v['supplier_id']}}">{{$v['express_amount']}}</span></label></dd>
                @else
                    <dd class="lineT"><input type="radio" name="express_type"  id="radio{{$k}}{{$v['supplier_id']}}" value="0"><label for="radio{{$k}}{{$v['supplier_id']}}" >包邮</label></dd>
                @endif

                @if($v['is_pick_up'] == 1)
                    <dd class="lineT"><input type="radio" name="express_type" id="radio{{$k}}{{$v['supplier_id']+1}}" value="1"><label for="radio{{$k}}{{$v['supplier_id']+1}}">自提</label></dd>
                @endif
            </dl>
            <input type="button" class="close button" value="确定" supplier_id="0" style="color: #fff;background-color: #ff8700" name="closeBtn">
        </div>
        @if($v['coupon_exits'] == 1)
            <div class="bottomPopup deliveryPopup coupon_{{$v['supplier_id']}}" aid="coupon_{{$v['supplier_id']}}">
                <dl style="padding: 0;">
                    <dt  style="padding-left: 20px;padding-right: 20px;">选择优惠券：</dt>
                    <dd style="background: #EEEEEE;padding:10px 0;height:270px;overflow-y: scroll;">
                    @foreach($v['coupon_base'] as $couponUser)
                        <div class="couponBox @if($couponUser['amount_order'] <= $v['amount'])couponBut @endif" coupon-user-id="{{$couponUser['id']}}" data-title="满{{$couponUser['amount_order']}}元减{{$couponUser['amount_coupon']}}">
                            <div class="subLeft">
                                <span class="price">￥<span class="PriceNum">{{number_format($couponUser['amount_coupon'],0)}}</span></span><br />
                                <span>满{{$couponUser['amount_order']}}可用</span>
                            </div>
                            <div class="decorate"><span style="top:-13px;"></span><span style="bottom:-20px;"></span></div>
                            <div class="subRight">
                                <div class="title">
                                    仅指定商品可用
                                </div>
                                <div class="yyg-color9">有效期{{date('Y:m:d',$couponUser['amount_order'])}}-{{date('Y:m:d',$couponUser['amount_order'])}}</div>
                            </div>
                        </div>



                    @endforeach
                    </dd>
                </dl>
                <input type="button" class="couponBut button" value="返回" supplier_id="0" style="color: #fff;background-color: #ff8700" name="closeBtn">
            </div>
        @endif
    @empty
    @endforelse
</div>
<div class="footButton orderFoot lineT">
    合计：<span class="price">￥<span class="totalAmount" style="font-size: 20px;">{{$data['total_amount']}}</span></span><input type="button" class="button" value="提交订单" id="subFormBtn">
</div>
</form>
@endsection
@section('javascript')
<script>
    $(".msg").keyup(function(){
        if($(this).val().length > 100){
            $(this).val( $(this).val().substring(0,100) );
        }
    });

    $("#subFormBtn").click(function(){
        var receiver_id = $("input[name = 'receiver_id']").val();
        if(receiver_id){
            $("#subForm").submit();
        }else{
            var info = '请添加地址';
            information(info);
        }

    });

    $(".couponList").click(function(){
        var i = $(this).attr("sid");
        $(".popupBg").show();
        $(".coupon_"+i).slideDown();
        $(".popupBg").attr("supplier_id",i);
    });
    $(".couponBut,.popupBg").click(function(){
        var i = $(".popupBg").attr("supplier_id");
        $(".coupon_"+i).slideUp();
        $(".popupBg,.popupWrap").hide();
    });
    $(".couponBut").click(function(){
        var bgid = $(".popupBg").attr("supplier_id");
        var id = $(this).attr("data-coupon");
        $(".coupon_"+bgid).slideUp();
        $(".popupBg,.popupWrap").hide();
        var couponId = $(this).attr("coupon-user-id");
        var newCouponAmount = $(this).find(".PriceNum").text();
        var couponAmount = $(".coupon_amount_"+bgid).val();
        var minTotalAmount = ($(".minTotalAmount"+bgid).text())*1
        var newMinTotalAmount = minTotalAmount+(couponAmount*1)-(newCouponAmount*1);
        $(".couponId_"+bgid).val(couponId);
        $(".coupon_amount_"+bgid).val(newCouponAmount);
        $(".minTotalAmount"+bgid).text(newMinTotalAmount.toFixed(2));
        var title = '优惠券：'+ $(this).attr("data-title");
        $(".couponOrder"+bgid).text(title);
        newTotalAmount();
    });
    function newTotalAmount(){
        var newNum = 0;
        $(".minTotalAmountbox").each(function(e){
            var num = $(".minTotalAmountbox").eq(e).find("span").text();
            newNum = newNum+(num*1);
        });
        $(".totalAmount").text(newNum.toFixed(2));
    }

//    couponId_

    $(".expressTypeA").click(function(){
        var i = $(this).attr("sid");
        $(".popupBg").show();
        $(".tr_"+i).slideDown();
        $("input[name=closeBtn]").attr("supplier_id",$(this).attr("sid"));
        $(".popupBg").attr("supplier_id",$(this).attr("sid"));
    })

    $(".close,.popupBg").click(function(){
        $(".popupBg,.popupWrap").hide();
        $(".bottomPopup").slideUp();
        var supplier_id = $(this).attr("supplier_id");
        var express_type = $("input[name=express_type]:checked").val();
        $("input[name=express_type_"+supplier_id+"]").val(express_type);
        var express = ($(".express"+supplier_id).text())*1;
        var express_type_text = '包邮';
        if(express_type == "0"){
            calculation(supplier_id);
            $("#express_type_text_"+supplier_id).attr("isReduce","isReduce");
            $(".express_amount_every"+supplier_id).val(0);
        }
        if(express_type == "1"){
            express_type_text = '自提';
            calculation(supplier_id);
            $("#express_type_text_"+supplier_id).attr("isReduce","isReduce");
            $(".express_amount_every"+supplier_id).val(0);
        }
        if(express_type == "2"){
            express_type_text = '快递￥'+express;
            calculation1(supplier_id);
            $(".express_amount_every"+supplier_id).val(express);
            
        }
        $("#express_type_text_"+supplier_id).text(express_type_text);
    })
    //商店总计加快递费
    function calculation1(i){
    	var express = ($(".express"+i).text())*1;
    	var totalAmount = ($(".totalAmount").text())*1;
    	var minTotalAmount = ($(".minTotalAmount"+i).text())*1
    	var isReduce = $("#express_type_text_"+i).attr("isReduce");
    	if(isReduce){
    		$(".totalAmount").text((totalAmount+express).toFixed(2));
    		$(".minTotalAmount"+i).text((minTotalAmount+express).toFixed(2));
    		$("#express_type_text_"+i).attr("isReduce","");
    	}
    }
    //商店总计减快递费
    function calculation(i){
    	var express = ($(".express"+i).text())*1;
    	var totalAmount = ($(".totalAmount").text())*1;
    	var minTotalAmount = ($(".minTotalAmount"+i).text())*1
    	var isReduce = $("#express_type_text_"+i).attr("isReduce");
    	if(!isReduce){
    		$(".totalAmount").text((totalAmount-express).toFixed(2));
    		$(".minTotalAmount"+i).text((minTotalAmount-express).toFixed(2));
    	}
    }

</script>
@endsection