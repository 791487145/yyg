@extends('wx.layout')
@section('title')
    购物车
@endsection
@section('content')
    <style>
        .cartDetail .orderGoods .info p.coupon{
            color:#ED6B09;
        }
        .cartState {
		    width: 50px;
		    height:50px;
		    line-height:50px;
		    position: absolute;
		    -webkit-transform: translate(-50%, -50%);
		    transform: translate(-50%, -50%);
		    left: 50%;
		    top: 50%;
		    text-align: center;
		    background: rgba(0,0,0,0.5);
		    border-radius: 50%;
		    -webkit-border-radius: 50%;
		    color: #fff;
		    font-size: 12px;
		}
		.cartDetail .orderGoods .info .amount{bottom:30px;}
		.limit{position: absolute;right: 14px; bottom:8px;font-size:12px;color:#ED6B09;}
		
		.cartDetail .orderGoods .cartStateNo{color: #999;}
		.cartDetail .orderGoods .cartStateNo .name{color: #999;}
		.cartDetail .orderGoods .cartStateNo p{color: #999;}
		.orderGoods .cartStateNo p.price{color: #999;}
		.cartStateNo .limit{color:#999;}
		.cartStateNo .amountInput input{color: #999;}
		
		
    </style>
    @if(empty($Suppliers['list']))
        @include('wx.cart.cart_null')
    @endif
<div class="content orderDetail cartDetail">
    <div class="orderTop allOrderTop checkBox lineB mb-6">
        <input type="checkbox" name="checkbox" class="allCheck" id="allCheck">
        <label for="allCheck">全部商品</label>
        <span class="right lineL">
            <input type="button" value="编辑" class="edit">
        </span>
    </div>
    @foreach($Suppliers['list'] as $key=> $Supplier)
    <div class="lineB orderGroup supplier{{$key}}">
        <div class="orderTop checkBox lineB"><input type="checkbox" data-key="{{$key}}" name="checkbox" class="allChildCheck" value="" id="allChildCheck{{$key+1}}"><label for="allChildCheck{{$key+1}}">{{$Supplier['store_name']}}</label></div>
        @foreach($Supplier['goods'] as $k=>$GoodBase)
        <div class="orderGoods lineB mb-6">
        	@if($GoodBase['spec_num'] > 0) 
            <dl class="info lineB">
                <dt>
                    <span class="checkBox">
                        <input type="checkbox" data-key="{{$key}}"  class="checkboxs" name="checkboxs" id="checkbox{{$key+1}}-{{$k+1}}" value="{{$GoodBase['cart_id']}}">
                        <label for="checkbox{{$key+1}}-{{$k+1}}"></label>
                    </span>
                    <a href="/goods/{{$GoodBase['good_id']}}">
	                    <img src="{{$GoodBase['image']}}?imageslim" alt="">
                    </a>
                </dt>
                <dd>
                    <p class="name">{{$GoodBase['title']}}</p>
                    <p>规格：{{$GoodBase['spec']}}</p>
                    <p class="price" data-price="{{$GoodBase['price']}}">￥{{$GoodBase['price']}}</p>
                    @if(isset($GoodBase['coupon_state']) && !isset($Supplier['coupon_base']))
                        <p class="coupon">{{$GoodBase['coupon_state']}}</p>
                    @endif
                    @if(isset($Supplier['coupon_base']) && isset($GoodBase['coupon_state']))
                        <p class="coupon">满{{$Supplier['coupon_base'][0]['amount_order']}}减{{$Supplier['coupon_base'][0]['amount_coupon']}}
                            @if(count($Supplier['coupon_base']) > 1) ...@endif
                        </p>
                    @endif
                    <span class="amount amountInput">
                        <input type="button" class="minus" data-key="{{$key}}" data-k="{{$k}}">
                        <input type="hidden" name="cart_id" value="{{$GoodBase['cart_id']}}">
                        <input type="number" data-key="{{$key}}" data-k="{{$k}}" onkeyup="this.value=this.value.replace(/^[0]\d*$/,'')" value="{{$GoodBase['num']}}" class="number number{{$key}}{{$k}}">
                        <input type="button" class="add" data-key="{{$key}}" data-k="{{$k}}">
                    </span>
                    @if(isset($GoodBase['spec_limit']))
                        <span class="limit">每个ID限制购买<span class="limit{{$key}}{{$k}}">{{$GoodBase['spec_limit']}}</span>件</span>
                    @endif
                </dd>
            </dl>
            @else
            <dl class="info lineB cartStateNo">
                <dt>
                    <span class="checkBox">
                        <label for="checkbox{{$key+1}}-{{$k+1}}"></label>
                    </span>
                    <a href="/goods/{{$GoodBase['good_id']}}">
                    	<div style="position: relative;display: inline-block;">
	                            <div class="cartState">已售罄</div>
	                        <img src="{{$GoodBase['image']}}?imageslim" alt="">
                    	</div>
                    </a>
                </dt>
                <dd>
                    <a href="/goods/{{$GoodBase['good_id']}}"><p class="name">{{$GoodBase['title']}}</p>
                    <p>规格：{{$GoodBase['spec']}}</p>
                    <p class="price" data-price="{{$GoodBase['price']}}">￥{{$GoodBase['price']}}</p></a>
                    @if(isset($GoodBase['coupon_state'])  && !isset($Supplier['coupon_base']))
                        <p class="coupon">{{$GoodBase['coupon_state']}}</p>
                    @endif

                    @if(isset($Supplier['coupon_base']) && isset($GoodBase['coupon_state']))
                        <p class="coupon">满{{$Supplier['coupon_base'][0]['amount_order']}}减{{$Supplier['coupon_base'][0]['amount_coupon']}}
                            @if(count($Supplier['coupon_base']) > 1) ...@endif
                        </p>
                    @endif
                    <span class="amount amountInput">
                        <input type="button" disabled="disabled" class="minus" data-key="{{$key}}" >
                        <input type="hidden" name="cart_id" value="{{$GoodBase['cart_id']}}">
                        <input type="number" disabled="disabled" data-key="{{$key}}" onkeyup="this.value=this.value.replace(/^[0]\d*$/,'')" value="{{$GoodBase['num']}}" class="number">
                        <input type="button" disabled="disabled" class="add" data-key="{{$key}}">
                    </span>
                    @if(isset($GoodBase['spec_limit']))
                        <span class="limit">每个ID限制购买{{$GoodBase['spec_limit']}}件</span>
                    @endif
                </dd>
            </dl>
            @endif
            
            @if(!empty($GoodBase['gift']))
                @foreach($GoodBase['gift'] as $gift)
                    <dl class="info lineB">
                    <dt class="br4 lineT" style="margin-left: 26px">
                        <span class="gifts">活动赠品</span>
                        <a href="/goods/{{$gift['id']}}">
                            <img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$gift['cover_image']}}?imageslim" alt="">
                        </a>
                    </dt>
                    <dd>
                        <a href="/goods/{{$gift['id']}}"><p class="name">{{$gift['title']}}</p>
                            <p>规格：{{$gift['spec_name']}}</p>
                            <p class="price" data-price="">￥0</p>
                        </a>
                    <span class="amount amountInput">
                        <input type="text" value="1"  readonly>
                    </span>
                    </dd>
                    </dl>
                @endforeach
            @endif
        </div>
        @endforeach
        @if($Supplier['total_amount'] != 0)
        <a href="/supplier/{{$Supplier['supplier_id']}}" class="linkArrow" style="margin-bottom:30px;">
            <dl><dt style="color: #ED6B09; ">满<span class="totalAmount">{{$Supplier['total_amount']}}</span>包邮，还差<span class="needAmount">{{$Supplier['total_amount']}}</span>元  </dt></dl>
            <span class="arrow" id="express_type_text_{{$key}}">去凑单</span>
        </a>
        @endif
    </div>
    @endforeach

</div>

<div class="cartFoot lineT">
    共 {{$count}} 件商品
    <span class="right">
        ￥<span id="amount" style="font-size: 16px;">0</span>
        <input type="button" class="btnOrange" value="去结算" onclick="order()">
    </span>
</div>
<div class="cartFoot lineT" style="display:none">
    已选：<span id="goodSelect">0</span> 件商品
    <span class="right">
        <input type="button" class="btnOrange delete" value="删除" onclick="deletes()">
    </span>
</div>
<div class="popupBg"></div>
<div class="popupWrap deletePopup">
    <h3>移除商品</h3>
    <p>是否将该商品移出购物车</p>
    <div class="bottomButtonGroup lineT lineR">
        <button class="close button">取消</button>
        <button class="close button" id="del">确定</button>
    </div>
</div>
@section('bottom_bar')
    @include('wx.bottom_bar')
@endsection

@endsection
@section('javascript')
    <script>
        function totalAmount (id){
            var munbox=[];
            $('.supplier'+id+' input[name="checkboxs"]:checked').each(function(e){
                var price=$(this).parent().parent().siblings("dd").find(".price").attr("data-price");
                var mun=$(this).parent().parent().siblings("dd").find("input[type='number']").val();
                mun = Math.floor(mun);
                var a = price * mun;
                munbox.push(a);

            });

            var num = 0;
            for(var i= 0;i<munbox.length;i++){
                num = munbox[i]*1 + num;
            }
            var allAmount = $('.supplier'+id+' .totalAmount').text();
            var needPrice = allAmount*1-num;
            if($('.supplier'+id+' input[name="checkboxs"]:checked').length<1){
                $('.supplier'+id+' .needAmount').text(allAmount);
                $("#express_type_text_"+id).text("去凑单");
                return false;
            }
            if(needPrice>0){
                $('.supplier'+id+' .needAmount').text(needPrice.toFixed(2));
                $("#express_type_text_"+id).text("去凑单");
            }else {
                $('.supplier'+id+' .needAmount').text("0");
                $("#express_type_text_"+id).text("去逛逛");
            }

        }



        function deletes(){
            var checked = $('input[name="checkboxs"]:checked').val()
            if(checked){
                popupShow('deletePopup')
            }else{
                information("请选择要删除的商品");
            }
        }

        $(function(){
            $('.minus').click(function(){
                var cart_id = $(this).next().val();
                var cartGooodNum = $(this).next().next().val();
                var key = $(this).attr("data-key");
                var k = $(this).attr("data-k");
                var isLimitNum = limitNum(key,k,cartGooodNum);
                if(!isLimitNum){return false;}
                $.post('/carts',{cart_id:cart_id,num:cartGooodNum,open_id:"{{Cookie::get('openid')}}"},function(msg){
                    pricebox ();
                    cartNum(msg.count)
                    totalAmount (key)
                })
            })
            $(".number").blur(function(){
                var cartGooodNum = $(this).val();
                var cart_id = $(this).prev().val();
                if(cartGooodNum == ''){
                    $(this).val(1);
                    var cartGooodNum = $(this).val();
                }
                var key = $(this).attr("data-key");
                var k = $(this).attr("data-k");
                var isLimitNum = limitNum(key,k,cartGooodNum);
                if(!isLimitNum){return false;}
                $.post('/carts',{cart_id:cart_id,num:cartGooodNum,open_id:"{{Cookie::get('openid')}}"},function(msg){
                    pricebox ();
                    cartNum(msg.count)
                    totalAmount (key);
                })
            })

            $('.add').click(function(){
                var cart_id = $(this).prev().prev().val();
                var cartGooodNum = $(this).prev().val();
                var key = $(this).attr("data-key");
                var k = $(this).attr("data-k");
                var isLimitNum = limitNum(key,k,cartGooodNum);
                if(!isLimitNum){return false;}
                $.post('/carts',{cart_id:cart_id,num:cartGooodNum,open_id:"{{Cookie::get('openid')}}"},function(msg){

                    if(msg.ret == 'yes'){
                        console.log(msg);
                        pricebox ();
                        cartNum(msg.count)
                        totalAmount (key);
                    }
                    if(msg.ret == 'no'){
                        var info = '数量超过上限';
                        information(info);
                    }
                })
            })
            
            function limitNum(key,k,num){
            	var limit = ($(".limit"+key+k).text())*1;
            	if(limit > 0 && num > limit){
            		 information('数量超过限购上限');
            		 $(".number"+key+k).val(limit);
            		 return false;
            	}else{
            		return true;
            	}
            }
            $(".allCheck").click(function(){
                $(".orderGroup").each(function(e){
                    totalAmount (e);
                })
            })
            $(".checkboxs,.allChildCheck,.allCheck").click(function(){
                var key = $(this).attr("data-key");
                totalAmount (key);
                pricebox ();
                var munbox=[];
                $('input[name="checkboxs"]:checked').each(function(e){
                    munbox.push($(this).val());
                });
                $.post('/cart/selected',{cart_id:munbox},function(msg){
                    //console.log(msg.id);
                })

            })
            function pricebox (){
                var munbox=[];
                $('input[name="checkboxs"]:checked').each(function(e){
                    var price=$(this).parent().parent().siblings("dd").find(".price").attr("data-price");
                    var mun=$(this).parent().parent().siblings("dd").find("input[type='number']").val();
                    mun = Math.floor(mun);
                    var a = price * mun;
                    munbox.push(a);

                });

                var dfsd = 0;
                for(var i= 0;i<munbox.length;i++){
                    dfsd = munbox[i]*1 + dfsd;
                }
                $('#amount').text(dfsd.toFixed(2));
                $('#goodSelect').text(munbox.length);

            }

            $("#del").click(function(){
                var cart_id = [];
                $('input[name="checkboxs"]:checked').each(function(){
                    var sfruit=$(this).val();
                    cart_id.push(sfruit);
                });
                var num = cart_id.length;

                $.post('/cart',{cart_id:cart_id},function(msg){
                    if(msg.ret == 'yes'){
                        information('删除成功');
                        window.location.reload();
                    }
                })
            })
        })

        function order(){
            var cart_id = [];
            $('input[name="checkboxs"]:checked').each(function(){
                var sfruit=$(this).val();
                cart_id.push(sfruit);
            });

            if(cart_id == ''){
                information('请选择商品');
                return false;
            }

            $.get('/order/carts',function(msg){
                if(msg.ret == 'no'){
                    information(msg.info);
                }
                if(msg.ret == 'order'){
                    location.href='/order/cart/0';
                }

            })

        }
    </script>
@endsection