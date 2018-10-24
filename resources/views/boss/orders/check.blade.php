@extends('layout')
<style>
    .goods-nav{font-size:14px;margin-bottom:20px;}
    .goods-nav a{display: inline-block;margin-right: 20px;padding: 5px; text-decoration-line: none !important;}
    .goods-nav .active{border-bottom: 2px solid #4395ff !important;}
    .textbox{text-align: left;float: left;margin-left: 20px;width:60%;}
    .textbox p.title{white-space:nowrap; text-overflow:ellipsis;width: 90%;  overflow: hidden;}
    .textbox p{margin-bottom: 0;}
</style>
@section("content")
    <div class="pd-20">
        <div class="goods-nav">
            <a class="@if($state == \App\Models\OrderReturn::STATE_NO_CHECK) active @endif" href="/orders/check/{{\App\Models\OrderReturn::STATE_NO_CHECK}}">待审核</a>
            <a class="@if($state == \App\Models\OrderReturn::STATE_NO_REFUND) active @endif" href="/orders/check/{{\App\Models\OrderReturn::STATE_NO_REFUND}}">待退款</a>
            <a class="@if($state == \App\Models\OrderReturn::STATE_SUCCESS) active @endif" href="/orders/check/{{\App\Models\OrderReturn::STATE_SUCCESS}}">已退款</a>
            <a class="@if($state == \App\Models\OrderReturn::STATE_REFUSE) active @endif" href="/orders/check/{{\App\Models\OrderReturn::STATE_REFUSE}}">已驳回</a>

            <div class="search">
                <form>

                    <table class="table table-border table-bordered table-bg">
                        <tr>
                            <td>订单编号:<input type="text" class="input-text ml-10 mr-10" style="width:57%" value="{{$tmp['order_no'] }}"  name="order_no"><input type="hidden" name="state" value="{{$state}}"></td>
                            <td>收货人姓名:<input type="text" class="input-text ml-10 mr-10" style="width:250px" value="{{$tmp['name'] }}"  name="name"></td>
                            <td>收货人手机:<input type="text" class="input-text ml-10 mr-10" style="width:250px" value="{{$tmp['mobile'] }}"  name="mobile"></td>
                            
                            <td><input type="submit" class="btn btn-danger" value="筛选"></td>
                            <td><a href='/orders/return/export/<?php echo $state?>?order_no={{$tmp['order_no'] }}&name={{$tmp['name']}}&mobile={{$tmp['mobile']}}' class="btn btn-success">导出</a></td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>


        <table class="table table-border table-bordered table-bg">
            <thead>

            <tr class="text-c">
                <th width="5%">用户ID</th>
                <th width="25%">商品</th>
                <th width="5%">数量</th>
                <th>运费</th>
                <th>订单金额</th>
                <th width="5%">实付</th>
                <th>状态</th>
                <th>下单时间</th>
                <th>发货时间</th>
                <th>收款账户</th>
                <th>配送方式</th>
                <th>收货人信息</th>
                <th width="10%" colspan="2">操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($orders as  $order)
                <tr class="text-c">
                    <td>
                        {{$order->uid}}
                    </td>
                    <td>
                        <div>
                            <div>订单编号：{{$order->order_no}}</div>
                            <div>
                                @foreach($order->tmp as $good)
                                    <div class="mb-10 mt-10 oh" style="width: 350px;">
	                                    <div class="f-l"><img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$good[2]['bannerFirst']['name']}}"  class="radius" width="80px" height="80px"></div>
	                                    <div class="f-l ml-10" style="text-align: left;width: 260px;">
	                                        <p class="title">{{$good[0]['goods_title']}}</p>
	                                        <p>￥{{$good[0]['price']}}</p>
	                                        <p>{{$good[0]['packname']}}</p>
	                                        <p>规格：{{$good[0]['spec_name']}}</p>
	                                        </br>
	                                    </div>
                                    </div>
                                @endforeach
                                <div style="text-align: left;color: red">
                                    <p>供应商名称：{{$order->supplier_name}}</p>
                                    <p>供应商号码：{{$order->supplier_mobile}}</p>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @foreach($order->tmp as $good)
                        {{$good[0]['num']}}</br>
                        @endforeach
                    </td>
                    <td>{{$order->amount_express}}</td>
                    <td>{{$order->amount_goods}}</td>
                    <td>{{$order->amount_real}}</td>
                    <td>
                        @if($state == \App\Models\OrderReturn::STATE_SUCCESS)
                                                                                 已退款
                        @endif
                        @if($state == \App\Models\OrderReturn::STATE_REFUSE)
                                                                                 已驳回
                        @endif
                        @if($state == \App\Models\OrderReturn::STATE_NO_CHECK)
                                                                                  待审核
                        @endif
                        @if($state == \App\Models\OrderReturn::STATE_NO_REFUND)
                                                                                 待退款
                        @endif
                    </td>
                    <td>{{$order->created_at_order}}</td>
                    <td>{{$order->express_time}}</td>
                    <td>
                        @if($order->pay_type ==\App\Models\OrderBase::PAY_TYPE_ALI)
                           ping++支付
                        @endif
                        @if($order->pay_type ==\App\Models\OrderBase::PAY_TYPE_WX)
                           ping++支付
                        @endif
                        @if($order->pay_type ==\App\Models\OrderBase::Pay_TYPE_WX_JS) 
                                                                                 微信商户支付  
                        @endif                
                    </td>
                    <td>{{$order->express_type}}</td>
                    <td>
                        {{$order->name}}</br>
                        {{$order->mobile}}</br>
                        {{$order->receiver_info->province}}{{$order->receiver_info->city}}{{$order->receiver_info->district}}{{$order->receiver_info->address}}
                    </td>
                    <td>
                        <a title="查看" href="javascript:;" onclick="a( '订单详情', '/orders/checkDetail/{{$order->order_no}}')" class="ml-5" style="text-decoration:none">查看</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <span style="float: left;line-height: 70px;margin: 0 20px">共{{$orders->lastPage()}}页,{{$orders->total()}}条数据 ；每页显示{{$orders->perPage()}}条数据</span>
        <?php echo $orders->appends(['order_no'=>$tmp['order_no'],'name'=>$tmp['name'],'mobile'=>$tmp['mobile']])->render(); ?>
    </div>
@endsection

@section("javascript")
    <script>
        function a(title,url){
            layer_show(title,url)
        }


    </script>
@endsection