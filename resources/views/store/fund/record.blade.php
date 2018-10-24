@extends('supplier')
@section('content')
    <style>
        .fundTable td{text-align: center !important;}
    </style>
    <div class="rightCon">
        <div class="wrap">
            <div class="statusTab">
                <span @if($inOut == 1) class="active" @endif><a href="{{url('/fund/record',1)}}">收入明细</a></span>
                <span @if($inOut == 2) class="active" @endif><a href="{{url('/fund/record',2)}}">退款明细</a></span>
            </div>
            <div class="tabBox">
                <div class="active">
                    <div class="searchForm">
                        <form>
                        @if($inOut == 1)
                            <!--收入明细-->
                            <table>
                                <tr>
                                    <th>交易编号：</th>
                                    <td><input type="text" name="trade_no" value="{{isset($option['trade_no'])?$option['trade_no']:''}}"></td>
                                    <th>订单编号：</th>
                                    <td><input type="text" name="order_no"  value="{{isset($option['order_no'])?$option['order_no']:''}}"></td>
                                    <th>收款类型：</th>
                                    <td><select><option>订单收入</option></select></td>
                                </tr>
                                <tr>
                                    {{--<th>付款方式：</th>
                                    <td>
                                        <select name="pay_type">
                                            <option value="0">全部</option>
                                            <option value="1" @if(1 == $pay_type = isset($option['pay_type'])?$option['pay_type']:0) selected @endif>支付宝</option>
                                            <option value="2" @if(2 == $pay_type = isset($option['pay_type'])?$option['pay_type']:0) selected @endif>微信</option>
                                        </select>
                                    </td>--}}
                                    <th>收款时间：</th>
                                    <td class="inputGroup">
                                        <input type="text" id="timeStart" name="start_time" style="width:184px;"  value="{{isset($option['start_time'])?$option['start_time']:''}}">
                                        到<input type="text" id="timeEnd" name="end_time" style="width:184px;"  value="{{isset($option['end_time'])?$option['end_time']:''}}">
                                    </td>
                                </tr>
                            </table>
                                <div class="buttonGroup">
                                    <input type="submit" value="搜索" class="gray">
                                    <input type="button" value="导出" class="export">
                                </div>
                            @elseif($inOut == 2)
                            <!--支出明细-->
                                <table>
                                    <tr>
                                        <th>退款编号：</th>
                                        <td><input type="text" name="trade_no" value="{{isset($option['trade_no'])?$option['trade_no']:''}}"></td>
                                        <th>订单编号：</th>
                                        <td><input type="text" name="order_no"  value="{{isset($option['order_no'])?$option['order_no']:''}}"></td>
                                        <th>付款类型：</th>
                                        <td><select><option>退款支出</option></select></td>
                                    </tr>
                                    <tr>
                                        <th>收款时间：</th>
                                        <td class="inputGroup">
                                            <input type="text" id="timeStart" name="start_time" style="width:184px;" value="{{isset($option['start_time'])?$option['start_time']:''}}">
                                            到<input type="text" id="timeEnd" name="end_time" style="width:184px;" value="{{isset($option['end_time'])?$option['end_time']:''}}">
                                        </td>
                                    </tr>
                                </table>
                                <div class="buttonGroup">
                                    <input type="submit" value="搜索" class="gray">
                                    <input type="button" value="导出" class="export">
                                </div>
                            @endif

                        </form>
                    </div>
                    <table class="goodsTable fundTable">
                        @if($inOut == 1)
                        <tr>
                            <th>交易编号</th>
                            <th>收款类型</th>
                            <th>订单编号</th>
                            <th>商品名称</th>
                            <th>收款方式</th>
                            <th>实收金额</th>
                            <th>收款时间</th>
                            <th>操作</th>
                        </tr>
                            @forelse($billings as $billing)
                                <tr>
                                    <td>{{$billing->trade_no}}</td>
                                    <td>@if($inOut == 1)订单收入@endif</td>
                                    <td>{{$billing->order_no}}</td>
                                    <td>
                                    @foreach($billing->goodsInfo as $v)
                                        <span>{{$v}}</span><br/>
                                    @endforeach
                                    </td>
                                    <td>
                                        @if($billing->order->pay_type == 1)支付宝 @elseif($billing->order->pay_type == 2)微信支付 @endif
                                    </td>
                                    <td>{{$billing->amount - $billing->return_amount}}</td>
                                    <td>{{$billing->created_at}}</td>
                                    <td>
                                        @if($billing->in_out == 1)
                                            <a href="{{url('/fund/show',$billing->order->id)}}">查看</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                            @endforelse

                        @elseif($inOut == 2)
                            <tr>
                                <th>退款编号</th>
                                <th>订单编号</th>
                                <th>商品名称</th>
                                <th>退款金额</th>
                                <th>退款时间</th>
                                <th>操作</th>
                            </tr>

                        @forelse($billings as $billing)
                        <tr>
                            <td>{{$billing->return_no}}</td>
                            <td>{{$billing->order_no}}</td>
                            <td>
                                @if(!empty($billing->goodsInfo))
                                    @foreach($billing->goodsInfo as $v)
                                            <span>{{$v}}</span><br/>
                                    @endforeach
                                @endif
                            </td>
                            <td>{{$billing->amount}}</td>
                            <td>{{$billing->created_at}}</td>
                            <td> <a href="{{url('order/aftersale',$billing->order->id)}}">查看</a></td>
                        </tr>
                        @empty
                            @endforelse
                        @endif

                    </table>
                    <div class="footPage">
                        <p>共{{$billings->lastPage()}}页,{{$billings->total()}}条数据 ；每页显示{{$billings->perPage()}}条数据</p>
                        <div class="pageLink">
                            {!! $billings->appends([
                                'trade_no' => isset($trade_no) ? $trade_no : '',
                                'order_no'=>isset($order_no) ? $order_no : '',
                                'pay_type'=>isset($pay_type) ? $pay_type : 0,
                                'start_time'=>isset($start_time) ? $start_time : '',
                                'end_time'=>isset($end_time) ? $end_time : '',
                                ])->render() !!}
                        </div>
                    </div>
                </div>

                </div>
            </div>
        </div>
    </div>
    </div>


    <script src="{{asset('lib/laydate/laydate.js')}}"></script>
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
        ;
        laydate(start);
        laydate(end);
        $('.export').click(function(){
            var url = "{{url('fund/record/export',$inOut)}}";
            url += "?<?php echo $_SERVER['QUERY_STRING'];?>";
            location.href = url;
        });
    </script>
    @stop
