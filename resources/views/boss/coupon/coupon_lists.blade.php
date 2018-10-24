
@extends('layout')
@section("content")
    <div class="page-container ml-20 mr-20">
        <div class="text-c">
        <table class="table table-border table-bordered table-bg">
            <thead>
            <tr>
                <th style="color: red;font-size: 16px" scope="col" colspan="9">
                    优惠券<a  class="btn btn-success f-r" href="javascript:;" onclick="coupon_add('添加优惠券','/coupons/add')">+添加优惠券</a>
                </th>

            </tr>
            <tr class="text-c">
                <th>供应商名称</th>
                <th>指定商品</th>
                <th>优惠券信息</th>

            </tr>
            </thead>
            <tbody>

            @foreach($supplierBase as $supplierCoupons)
                <tr class="text-c">
                    <td>{{$supplierCoupons->name}}</td>
                    <td>
                        @foreach($supplierCoupons->goodsId as $key=>$goodsId)
                            ID.{{$goodsId}}&nbsp;&nbsp;@if(($key+1)%3 ==0)<br/>@endif
                        @endforeach
                        <div><a href="javascript:;" onclick="supplierAddCouponGoods('/coupons/supplier/add/goods/{{$supplierCoupons->id}}')">添加商品</a></div>
                    </td>
                    <td>
                        @foreach($supplierCoupons->couponInfos as $coupon)
                        <div style="border-bottom: 1px solid #e8e8e8;text-align: left;">
                            <p>ID: {{$coupon->id}}</p>
                            <p>{{$coupon->title}}</p>
                            <p>一次性购满{{$coupon->amount_order}}元减{{$coupon->amount_coupon}}元<a href="/coupons/show/{{$coupon->id}}" style="float: right">查看</a></p>
                            <p>
                                {{$coupon->start_time}}至{{$coupon->end_time}}
                               @if(isset($coupon->expired)) <span style="color:red;">（已过期）</span> @endif
                            </p>

                        </div>
                        @endforeach
                    </td>
                </tr>
            @endforeach

            </tbody>
        </table>
    </div>
@endsection
@section('javascript')
    <script type="text/javascript">
        function coupon_add(title,url){
            layer_show(title,url);
        }

        function confirm_delete(title,url){
            layer.confirm(title,function(data){
                $.get(url,function(data){
                    if(data.state == 'yes'){
                        window.location.reload();
                    }
                })
            });
        }

        function supplierAddCouponGoods(url){
            layer_show('供应商添加优惠券商品',url);
        }
    </script>
@endsection