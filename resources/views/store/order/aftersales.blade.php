@extends('supplier')
@section('content')
    <style>
        .rightCon .goodsTable dl{overflow: hidden !important;line-height: 54px;}
    </style>
    <div class="rightCon">
        <div class="wrap">
            <div class="statusTab">
                <span class="@if($state == 0) active @endif"><a href="{{url('/order/aftersales/0')}}">待处理</a></span>
                <span class="@if($state == 1) active @endif"><a href="{{url('/order/aftersales/1')}}" >待退款</a></span>
                <span class="@if($state == 3) active @endif"><a href="{{url('/order/aftersales/3')}}" >已退款</a></span>
                <span class="@if($state == 4) active @endif"><a href="{{url('/order/aftersales/4')}}" >已驳回</a></span>
            </div>
            <div class="searchForm">
                <form>
                    <table>
                        <tr>
                            <th>商品名称：</th>
                            <td><input type="text" name="goods_name" value="{{isset($option['goods_name'])?$option['goods_name']:''}}"></td>
                            <th>订单编号：</th>
                            <td><input type="text" name="order_no" value="{{isset($option['order_no'])?$option['order_no']:''}}"></td>
                            <th>收货人手机：</th>
                            <td><input type="text" name="receiver_mobile" value="{{isset($option['receiver_mobile'])?$option['receiver_mobile']:''}}"></td>
                            <td class="buttonGroup"><input type="submit" value="筛选"></td>
                        </tr>
                    </table>
                </form>
            </div>
            <div class="tabBox">
                <div class="active">
                    <table class="goodsTable orderTable">
                        <tr>
                            <th></th>
                            <th>商品信息</th>
                            <th>数量</th>
                            <th>运费</th>
                            <th>订单金额</th>
                            <th>付款金额</th>
                            <th>状态</th>
                            <th>配送方式</th>
                            <th>提交售后时间</th>
                            <th>收货人信息</th>
                            <th>操作</th>
                        </tr>
                        @forelse($orders as $order)
                            <tr>
                                <td></td>
                                <td>
                                    <p class="orderNum"><a href="{{url('order/aftersale',$order->id)}}">订单编号{{$order->order_no}}</a></p>
                                    @forelse($order->goods as $goods)

                                        <dl>
                                            <dt><img src="{{env('IMAGE_DISPLAY_DOMAIN')}}/{{$goods->img}}"></dt>
                                            <dd><p class="limitText">{{$goods->goods_title}}</p><p>供货价：￥{{$goods->price}}</p><p>规格：{{$goods->spec_name}}</p></dd>
                                        </dl>
                                    @empty
                                    @endforelse
                                </td>
                                <td>
                                    @forelse($order->goods as $order_goods)
                                        <dl>
                                            {{$order_goods->num}}件
                                        </dl>
                                    @empty
                                    @endforelse
                                </td>
                                <td>￥{{$order->amount_express}}</td>
                                <td>￥{{$order->amount_goods}}</td>
                                <td>￥{{$order->amount_real}}</td>
                                <td>{{$order->status}}</td>
                                <td>{{$order->express_type}}</td>
                                <td>{{$order->created_at}}</td>
                                <td><p>{{$order->receiver_name}}</p><p>{{$order->receiver_mobile}}</p>
                                    <p>{{$order->receiver_info->province}}{{$order->receiver_info->city}}{{$order->receiver_info->district}}{{$order->receiver_info->district}}{{$order->receiver_info->address}}</p></td>
                                <td>
                                    <a href="{{url('order/aftersale',$order->id)}}">查看</a>
                                </td>
                            </tr>
                        @empty
                        @endforelse
                    </table>
                    <div class="footPage">
                        <p>共{{!empty($orders)?$orders->lastPage():0}}页,
                            {{!empty($orders)?$orders->total():0}}条数据 ；
                            每页显示{{!empty($orders)?$orders->perPage():''}}条数据</p>
                        <div class="pageLink">
                            @if(!empty($orders))
                            {!! $orders->appends([
                                'goods_name' => isset($option['goods_name'])?$option['goods_name']:'',
                                'order_no' => isset($option['order_no'])?$option['order_no']:'',
                                'receiver_mobile' => isset($option['receiver_mobile'])?$option['receiver_mobile']:'',
                            ])->render() !!}
                                @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    @stop
