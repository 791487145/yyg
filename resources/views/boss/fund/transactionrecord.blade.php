@extends('layout')
<style>
    .text-c a{color: #0000cc;}
    .goods-nav .active{
        border-bottom: 2px solid #4395ff !important;
    }
    .goods-nav{font-size:14px;margin-bottom:20px;}
    .goods-nav a{display: inline-block;margin-right: 20px;padding: 5px; text-decoration-line: none !important;}
</style>
@section("content")
    <div class="goods-nav">
        <a href="/fund/reportfund/4" @if($action == \App\Http\Controllers\Admin\FundController::platformAction)class="active" @endif>平台交易记录</a>
        <a href="/fund/reportfund/1" @if($action == \App\Http\Controllers\Admin\FundController::supplierAction)class="active" @endif>供应商交易记录</a>
        <a href="/fund/reportfund/3" @if($action == \App\Http\Controllers\Admin\FundController::taAction)class="active" @endif>旅行社交易记录</a>
        <a href="/fund/reportfund/2" @if($action == \App\Http\Controllers\Admin\FundController::guideAction)class="active" @endif>导游交易记录</a>
    </div>
    <form>
    <div class="text-l" style="margin: 20px 5px">
        单号：<input type="text" name="order_no" value="<?php echo $order_no?>" style="width:230px" class="input-text">
        交易编号：<input type="text" name="trade_no" value="<?php echo $trade_no?>"  style="width:230px" class="input-text">

        @if($action == \App\Http\Controllers\Admin\FundController::taAction)
            旅行社：
            <select value="" name="ta_id" class="input-text" style="width:230px">
                <option value="">请选择</option>
                @foreach($travels as $travel)
                    <option value="{{$travel->id}}" @if($ta_id == $travel->id)selected="selected"@endif>@if($travel->ta_name){{$travel->ta_name}}@else{{$travel->mobile}}@endif</option>
                @endforeach
            </select>
        @endif

        @if($action == \App\Http\Controllers\Admin\FundController::supplierAction)
            供应商：
            <select value="" name="supplier_id" class="input-text" style="width:230px">
                <option value="">请选择</option>
                @foreach($suppliers as $supplier)
                    <option value="{{$supplier->id}}" @if($supplier_id == $supplier->id)selected="selected"@endif>@if($supplier->name){{$supplier->name}}@else{{$supplier->mobile}}@endif</option>
                @endforeach
            </select>
        @endif

        @if($action == \App\Http\Controllers\Admin\FundController::guideAction)
            导游姓名：
            <input type="text" name="guide_name" value="<?php echo $guide_name?>"  style="width:230px" class="input-text">
        @endif

        状态：
        <select value="" name="contentType" class="input-text" style="width:230px">
            <option value="value">请选择</option>
            <option value="0" @if($contentType === "0")selected="selected"@endif>待入账</option>
            <option value="1" @if($contentType == 1)selected="selected"@endif>已入账</option>
            <option value="10" @if($contentType == 10)selected="selected"@endif>已入账未提现</option>
            <option value="11" @if($contentType == 11)selected="selected"@endif>已提现</option>
        </select>

        收款账户：
            <select name="pay_type"  class="input-text" style="width:166px" >
                <option value="0" @if($pay_type==0) selected @endif>全部</option>
                <option value="1" @if($pay_type==1) selected @endif>ping++支付宝支付</option>
                <option value="2" @if($pay_type==2) selected @endif>ping++微信支付</option>
                <option value="3" @if($pay_type==3) selected @endif>微信商户支付</option>
            </select>


        收款时间：
        <input type="text" name="start_time" value="<?php echo $start_time?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',maxDate:''})" id="logmin" class="input-text Wdate" style="width:180px;">
        至
        <input type="text" name="end_time" value="<?php echo $end_time?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',minDate:''})" id="logmax" class="input-text Wdate" style="width:180px;">
        <button name="" id="" class="btn btn-success" type="submit"><i class="Hui-iconfont"></i> 搜索</button>
        <a href="/fund/record/export/<?php echo $action?>?order_no=<?php echo $order_no?>&trade_no=<?php echo $trade_no?>&pay_type=<?php echo $pay_type?>&contentType=<?php echo $contentType?>&ta_id=<?php echo $ta_id?>&supplier_id=<?php echo $supplier_id?>&guide_name=<?php echo $guide_name?>&start_time=<?php echo $start_time?>&end_time=<?php echo $end_time?>&page=<?php echo $currentPage?>" name="" id="" class="btn btn-success">导出</a>
    </div>
    </form>

<table class="table table-border table-bordered table-bg">
    <thead>
    <tr class="text-c">
        <th>单号</th>
        <th>交易编号</th>
        <th>类型</th>
        <th>收款方式</th>
        <th>金额</th>
        <th>状态</th>
        <th>收款时间</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>

    @foreach($tables as $table)
    <tr class="text-c">
        <td>{{$table->order_no}}</td>
        <td>{{$table->trade_no}}</td>
        <td>{{$table->content == '售货进账' ? '供应价' : $table->content}}</td>
        <td>
            @if($table->pay_type ==\App\Models\OrderBase::PAY_TYPE_ALI)
               ping++支付宝支付
            @endif
            @if($table->pay_type ==\App\Models\OrderBase::PAY_TYPE_WX)
               ping++微信支付
            @endif
            @if($table->pay_type ==\App\Models\OrderBase::Pay_TYPE_WX_JS) 
                                             微信商户支付  
            @endif
        </td>
        <td>{{$table->amount - $table->return_amount}}</td>
        <td>{{$table->state}}</td>
        <td>{{$table->created_at}}</td>
        <td>
            @if(!empty($table->order_no))
            <a href="javascript:;" onclick="a( '订单详情', '/orders/ordersDetail/{{$table->order_no}}')">查看</a>
            @endif
        </td>
    </tr>

    @endforeach
    </tbody>
</table>
    <span style="float: left;line-height: 70px;margin: 0 20px">共{{$tables->lastPage()}}页,{{$tables->total()}}条数据 ；每页显示{{$tables->perPage()}}条数据</span>
    <?php echo $tables->appends(['order_no'=>$order_no,'trade_no'=>$trade_no,'pay_type'=>$pay_type,'contentType'=>$contentType,'ta_id'=>$ta_id,'supplier_id'=>$supplier_id,'guide_name'=>$guide_name,'start_time'=>$start_time,'end_time'=>$end_time])->render(); ?>
@endsection
@section('javascript')
    <script>
        function a(title,url){
        layer_show(title,url)
        }

        function orderExport(url){
            $.ajax({
                type:'post',
                url:url,
                success:function(data){

                }
            })
        }

        /*function postData(url){
            var order_no = $("input[name = order_no]").val();
            var trade_no = $("input[name = trade_no]").val();
            var contentType = $("select[name = contentType]").val();
            var start_time = $("input[name = start_time]").val();
            var end_time = $("input[name = end_time]").val();
            $.ajax({
                type:'get',
                url:url,
                data:{
                    order_no:order_no,
                    trade_no:trade_no,
                    content:contentType,
                    start_time:start_time,
                    end_time:end_time
                },
                dataType:'json',
                success:function(data){

                }
            })
        }*/
    </script>
@endsection