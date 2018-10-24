@extends('supplier')
@section('content')
    <div class="rightCon">
        <div class="wrap">
            <h2><span>订单详情</span></h2>
            <form class="form orderDetail" method="post" action="{{url('order/delivery',$order->id)}}" id="order-delivery">
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
                                {{$order->express_type}}
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
                                    <td>[赠品]:<a href="{{url('goods',$gift->goods_id)}}"  style="color: #8a8989;">{{$gift->goods_title}}</a></td>
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
                @if($order->express_type != "自提")
                <div class="box">
                    <table>
                        <tr>
                            <th width="100">物流公司：</th>
                            <td width="280" >
                                <select name="express_name">
                                    @forelse($order->express as $express)
                                    <option>{{$express->name}}</option>
                                        @empty
                                    @endforelse
                                </select>
                            </td>
                            <th>物流单号：</th>
                            <td width="280"><input type="text" datatype="n" name="express_no"></td>
                        </tr>
                    </table>
                </div>
                @endif
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

                <div class="footButton">
                    <input type="button" class="back" value="返回" onclick="javascript:history.back(-1);">
                    <input type="submit" value="发货" class="button">
                </div>
            </form>
        </div>
    </div>
    </div>
    <script>
        $("#order-delivery").Validform({
            tiptype:2,
            ajaxPost:true,
            postonce:true,
            callback:function(data){
                if(data.ret == 'yes') {
                    layer.alert(data.msg,{icon:1,time:1000});
                    location.href = '{{url('order/deliverys')}}';
                    //layer_close();
                } else {
                    //layer.alert(data.msg, {icon:2,time:5000});
                    alert(data.msg)
                }
            }
        });
    </script>
@stop
