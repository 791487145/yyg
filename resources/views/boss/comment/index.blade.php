@extends('layout')
<style>
    .goods-nav{font-size:14px;margin-bottom:20px;}
    .goods-nav a{display: inline-block;margin-right: 20px;padding: 5px; text-decoration-line: none !important;}
    .goods-nav .active{border-bottom: 2px solid #4395ff !important;}
</style>
@section("content")
    <div class="pd-20">
        <div class="goods-nav">
            <p>订单评价</p>
            <div class="search">
                <form>
                    <table class="table table-border table-bordered table-bg">
                        <tr>
                            <td>订单编号:<input type="text" class="input-text ml-10" style="width:57%" value="@if(!empty($order_no)) {{$order_no}} @endif"  name="order_no"></td>
                            <td>下单时间:
                                <input type="text" class="Wdate" style="width:35%" value="@if(!empty($start_time)) {{$start_time}} @endif" name="start_time"  id="d412" onfocus="WdatePicker({skin:'whyGreen',dateFmt:'yyyy-MM-dd'})" />到
                                <input type="text" class="Wdate" style="width:35%" value="@if(!empty($end_time)) {{$end_time}} @endif" name="end_time"  id="d412" onfocus="WdatePicker({skin:'whyGreen',dateFmt:'yyyy-MM-dd'})" />
                            </td>
                            <td>供应商:
                                <select name="supplier_id" class="input-text ml-10" style="width:auto">
                                    <option value="0">请选择供应商</option>
                                   @foreach($suppliers as $val)
                                    <option value="{{$val->id}}" @if($val->id == $supplier_id) selected @endif>{{$val->name}}</option>
                                       @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="submit" class="btn btn-danger" value="筛选">                              
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
        <table class="table table-border table-bordered table-bg">
            <thead>

            <tr class="text-c">
                <th width="5%">序号</th>
                <th width="25%">商品</th>
                <th width="5%">数量</th>
                <th>运费</th>
                <th>订单金额</th>
                <th>状态</th>
                <th>配送方式</th>
                <th>下单时间</th>
                <th>收货人信息</th>
                <th width="10%" colspan="2">操作</th>
            </tr>
            </thead>
            <tbody>
            @if(!empty($orders))
            @foreach($orders as $k => $order)
                <tr class="text-c">
                    <td>
                        {{$order->id}}
                    </td>
                    <td>
                        <div>
                            <div>订单编号{{$order->order_no}}</div>
                            <div>
                            @if(!empty($order->goodsinfo))
                                @foreach($order->goodsinfo as $good)
                                    <div class="oh mb-10 mt-10" style="width: 350px;">
                                        <div class="f-l photos" ><img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$good->coverimg}}"  width="80px" height="80px"></div>
                                        <div class="f-l ml-10" style="width:250px;text-align: left;">
                                            {{$good->goods_title}}<br/>
                                                                                                                            【价格】:{{$good->price}}
                                                                                                                            【规格】:{{$good->spec_name}}
                                        </div>
                                    </div>   
                                @endforeach 
                            @endif   
                            </div>
                        </div>
                    </td>
                    <td>
                    @if(!empty($order->goodsinfo))
                        @foreach($order->goodsinfo as $good)
                        {{$good->num}}<br><br>
                        @endforeach
                    @endif
                    </td>
                    <td>{{$order->amount_express}}</td>
                    <td>{{$order->amount_goods}}</td>
                    <td>
                        @if($order->state == 5)
                                                                            已完成
                        @else
                                                                            其他
                        @endif
                    </td>
                    <td>@if($order->express_type == 0)快递@else自提@endif</td>
                    <td>{{$order->express_time}}</td>
                    <td>
                        {{$order->receiver_info['name']}}</br>
                        {{$order->receiver_info['mobile']}}</br>
                        {{$order->receiver_info['province']}}{{$order->receiver_info['city']}}{{$order->receiver_info['district']}}{{$order->receiver_info['address']}}
                    </td>
                    <td>
                        <a href="javascript:;" onclick="a( '订单详情', '/comment/detail/{{$order->order_no}}')" style="text-decoration:none;color:blue;">查看</a>
                    </td>
                </tr>
            @endforeach
            @endif
            </tbody>
        </table>
        <span style="float: left;line-height: 70px;margin: 0 20px">共{{$orders->lastPage()}}页,{{$orders->total()}}条数据 ；每页显示{{$orders->perPage()}}条数据</span>
        <?php echo $orders->appends([
            'order_no' => $order_no,
            'start_time' => $start_time,
            'end_time'   => $end_time,
            'supplier_id'=> $supplier_id
        ])
        ->render(); ?>
    </div>
@endsection

@section("javascript")
    <script src="{{asset('lib/layer/3.0.3/layer.js')}}"></script>
    <script>
        function a(title,url){
            layer_show(title,url)
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