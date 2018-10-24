@extends('layout')
<style>
    .text-c a{color: #0000cc;}
    .goods-nav .active{
        border-bottom: 2px solid #4395ff !important;
    }

    .margin20{margin: 20px;}
    .goods-nav{font-size:14px;margin-bottom:20px;}
    .goods-nav a{display: inline-block;margin-right: 20px;padding: 5px; text-decoration-line: none !important;}
    .width-4-1{width:23%;float: left;padding:20px 1%;}
    .titleText{width:400px;display: inline-block;}
</style>
@section("content")
    <div style="overflow: hidden;">
        @if($action == 1)
            <div class="width-4-1">供应商账户余额：<span>￥{{$body->amount}}</span></div>
            <div class="width-4-1">供应商待入账余额：<span>￥{{$waitToAmount}}</span></div>
        @elseif($action == 2)
            <div class="width-4-1">导游账户余额：<span>￥{{$body->amount}}</span></div>
            <div class="width-4-1">导游待入账余额：<span>￥{{$waitToAmount}}</span></div>
        @else
            <div class="width-4-1">旅行社账户余额：<span>￥{{$body->amount}}</span></div>
            <div class="width-4-1">旅行社待入账余额：<span>￥{{$waitToAmount}}</span></div>
        @endif
        <div class="width-4-1">本次提现金额为：<span>￥{{$amount}}</span></div>
        <div class="width-4-1">本次提现订单数：<span>{{$billingSourceCount}}</span></div>
    </div>
    <form method="post">
        <div class="text-l margin20">
            单号：<input type="text" name="order_no" value="<?php echo $order_no?>" placeholder="单号" style="width:250px" class="input-text">&nbsp;&nbsp;&nbsp;
            交易编号：<input type="text" name="trade_no" value="<?php echo $trade_no?>" placeholder="交易编号" style="width:250px" class="input-text">
            <button class="btn btn-success" type="submit"><i class="Hui-iconfont"></i> 筛选</button>
            <a href='/withdraw/export/{{$actionId}}/{{$action}}/{{$id}}/{{$state}}/{{$amount}}?order_no=<?php echo $order_no?>&trade_no=<?php echo $trade_no?>&billingSourceCount=<?php echo $billingSourceCount?>' name="" id="" class="btn btn-success">导出</a>
        </div>
    </form>
    <div class="goods-nav margin20">
        <a class="active">本次申请提现的订单</a>
    </div>

<table class="table table-border table-bordered table-bg">
    <thead>
    <tr class="text-c">
        <th>单号</th>
        <th>交易编号</th>
        <th>收款账户</th>
        <th>商品名称</th>
        <th>成交数量（件）</th>
        @if($action == 1)
            <th>供应单价</th>
            <th>运费（元）</th>
            <th>退款金额</th>
            <th>订单总供应价</th>
        @elseif($action == 2)
            <th>运费（元）</th>
            <th>订单总金额</th>
            <th>退款金额</th>
            <th>导游返利（元）</th>
        @else
            <th>运费（元）</th>
            <th>订单总金额</th>
            <th>退款金额</th>
            <th>旅行社返利（元）</th>
        @endif
    </tr>
    </thead>
    <tbody>

    @foreach($infos as $info)
    <tr class="text-c">
        <td><a href="javascript:;" onclick="show_order_detail('订单详情','/orders/ordersDetail/{{$info->order_no}}')">{{$info->order_no}}</a></td>
        <td>{{$info->trade_no}}</td>
        <td>{{$info->payType}}</td>
        <td>
            @if(is_array($info->goodsTitle))
            @foreach($info->goodsTitle as $value)
                {{$value}}<br/>
            @endforeach
            @else
                {{$info->goodsTitle}}
            @endif
        </td>
        <td>@if(is_array($info->goodsNumSum))
                @foreach($info->goodsNumSum as $v)
                    {{$v}}<br/>
                @endforeach
             @endif
        </td>

        @if($action == 1)
            <td>@if(is_array($info->goodsSupplierPrice))
                    @foreach($info->goodsSupplierPrice as $key=>$value)
                        {{$value}} @if(!empty($info->isChangePriceBuying[$key])) <a href="javascript:;" onclick="show_change('供应价改价记录','/withdraw/price/change/record/{{$info->isChangePriceBuying[$key]}}')"><i style="font-size: 15px;color: red" class="Hui-iconfont Hui-iconfont-tishi"></i></a>@endif<br/>
                    @endforeach
                @else
                    {{$info->goodsSupplierPrice}}
                @endif</td>
            <td>{{$info->amount_express}}</td>
            <td>{{$info->return_amount}}</td>
            <td>
                {{$info->orderSupplierPrice}}
                @if(!empty(intval($info->coupon_amount)))
                    <a href="javascript:;" onclick="layer.alert('用户支付已使用优惠券{{$info->coupon_amount}}元，订单供应价为{{$info->orderSupplierPrice}}（{{$info->totalAmount}}-{{$info->coupon_amount}}）')"><i style="font-size: 15px;color: red" class="Hui-iconfont Hui-iconfont-tishi"></i></a>
                    @endif
            </td>
        @elseif($action ==2 )
            <td>{{$info->amount_express}}</td>
            <td>{{$info->amount_goods}}</td>
            <td>{{$info->return_amount}}</td>
            <td>{{$info->rebate}}</td>
        @elseif($action == 3)
            <td>{{$info->amount_express}}</td>
            <td>{{$info->amount_goods}}</td>
            <td>{{$info->return_amount}}</td>
            <td>{{$info->rebate}}</td>
        @endif
    </tr>
    @endforeach

    </tbody>
</table>
    @if($state == 15)
        <div>
            <div class="margin20">
                <span class="titleText">审核结果：采购审核通过</span>
                审核时间：<span>{{$billingWithdrawInfo->updated_at}}</span>
            </div>
            <div class="margin20">
                审核负责人：<span>@if($auditorInfo){{$auditorInfo->auditor}}@endif</span>  审核无误，同意提现！</div>
            <div class="margin20">提现金额：<span>￥{{$billingWithdrawInfo->amount}}</span></div>
        </div>
    @endif
    @if($state == 12 || $state == 13)
        <div>
            <div class="margin20">
            	<span class="titleText">审核结果：采购审核通过</span>
            	审核时间：<span>{{$billingWithdrawInfo->updated_at}}</span>
            	</div>
            <div class="margin20">
            	审核负责人：<span>@if($auditorInfo){{$auditorInfo->auditor}}@endif</span>  审核无误，同意提现！</div>
            <div class="margin20">提现金额：<span>￥{{$billingWithdrawInfo->amount}}</span></div>
        </div>
        @if(isset($auditorInfo->finance_auditor))
            <div>
                <div class="margin20">
                    <span class="titleText">审核结果：财务审核通过，待打款</span>
                    审核时间：<span>{{$billingWithdrawInfo->updated_at}}</span>
                </div>
                <div class="margin20">
                    审核负责人：<span>@if($auditorInfo){{$auditorInfo->finance_auditor}}@endif</span>  审核无误，同意提现！</div>
                <div class="margin20">提现金额：<span>￥{{$billingWithdrawInfo->amount}}</span></div>
            </div>
        @endif
    @endif
    @if($state == 13)
        <div>
            <div class="margin20">
            	<span class="titleText">财务打款结果：已打款</span>  
            	打款时间：<span>{{$billingWithdrawInfo->updated_at}}</span></div>
            <div class="margin20">打款金额：￥{{$billingWithdrawInfo->amount}}</div>
        </div>
    @endif
    @if($state == 14)
        <div>
            <div class="margin20">
            	<span class="titleText">审核结果：审核不通过，已驳回 </span>
            	审核时间：<span>{{$billingWithdrawInfo->updated_at}}</span>
            </div>
            <div class="margin20">驳回原因：@if($auditorInfo){{$auditorInfo->rejectReason}}@endif</div>
            <div class="margin20">提现金额：<span>￥{{$billingWithdrawInfo->amount}}</span></div>
        </div>
    @endif
<div class="cl pd-5 bg-1 bk-gray mt-20">
    @if($state == 11)
    <span class="l margin20">
        <a href="javascript:;" onclick="reject('请填写驳回原因','/withdraw/sms/reject/<?php echo $action?>',<?php echo $id?>)" class="btn btn-danger radius"> 审核不通过并驳回</a>
        <a class="btn btn-primary radius" onclick="pay('请确定你已审核无误，直接输入姓名作为签名，表示同意','/withdraw/sms/pass/<?php echo $action?>',<?php echo $id?>)" href="javascript:;"> 审核通过</a>
        <a class="btn btn-primary radius" onclick="history.go(-1)"> 返回</a>
    </span>
    @endif
        @if($state == 15)
            <span class="l margin20">
        <a href="javascript:;" onclick="reject('请填写驳回原因','/withdraw/sms/reject/<?php echo $action?>',<?php echo $id?>)" class="btn btn-danger radius"> 审核不通过并驳回</a>
        <a class="btn btn-primary radius" onclick="pay('请确定你已审核无误，直接输入姓名作为签名，表示同意','/withdraw/finance/pass/',<?php echo $id?>)" href="javascript:;"> 审核通过</a>
        <a class="btn btn-primary radius" onclick="history.go(-1)"> 返回</a>
    </span>
        @endif
        @if($state == 12)
    <span class="l margin20">
        <a href="javascript:;" onclick="reject('请填写驳回原因','/withdraw/sms/reject/<?php echo $action?>',<?php echo $id?>)" class="btn btn-danger radius"> 审核不通过并驳回</a>
        <a href="javascript:;" onclick="payMoney('确定要打款吗？提现金额：<?php echo $amount?>,确定后资金将直接打入收款账号中。','/withdraw/sms/pay/<?php echo $action?>',<?php echo $id?>)" class="btn btn-success radius"> 手动打款</a>
        <a class="btn btn-primary radius" onclick="history.go(-1)"> 返回</a>
    </span>
        @endif
       
</div>
    @endsection

@section('javascript')
    <script src="{{asset('lib/layer/3.0.3/layer.js')}}"></script>
    <script>

        function reject(title,url,id){
        layer.prompt({title: title, formType: 2,btn:['发送短信','取消']}, function(text, index){
            $.ajax({
                type:'get',
                url:url,
                dataType:'json',
                data:{
                    'reason':text,
                    'id':id
                },
                success:function(data){
//                  parent.location.replace(parent.location.href);
                    window.location.href= document.referrer;
                }
            });

        });
        }

        function pass(title,url,id){
            layer.confirm(title,function(){
                $.ajax({
                    type:'get',
                    url:url,
                    dataType:'json',
                    data:{
                        'id':id
                    },
                    success:function(data){
						window.location.href= document.referrer;
                    }
                });
               
            });
        }

        function pay(title,url,id){
            layer.prompt({title: title, formType: 2,btn:['确定','取消']},function(text,index){
                $.ajax({
                    type:'get',
                    url:url,
                    dataType:'json',
                    data:{
                        'sign':text,
                        'id':id,
                    },
                    success:function(data){
                        layer.msg(data.msg,function(){
                            window.location.href= document.referrer;
                        });
                    }
                });
            });
        }

        function payMoney(title,url,id){
            layer.confirm(title,function(){
                $.ajax({
                    type:'get',
                    url:url,
                    dataType:'json',
                    data:{
                        'id':id
                    },
                    success:function(data){

                    	window.location.href= document.referrer;
                    }
                });
            })
        }

        function show_order_detail(title,url) {
            layer_show(title, url)
        }

        function show_change(title,url){
            layer_show(title,url)
        }



    </script>
@endsection