@extends('supplier')
@section('content')
    <link rel="stylesheet" href="{{asset('lib/imgbox/css/lrtk.css')}}" />
    <script type="text/javascript" src="{{asset('lib/imgbox/js/jquery.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('lib/imgbox/js/jquery.imgbox.pack.js')}}"></script>
    <div class="rightCon">
        <div class="wrap">
            <h2><span>订单详情</span></h2>
            <form class="form orderDetail">
                <div class="box">
                    <h5>订单信息</h5>
                    <table>
                        <tr>
                            <td width="400">订单编号：{{$order->order_no}}</td>
                            <td width="400">订单金额：{{sprintf("%.2f", $order->amount_goods + $order->amount_express)}}</td>
                        </tr>
                        <tr>
                            <td>付款方式：@if($order->pay_type == 1) 支付宝支付 @elseif($order->pay_type == 2) 微信支付 @endif</td>
                            <td>付款时间：{{$order->log}}</td>
                            <td>运费：{{$order->amount_express}}</td>
                        </tr>
                        <tr>
                            <td>付款金额：<span style="color: red;">￥{{$order->amount_real}}</span></td>
                            <td>配送方式：
                                @if($order->express_type == 0)
                                    包邮
                                @else
                                    自提
                                @endif
                            </td>
                        </tr>

                    </table>

                </div>
                <div class="box">
                    <h5>商品信息</h5>
                    @forelse($order->goods as $goods)
                        <table style="padding: 20px;">
                            <tr>
                                <td rowspan="2" width="100"><img src="{{env('IMAGE_DISPLAY_DOMAIN')}}/{{$goods->data->img}}" width="100"></td>
                                <td width="400"><p class="limitText">商品名称：{{$goods->data->title}}</p></td>
                                <td width="400">零售价：{{$goods->spec->price}}</td>
                            </tr>

                            <tr>
                                <td>规格：{{$goods->spec->name}}</td>
                                <td>数量：{{$goods->num}}</td>
                            </tr>
                            <!--赠品信息-->
                            @forelse($goods->data->gift as $gift)
                                <tr>
                                    <td colspan="3">[赠品]:<a href="{{url('goods',$gift->id)}}"  style="color: #8a8989;">{{$gift->title}}</a></td>

                                </tr>
                            @empty
                            @endforelse
                        </table>
                    @empty
                    @endforelse
                </div>


                <div class="box">
                    <h5>收货人信息</h5>
                    <p>
                        <span>{{$order->receiver_name}}</span>
                        <span>{{$order->receiver_mobile}}</span>
                        <span>{{$order->receiver_info->province}}{{$order->receiver_info->city}}{{$order->receiver_info->district}}{{$order->receiver_info->address}}</span>
                    </p>
                </div>
                <div class="box">
                    <table>
                        <tr>
                            @if($order->state == 2 || $order->state == 5)
                                <th width="100">物流公司：</th>
                                <td width="280"><input value="{{$order->express_name}}" name="express_name"></td>
                                <th width="100">物流单号：</th>
                                <td width="280"><input value="{{$order->express_no}}" name="express_no"></td>
                            @else
                                <th width="100">物流公司：</th>
                                <td width="280">{{$order->express_name}}</td>
                                <th width="100">物流单号：</th>
                                <td width="280">{{$order->express_no}}</td>
                            @endif
                            <td><a href="http://www.baidu.com/s?wd={{$order->express_name}}+{{$order->express_no}}" target="_blank" class="button">手动查询物流信息</a></td>
                        </tr>
                    </table>
                </div>
                <div class="box">
                    <table>
                        <tr>
                            <th>买家留言：</th>
                        </tr>
                        <tr>
                            <td width="650">
                                <div>{{$order->buyer_message}}</div>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="box">
                    <h5>售后信息</h5>
                    <table>
                        <tr>
                            <td width="250">状态：<span class="status">{{$order->aftersale->status}}</span></td>
                            <td width="250">申请时间：{{$order->aftersale->created_at}}</td>
                        </tr>
                    </table>
                </div>
                <div class="box">
                    <h5>售后原因</h5>
                    <p>{{$order->aftersale->return_content}}</p>
                    <p>
                        @forelse($order->aftersale->images as $image)
                            <span class="img"><a href="{{env('IMAGE_DISPLAY_DOMAIN')}}/{{$image->name}}" class="order-imgbox">
                                    <img src="{{env('IMAGE_DISPLAY_DOMAIN')}}/{{$image->name}}" width="100" height="100">
                                </a></span>
                        @empty
                        @endforelse
                    </p>
                    @if($order->aftersale->status != "待审核")
                        <p class="price">退款金额：<span>{{$order->aftersale->amount}}</span>元</p>
                    @endif
                </div>
                @if($order->aftersale->state == 4)
                <div class="box">
                    <h5>驳回原因</h5>
                    <p>{{$order->aftersale->reject_content}}</p>
                </div>
                @endif
                <div class="footButton">
                    <input type="button" class="back" value="返回" onclick="javascript:history.back(-1);">

                @if($order->state == 2 || $order->state == 5)
                        <input type="button" class="back" value="保存" onclick="saveExpress('/order/aftersale/update/<?php echo $id?>')">
                @endif
                </div>
            </form>
        </div>
    </div>
    </div>
    <script type="text/javascript">
        $(function() {
            $(".order-imgbox").imgbox({
                'speedIn'		: 0,
                'speedOut'		: 0,
                'alignment'		: 'center',
                'overlayShow'	: true,
                'allowMultiple'	: false
            });
        });
        function saveExpress(url){
            var express_name = $("input[name=express_name]").val();
            var express_no = $("input[name=express_no]").val();
            $.ajax({
                url:url,
                data:{
                    express_name:express_name,
                    express_no : express_no
                },
                success:function($data){
                    parent.location.replace(parent.location.href);
                }
            });
        }
    </script>
@stop
