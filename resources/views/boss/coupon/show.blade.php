
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
        <a href="/coupons/show/{{$id}}/0" @if($state == 0) class="active" @endif>已领取未使用(<span id="nouseCount"></span>)</a>
        <a href="/coupons/show/{{$id}}/1" @if($state == 1) class="active" @endif>已领取已使用(<span id="usedCount"></span>)</a>
    </div>
    <div class="page-container ml-20 mr-20">
        <div class="text-c">
            <table class="table table-border table-bordered table-bg">
                <thead>
                <tr>
                    <th style="color: red;font-size: 16px" scope="col" colspan="9">
                        优惠券使用详情
                    </th>

                </tr>
                <tr class="text-c">
                    <th>用户ID</th>
                    <th>用户类型</th>
                    <th>用户信息</th>
                    <th>优惠券名称</th>
                    <th>发放条件</th>
                    <th>使用条件</th>
                    <th>状态</th>
                </tr>
                </thead>
                <tbody>

                @foreach($couponUser as $user)
                    <tr class="text-c">
                        <td>{{$user->uid}}</td>
                        <td>{{$user->identity}}</td>
                        <td>{{$user->name}}|{{$user->mobile}}</td>
                        <td>{{$user->title}}</td>
                        <td>购买指定商品发放使用</td>
                        <td>一次性购满{{$user->amount_order}}元减{{$user->amount_coupon}}元</td>
                        <td>{{$user->state == 1 ? '已领取已使用' : '已领取未使用'}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

@endsection
@section('javascript')
    <script>
        couponUseStateCount({{$id}},0);
        couponUseStateCount({{$id}},1);
        function couponUseStateCount(id,state){
            $.get('/coupons/used/count/'+id+'/'+state,function(data){
                if(state == 1){
                    $("#usedCount").html(data);
                }else{
                    $("#nouseCount").html(data);
                }
            })
        }
    </script>
@endsection
