@extends('supplier')
@section('content')
    <style>
        .rightCon .goodsTable dl{overflow: hidden !important;line-height: 54px;}
        .import{width: 140px !important;}
        .searchForm .inputGroup input{width:90px}
         .setDiv{position: fixed;background: rgba(0,0,0,0.5);width: 100%;height: 100%;z-index: 999;
             left:0;right: 0;top: 0;display: none;}
        .form{position: absolute;left: 50%;top: 50%;transform: translate(-50%, -50%);background: #fff;}
        .form h4{font-size:18px;background: #E3E3E3;padding: 10px 20px;margin-top: 0px;}
        .form .box{padding: 20px;width:320px;}
        .form select{width:100px;padding: 4px;}
        .Preservation{padding: 8px 20px;background:#e7641c;color: #FFF;display:block;margin: 20px auto;border: 0;cursor: pointer;}
        .inputText{width: 184px; height: 30px;border: 1px solid #6e6e6e;padding: 0 8px;margin-right: 14px;}
        .form .fileUpload {position: relative;display: inline-block;width: 100px;height: 100px;background: url(../images/file_icon_03.png);float: left;background-size: 100px;}
        .deletePopup{float: right;font-size: 16px;cursor: pointer;}
        .inputText{border: 1px solid #e0e0e0;padding:0px 6px;width: 50px;margin: 0 4px;}
        .topNav{padding: 10px 20px;}
        .topNav span{line-height: 34px;padding:0 0px 10px 0;margin-right: 10px;}
       .borderbottom{border-bottom: 3px solid #e7641c;}
    </style>
    <div class="rightCon">
        <div class="wrap">
            <div class="topNav">
                <a href="{{url('order/deliverys/0')}}"><span class="@if($express_type == 0)borderbottom @endif">快递订单</span></a>
                <a href="{{url('order/deliverys/1')}}"><span class="@if($express_type == 1)borderbottom @endif">自提订单</span></a>
            </div>
            <input type="hidden" value="{{$express_type}}" id="express_type">
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
                        </tr>
                    </table>
                    <div class="buttonGroup">
                        <input type="submit" value="筛选" class="gray">
                        @if($express_type == 1)
                            <a  class="black"> <input type="button" value="批量发货" class="sendMany"></a>
                        @else
                            <a href="{{url('order/import')}}" class="black"> <input type="button" value="批量导入发货订单" class="import"></a>
                        @endif
                        <input type="button" value="批量导出" class="export">

                    </div>
                </form>
            </div>
            <table class="goodsTable orderTable">
                <tr>
                	<th>
	                    @if($express_type == 1)
	                        <input type="checkbox" name="checkAll" id="checkAll"> 全选
	                    @endif
                    </th>
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
                <tr class="tr_{{$order->id}}">
                	<td>
	                    @if($express_type == 1)
	                        <input type="checkbox" name="order_id" value="{{$order->id}}">
	                    @endif
                    </td>
                    <td>
                        <p class="orderNum"><a href="{{url('order/show',$order->id)}}">订单编号{{$order->order_no}}</a></p>
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
                        <p>{{$order->receiver_info->province}}{{$order->receiver_info->city}}{{$order->receiver_info->district}}{{$order->receiver_info->address}}</p></td>
                    <td><a href="{{url('order/delivery',$order->id)}}">发货</a></td>
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
                                'has_gift'=>isset($option['has_gift']) ? $option['has_gift'] : 0,
                                ])->render() !!}
                </div>
            </div>
        </div>
    </div>
    <div class="setDiv nothing">
        <form class="form">
            <h4> &nbsp;<span class="deletePopup">x</span></h4>
            <div class="box" style="width: 300px;">
                <div>
                    自提订单无需物流公司和物流单号，直接点击发货即可完成
                    确定需要批量发货吗？
                </div>
                <div style="margin: 20px auto;width:235px">
                    <input type="button" class="but-yes add"value="确定"/>&nbsp;&nbsp;
                    <input type="button" class="but-no deletePopups"value="取消" />
                </div>
            </div>
        </form>
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
            var url = "{{url('order/export',$express_type.'abc1')}}";
            url += "?<?php echo $_SERVER['QUERY_STRING'];?>";
            location.href = url;
        });

        $(".sendMany").click(function(){
            $(".nothing").show();
        })
        $(".deletePopups").click(function(){
            $('input[name="order_id"]:checked').attr("checked",false);
            $(".nothing").hide();
        })

        $(".add").click(function(){
            orderID = [];
            $('input[name="order_id"]:checked').each(function(e){
                orderID.push($(this).val());
            });
            if(orderID){
                $('input[name="checkAll"]:checked').prop("checked",false);
                $.post("/order/delivery",{id:orderID},function(data){
                    $(".deletePopups").click();
                    $.each(data.ret,function(i,v){
                        $(".tr_"+v).remove();
                    })
                })
            }else{
                layer.msg("请选择商品",{icon:1,time:1000});
            }
        })

        $(".deletePopup").click(function(){
            $(".nothing").hide();
        });

        $("#checkAll").click(function() {
            if(this.checked){
                $('input[name="order_id"]').each(function() {
                    $(this).prop("checked", true);
                });
            }else{
                $('input[name="order_id"]').each(function() {
                    $(this).prop("checked", false);
                });
            }
        });
    </script>
@stop
