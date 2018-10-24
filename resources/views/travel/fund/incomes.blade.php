@extends('travel')
@section('content')
<style>
	.statusTab{padding: 0 20px;margin: 0;height: auto;}
	.statusTab span{padding:5px 0;}
	.statusTab span a{color: #666;}
</style>
    <div class="rightCon">
        <div class="wrap">
            <div class="statusTab">
                <a href="{{url('/fund/incomes/0')}}"><span @if($state == 0) class="active" @endif>待入账余额明细</span></a>
                <a href="{{url('/fund/incomes/1')}}"><span @if($state == 1) class="active" @endif>账户余额明细</span></a>
            </div>
            <div class="tabBox">
                <div class="active">
                    <div class="searchForm">
                        <form>
                            <table>
                                <tr>
                                    <th>交易编号：</th>
                                    <td><input type="text" name="trade_no" value="{{isset($keywords['trade_no'])?$keywords['trade_no']:''}}"></td>
                                    <th>入账时间：</th>
                                    <td class="inputGroup">
                                        <input type="text" id="timeStart" name="start_time" value="{{isset($keywords['start_time'])?$keywords['start_time']:''}}" style="width:25%">
                                        至<input type="text" id="timeEnd" name="end_time" value="{{isset($keywords['end_time'])?$keywords['end_time']:''}}" style="width:25%">
                                    </td>
                                </tr>
                            </table>
                            <div class="buttonGroup">
                                <input type="submit" value="搜索" class="gray">
                                <input type="button" value="导出" class="export">
                            </div>
                        </form>
                    </div>
                    <table class="detailTable">
                        <tr>
                            <th>交易编号</th>
                            <th>商品名称</th>
                            <th style="width: 350px">数量</th>
                            <th style="text-align:left;">返利收入</th>
                            <th>下单时间</th>
                            <th>操作</th>
                        </tr>
                        @forelse($incomes as $income)
                        <tr>
                            <td>{{$income->trade_no}}</td>
                            <td>
                                {{$income->orderGood->goods_title}}
                            </td>
                            <td>{{$income->orderGood->num}}</td>
                            <td style="text-align:left;">
                                @if($state == 0)
                                    {{sprintf("%.2f", $income->amount)}} 
                                   @if(!empty($income->return_amount))&emsp;&emsp;
                                        @if($income->return_amount > 0)
                                            <span style="color:red">退款：- {{sprintf("%.2f",$income->return_amount)}}</span>
                                        @endif
                                   @endif
                                @else
                                    {{sprintf("%.2f", $income->amount-$income->return_amount)}}
                                @endif
                            </td>
                            <td>{{$income->created_at}}</td>
                            <td><a href="{{url('fund/income',$income->order_no)}}">查看</a></td>
                        </tr>
                        @empty
                        @endforelse
                    </table>
                    <div class="footPage">
                        <p>共{{$incomes->lastPage()}}页,{{$incomes->total()}}条数据 ；每页显示{{$incomes->perPage()}}条数据</p>
                        <div class="pageLink">
                            {!! $incomes->appends([
                        'trade_no'=>isset($keywords['trade_no'])?$keywords['trade_no']:'',
                        'start_time'=>isset($keywords['start_time'])?$keywords['start_time']:'',
                        'end_time'=>isset($keywords['end_time'])?$keywords['end_time']:'',
                    ])->render() !!}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    </div>
    <script type="text/javascript" src="{{asset('lib/laydate/laydate.js')}}"></script>
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
        	var length = $('.detailTable td').length;
            if(length > 0){
            	var url = "{{url('fund/export',$state)}}";
                url += "?<?php echo $_SERVER['QUERY_STRING'];?>";
                location.href = url;
            }else{
            	layer.alert("当前没有数据")
            }
        });
        
    </script>
    @stop
