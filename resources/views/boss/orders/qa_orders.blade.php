@extends('layout')
<style>
    .goods-nav{font-size:14px;margin-bottom:20px;}
    .goods-nav a{display: inline-block;margin-right: 20px;padding: 5px; text-decoration-line: none !important;}
    .goods-nav .active{border-bottom: 2px solid #4395ff !important;}
</style>
@section("content")
    <div class="pd-20">
        <div class="goods-nav">
            <div class="search">
                <form>

                    <table class="table table-border table-bordered table-bg">
                        <tr>
                            <td>订单编号:<input type="text" class="input-text ml-10" style="width:57%" value="{{$orderNo}}"  name="order_no"></td>

                            <td>下单时间:
                                <input type="hidden"  value=""  name="state">
                                <input type="text" class="Wdate" style="width:35%" value="{{$created_at_min}}"  name="created_at_min"  id="d412" onfocus="WdatePicker({skin:'whyGreen',dateFmt:'yyyy-MM-dd'})" />到
                                <input type="text" class="Wdate" style="width:35%" value="{{$created_at_max}}"  name="created_at_max"  id="d412" onfocus="WdatePicker({skin:'whyGreen',dateFmt:'yyyy-MM-dd'})" />
                            </td>
                            <td>
                                <input type="submit" class="btn btn-success" value="查询" />
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>


        <table class="table table-border table-bordered table-bg">
            <thead>

            <tr class="text-c">
                <th width="25%">商品</th>
                <th>运费</th>
                <th>商品金额</th>
                <th>订单状态</th>
                <th width="5%">实付</th>
                <th>下单时间</th>
                <th>收货人信息</th>
                <th width="10%" colspan="2">操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($orders as $order)
                <tr class="text-c">
                    <td>
                        <div>
                            <div>订单编号{{$order->order_no}}</div>
                            <div>
                                @foreach($order->goods as $goods)
                                    <div class="oh mb-10 mt-10" style="width: 350px;">
                                        <div class="f-l photos" ><img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$goods->first_img}}"  width="80px" height="80px"></div>
                                        <div class="f-l ml-10" style="width:250px;text-align: left;">
                                            {{$goods->goods_title}}<br/>
                                            【价格】:{{$goods->price}}
                                            【规格】:{{$goods->spec_name}}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </td>
                    <td>{{$order->amount_express}}</td>
                    <td>{{$order->amount_goods}}</td>
                    <td>
                        @if($order->state ==0)待付款
                            @elseif($order->state ==1)待发货
                            @elseif($order->state ==2)待收货
                            @elseif($order->state ==5)已完成
                            @elseif($order->state ==11)系统超时取消
                            @elseif($order->state ==12)用户主动取消
                            @elseif($order->state ==13)客服关闭订单
                        @endif
                    </td>
                    <td>{{$order->amount_real}}</td>
                    <td>{{$order->created_at}}</td>
                    <td>
                        {{$order->receiver_name}}
                        {{$order->receiver_mobile}}
                       {{$order->receiver_info['province']}}-{{$order->receiver_info['city']}}-{{$order->receiver_info['district']}}-{{$order->receiver_info['address']}}
                    </td>
                    <td>
                        <a title="删除" href="javascript:;" onclick="deleteOrder('确定删除吗？','/orders/delete/',{{$order->order_no}})" class="ml-5" style="text-decoration:none">删除
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <span style="float: left;line-height: 70px;margin: 0 20px">共{{$orders->lastPage()}}页,{{$orders->total()}}条数据 ；每页显示{{$orders->perPage()}}条数据</span>
        <?php echo $orders->appends(['order_no'=>$orderNo,'created_at_min'=>$created_at_min,'created_at_max'=>$created_at_max])->render(); ?>
    </div>
@endsection

@section("javascript")
    <script src="{{asset('lib/layer/3.0.3/layer.js')}}"></script>
    <script>

        function deleteOrder(title,url,orderNo){
            layer.confirm(title,function(){
                $.post(url,{order_no:orderNo},function(data){
                    if(data == 1){
                        layer.alert('删除成功',function(){
                            parent.location.replace(parent.location.href);
                        })
                    }
                });
            });
        }

        layer.photos({
            photos: '.photos'
            ,anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
        });

        function exal(state){
            /* var url = "{url('orders/export',99)}}";*/
            var url = "/orders/export/"+state;
            url += "?<?php echo $_SERVER['QUERY_STRING'];?>";
            location.href = url;
        }
    </script>
@endsection