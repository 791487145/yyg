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

    <div id="tab_demo" >
		<div class="goods-nav">
            @if($action == 1)
                <a class="@if($state == 11) active @endif" href="/withdraw/guide/11/<?php echo $action?>">采购审核</a>
                <a class="@if($state == 15) active @endif" href="/withdraw/guide/15/<?php echo $action?>">财务审核</a>
            @else
                <a class="@if($state == 11) active @endif" href="/withdraw/guide/11/<?php echo $action?>">待审核</a>
            @endif
            <a class="@if($state == 12) active @endif" href="/withdraw/guide/12/<?php echo $action?>">待打款</a>
            <a class="@if($state == 13) active @endif" href="/withdraw/guide/13/<?php echo $action?>">已打款</a>
            <a class="@if($state == 14) active @endif" href="/withdraw/guide/14/<?php echo $action?>">已驳回</a>
       </div>
        {{-- 未审核 --}}
        <div>
            <form method="post" action="/withdraw/guide/<?php echo $state?>/<?php echo $action?>">
            <div class="text-l" style="margin: 20px 5px">
                收款人姓名：<input type="text" name="withdraw_name" value="<?php echo $withdraw_name?>" placeholder=" 收款人姓名" style="width:250px" class="input-text">
                提交时间：
                <input type="text" name="start_time" value="<?php echo $start_time?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',maxDate:''})" id="logmin" class="input-text Wdate" style="width:180px;">
                至
                <input type="text" name="end_time" value="<?php echo $end_time?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',minDate:''})" id="logmax" class="input-text Wdate" style="width:180px;">
                <button  class="btn btn-success" type="submit"><i class="Hui-iconfont"></i> 筛选</button>
                <a href="/fund/export/<?php echo $action?>/<?php echo $state?>/withdraw?withdraw_name=<?php echo $withdraw_name?>&start_time=<?php echo $start_time?>&end_time=<?php echo $end_time?>" name="" id="" class="btn btn-success">导出</a>
            </div>
            </form>
            <table class="table table-border table-bordered table-bg">
                <thead>
                <tr class="text-c">
                    <th>提交时间</th>
                    @if($action == 1)
                        <th>供应商真实名称</th>
                    @elseif($action ==2)
                        <th>导游真实姓名</th>
                    @else
                        <th>旅行社负责人姓名</th>
                    @endif
                    <th>提现金额</th>
                    <th>提现账户信息</th>
                    <th>提现笔数</th>
                    <th>状态</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>

                @foreach($guideBillings as $guideBilling)
                <tr class="text-c">
                    <td>{{$guideBilling->created_at}}</td>
                    <td>{{$guideBilling->real_name}}</td>
                    <td>{{$guideBilling->amount}}</td>
                    <td>{{$guideBilling->withdraw_bank}}
                        @if($guideBilling->withdraw_sub_bank)
                            {{$guideBilling->withdraw_sub_bank}}
                        @endif
                        {{$guideBilling->withdraw_card_number}}
                        {{$guideBilling->withdraw_name}}
                    </td>
                    <td>{{$guideBilling->billingSourceCount}}笔</td>
                    <td>
                        @if($guideBilling->state == 11)提现审核中@endif
                        @if($guideBilling->state == 12)提现待打款@endif
                        @if($guideBilling->state == 13)提现已打款@endif
                        @if($guideBilling->state == 14)提现已驳回@endif
                    </td>

                    @if($action == 1)
                    <td><a href="/withdraw/txaudit/{{$guideBilling->supplier_id}}/<?php echo $action?>/{{$guideBilling->id}}/{{$guideBilling->state}}/{{$guideBilling->amount}}" >查看</a></td>
                    @endif
                    @if($action == 2)
                        <td><a href="/withdraw/txaudit/{{$guideBilling->guide_id}}/<?php echo $action?>/{{$guideBilling->id}}/{{$guideBilling->state}}/{{$guideBilling->amount}}" >查看</a></td>
                    @endif
                    @if($action == 3)
                        <td><a href="/withdraw/txaudit/{{$guideBilling->ta_id}}/<?php echo $action?>/{{$guideBilling->id}}/{{$guideBilling->state}}/{{$guideBilling->amount}}" >查看</a></td>
                    @endif

                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
<?php echo $guideBillings->appends(['withdraw_name'=>$withdraw_name,'start_time'=>$start_time,'end_time'=>$end_time])->render()?>
    </div>




@endsection
@section("javascript")

@endsection