@extends('layout')

@section('nav')
    <style>
        td{
            width:15%;
        }
    </style>
@endsection

@section("content")
    <div class="panel panel-default">
        <div class="panel-header">订单统计</div>
        <div class="panel-body">
            <table class="table table-border table-hover">
                <tbody>
                <tr>
                    <td>
                        ￥ {{ $order['amount_now']}}</br>
                        今日营业额
                    </td>
                    <td colspan="5">
                        {{$order['order_num']}}</br>
                        今日订单数
                    </td>
                    <td colspan="5">
                        {{$order['amount_yesterday']}}</br>
                        昨日营业额
                    </td>
                    <td colspan="5">
                        {{$order['order_num_yesterday']}}</br>
                        昨日订单数
                    </td>

                    <td colspan="5">
                        {{$order['amount_totle']}}</br>
                        总营业额
                    </td>
                    <td colspan="5">
                        {{$order['order_num_totle']}}</br>
                        总订单数
                    </td>
                    <td ></td>
                    <td ></td>
                    <td ></td>
                    <td></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="panel panel-default mt-20">
        <div class="panel-header">会员统计</div>
        <div class="panel-body">
            <table class="table table-border table-hover">
                <tbody>
                <tr>
                    <td>
                        {{ $order['taBase_now']}}</br>
                        今日新增旅行社
                    </td>
                    <td>
                        {{$order['taBase_num']}}</br>
                        旅行社总数
                    </td>
                    <td>
                        {{$order['guide_now']}}</br>
                        今日新增导游数
                    </td>
                    <td>
                        {{$order['guide_num']}}</br>
                        导游总数
                    </td>
                    <td>
                        {{$order['user_now']}}</br>
                        今日新增游客数
                    </td>
                    <td>
                        {{$order['user_num']}}</br>
                        游客总数
                    </td>
                    <td>
                        {{$order['ref']}}</br>
                        关注公众号游客数
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="panel panel-default mt-20">
        <div class="panel-header">商家统计</div>
        <div class="panel-body">
            <table class="table table-border table-hover">
                <tbody>
                <tr>
                    <td>
                        {{$order['supplier_now']}}</br>
                        今日新增供应商
                    </td>
                    <td>
                        {{$order['supplier_num']}}</br>
                        供应商总数
                    </td>
                    <td>
                        {{$order['goodBase_now']}}</br>
                        今日新增商品数
                    </td>
                    <td>
                        {{$order['goodBase_num']}}</br>
                        商品总数
                    </td>
                    <td></td>
                    <td></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

@endsection

@section("javascript")
@endsection