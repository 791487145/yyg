@extends('supplier')
@section('content')
    <style>
        .rightCon .goodsTable dl{overflow: hidden !important;line-height: 54px;}
    </style>
    <div class="rightCon">
        <div class="wrap">
            <div class="statusTab">
                <span class="@if($state == 99) active @endif"><a href="{{url('/order/all/99')}}">全部</a></span>
                <span class="@if($state == 0) active @endif"><a href="{{url('/order/all/0')}}" >待付款</a></span>
                <span class="@if($state == 1) active @endif"><a href="{{url('/order/all/1')}}" >待发货</a></span>
                <span class="@if($state == 2) active @endif"><a href="{{url('/order/all/2')}}" >待收货</a></span>
                <span class="@if($state == 5) active @endif"><a href="{{url('/order/all/5')}}" >已完成</a></span>
                <span class="@if($state == 20) active @endif"><a href="{{url('/order/all/20')}}">已关闭</a></span>
            </div>
            <div class="searchForm">
                <form>
                    <table>
                        <tr>
                            <th>商品名称：</th>
                            <td><input type="text" name="goods_name" value="{{isset($option['goods_name']) ? $option['goods_name'] : ''}}"></td>
                            <th>下单时间：</th>
                            <td class="inputGroup">
                                <input type="text" id="timeStart" name="timeStart" value="{{isset($option['timeStart']) ? $option['timeStart'] : ''}}" style="width:25%">
                                到<input type="text" id="timeEnd"  name="timeEnd" value="{{isset($option['timeEnd']) ? $option['timeEnd'] : ''}}" style="width:25%"></td>
                            <th>订单编号：</th>
                            <td><input type="text" name="order_no" value="{{isset($option['order_no']) ? $option['order_no'] : ''}}"></td>
                        </tr>
                        <tr>
                            <th>收货人姓名：</th>
                            <td><input type="text" name="receiver_name" value="{{isset($option['receiver_name']) ? $option['receiver_name'] : ''}}"></td>
                            <th>支付方式：</th>
                            <td>
                                <select name="pay_type">
                                    <option value="0">全部</option>
                                    <option @if(1 == $pay_type = isset($option['pay_type'])?$option['pay_type']:0) selected @endif value="1">支付宝支付</option>
                                    <option @if(2 == $pay_type = isset($option['pay_type'])?$option['pay_type']:0) selected @endif value="2">微信支付</option>
                                </select>
                            </td>
                            <th>收货人手机：</th>
                            <td><input type="text" name="receiver_mobile" value="{{isset($option['receiver_mobile']) ? $option['receiver_mobile'] : ''}}"></td>
                        </tr>
                        <tr>
                            <th>是否有赠品：</th>
                            <td>
                                <select name="has_gift">
                                    <option @if(0 == $pay_type = isset($option['has_gift'])?$option['has_gift']:-1) selected @endif value="-1">全部</option>
                                    <option @if(1 == $pay_type = isset($option['has_gift'])?$option['has_gift']:-1) selected @endif value="1">有</option>
                                    <option @if(0 == $pay_type = isset($option['has_gift'])?$option['has_gift']:-1) selected @endif value="0">无</option>
                                </select>
                            </td>
                            <th>配送方式：</th>
                            <td>
                                <select name="express_type">
                                    <option @if(-1 == $pay_type = isset($option['express_type'])?$option['express_type']:-1) selected @endif value="-1">全部</option>
                                    <option @if(1 == $pay_type = isset($option['express_type'])?$option['express_type']:-1) selected @endif value="1">自提</option>
                                    <option @if(0 == $pay_type = isset($option['express_type'])?$option['express_type']:-1) selected @endif value="0">快递</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                    <div class="buttonGroup">
                        <input type="submit" value="筛选" class="gray">
                        <input type="button" value="导出" class="export">
                    </div>
                </form>
            </div>
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
                    <th>下单时间</th>
                    <th>收货人信息</th>
                    <th>操作</th>
                </tr>
                @forelse($orders as $order)
                <tr>
                    <td></td>
                    <td>
                        <p class="orderNum">
                                @if($order->after == 1)
                                    <a href="{{url('order/aftersale',$order->id)}}">订单编号{{$order->order_no}}</a>
                                    @else
                                    <a href="{{url('order/show',$order->id)}}">订单编号{{$order->order_no}}</a>
                                @endif
                        </p>
                        @forelse($order->goods as $goods)

                        <dl>
                            <dt><img src="{{env('IMAGE_DISPLAY_DOMAIN')}}/{{$goods->img}}"></dt>
                            <dd><p class="limitText">{{$goods->title}}</p><p>供货价：￥{{$goods->price}}</p><p>规格：{{$goods->spec_name}}</p></dd>
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
                        <p>
                            {{isset($order->receiver_info->province)?$order->receiver_info->province:''}}
                            {{isset($order->receiver_info->city)?$order->receiver_info->city:''}}
                            {{isset($order->receiver_info->district)?$order->receiver_info->district:''}}
                            {{isset($order->receiver_info->address)?$order->receiver_info->address:''}}
                        </p></td>
                    <td>
                        @if($order->state == 1)
                            <a href="{{url('order/delivery',$order->id)}}">发货</a>
                        @else
                            @if($order->after == 1)
                                <a href="{{url('order/aftersale',$order->id)}}">查看</a>
                            @else
                                <a href="{{url('order/show',$order->id)}}">查看</a>
                            @endif

                        @endif

                    </td>
                </tr>
                @empty
                @endforelse

            </table>
            <div class="footPage">
                <p>共{{$orders->lastPage()}}页,{{$orders->total()}}条数据 ；每页显示{{$orders->perPage()}}条数据</p>
                <div class="pageLink">
                    {!! $orders->appends([
                                'goods_name' => isset($option['goods_name']) ? $option['goods_name'] : '',
                                'timeStart'=>isset($option['timeStart']) ? $option['timeStart'] : '',
                                'timeEnd'=>isset($option['timeEnd']) ? $option['timeEnd'] : '',
                                'order_no'=>isset($option['order_no']) ? $option['order_no'] : '',
                                'receiver_name'=>isset($option['receiver_name']) ? $option['receiver_name'] : '',
                                'pay_type'=>isset($option['pay_type']) ? $option['pay_type'] : 0,
                                'receiver_mobile'=>isset($option['receiver_mobile']) ? $option['receiver_mobile'] : '',
                                'has_gift'=>isset($option['has_gift']) ? $option['has_gift'] : -1,
                                'express_type'=>isset($option['express_type']) ? $option['express_type'] : -1,
                                ])->render() !!}
                </div>
            </div>
        </div>
    </div>
    <script src="{{asset('/lib/laydate/laydate.js')}}"></script>
    <script>
        //日期范围限制
        var start = {
            elem: '#timeStart',
            format: 'YYYY-MM-DD hh:ss:mm',
            min: '2000-01-01', //设定最小日期为当前日期
            max: '2099-06-16', //最大日期
            istime: true,
            istoday: false,
            choose: function(datas){
                end.min = datas; //开始日选好后，重置结束日的最小日期
                end.start = datas //将结束日的初始值设定为开始日
            }
        };
        var end = {
            elem: '#timeEnd',
            format: 'YYYY-MM-DD hh:ss:mm',
            min: '2000-01-01',
            max: '2099-06-16',
            istime: true,
            istoday: false,
            choose: function(datas){
                start.max = datas; //结束日选好后，充值开始日的最大日期
            }
        };
        laydate(start);
        laydate(end);

        $('.export').click(function(){
            var url = "{{url('order/export',$state)}}";
            url += "?<?php echo $_SERVER['QUERY_STRING'];?>";
            location.href = url;
        });
    </script>
@stop
