@extends('supplier')
@section('content')
    <div class="rightCon">
        <div class="wrap">
            <h2><span>订单详情</span></h2>
            <form class="form orderDetail">
                <div class="box ">
                    <div class="orderStep">
                        @forelse($logs as $log)
                            @if($log->action == '下单')
                                <dl>
                                    <dt>买家下单</dt>
                                    <dd>{{$log->created_at}}</dd>
                                </dl>
                            @endif
                            @if($log->action == '付款')
                                <dl>
                                    <dt>付款</dt>
                                    <dd>{{$log->created_at}}</dd>
                                </dl>
                            @endif
                            @if($log->action == '发货')
                                <dl>
                                    <dt>商家发货</dt>
                                    <dd>{{$log->created_at}}</dd>
                                </dl>
                            @endif
                            @if($log->action == '结算')
                                <dl>
                                    <dt>结算货款</dt>
                                    <dd>{{$log->created_at}}</dd>
                                </dl>
                            @endif
                            @empty
                        @endforelse
                    </div>
                </div>
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
                        </tr>
                        <tr>
                            <td>付款金额：<span style="color: red;">￥{{$order->amount_real}}</span></td>
                            <td></td>
                        </tr>
                    </table>
                </div>
                <div class="box">
                    <h5>商品信息</h5>
                    @forelse($order->goods as $goods)
                        <table>
                            <tr>
                                <td rowspan="2" width="100"><img src="{{env('IMAGE_DISPLAY_DOMAIN')}}/{{$goods->data->cover}}" width="100"></td>
                                <td width="400"><p class="limitText">商品名称：{{$goods->goods_title}}</p></td>
                                <td width="400">零售价：{{$goods->spec->price}}</td>
                            </tr>

                            <tr>
                                <td>规格：{{$goods->spec_name}}</td>
                                <td>数量：{{$goods->num}}</td>
                            </tr>
                            <!--赠品信息-->
                            @forelse($goods->data->gift as $gift)
                                <tr>
                                    <td colspan="3">[赠品]:<a href="{{url('goods',$gift->id)}}"  style="color: #8a8989;">{{$gift->goods_title}}</a></td>
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
                        <span>{{$order->receiver_info->province}}{{$order->receiver_info->city}}{{$order->receiver_info->address}}</span>
                    </p>
                </div>
                <div class="box">
                    <table>
                        <tr>
                            <th>物流公司：</th>
                            <td width="280">{{$order->express_name}}</td>
                            <th>物流单号：</th>
                            <td width="280">{{$order->express_no}}</td>
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

                <div class="footButton">
                    <input type="button" class="back" value="返回" onclick="javascript:history.back(-1);">
                </div>
            </form>
        </div>
    </div>
    </div>
@stop