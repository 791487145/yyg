@extends('supplier')
@section('content')
    <style>
        .rightCon .goodsTable dl{overflow: hidden !important;line-height: 54px;}
        .searchForm input[type=text], .searchForm select{width:150px;}
    </style>
    <div class="rightCon">
        <div class="wrap">
        <h2><span>我的评价</span></h2>
            <div class="searchForm">
                <form>
                    <table>
                        <tr>
                            <th>商品名称:</th>
                            <td><input type="text" name="goods_name" value="@if(!empty($goods_name)) {{$goods_name}} @endif"></td>
                            <th>订单编号:</th>
                            <td><input type="text" name="order_no" value="@if(!empty($order_no)) {{$order_no}} @endif"></td>
                            <th>下单时间：</th>
                            <td class="inputGroup">
                                <input type="text" id="timeStart" name="start_time" value="@if(!empty($start_time)) {{$start_time}} @endif"  style="width:132px">
                                                                                                     到
								<input type="text" id="timeEnd"  name="end_time" value="@if(!empty($end_time)) {{$end_time}} @endif" style="width:132px">
							</td>
                            <th>收件人手机:</th>
                            <td><input type="text" name="mobile" value="@if(!empty($mobile)) {{$mobile}} @endif"></td>
                            <td class="buttonGroup"><input type="submit" value="筛选"></td>
                        </tr>
                    </table>
                </form>
            </div>
            <div class="tabBox">
                <div class="active">
                    <table class="goodsTable orderTable">
                        <tr>
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
                        @if(!empty($orders))
                        @foreach($orders as $k => $order)
                        <tr class="text-c">
                            <td>
                                {{$order->id}}
                            </td>
                            <td>
                                <div>
                                    <div class="margin-bottom10">订单编号{{$order->order_no}}</div>
                                    <div>
                                    @if(!empty($order->goodsinfo))
                                        @foreach($order->goodsinfo as $good)
                                            <div class="oh" style="width: 350px;">
                                                <div class="float-left photos" ><img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$good->coverimg}}"  width="80px" height="80px"></div>
                                                <div class="float-left margin-left10" style="width:250px;text-align: left;">
                                                    {{$good->goods_title}}<br/>【价格】:{{$good->price}}【规格】:{{$good->spec_name}}
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
                                <a href="{{url('comment/detail',$order->order_no)}}" style="text-decoration:none;color:blue;">查看</a>
                            </td>
                       </tr>
                       @endforeach
                       @endif
                    </table>
                    <div class="footPage">
                        <p>共{{$orders->lastPage()}}页,{{$orders->total()}}条数据 ；每页显示{{$orders->perPage()}}条数据</p>
                        <div class="pageLink">
                           <?php echo $orders->appends([
                               'goods_name' => $goods_name,
                               'order_no'   => $order_no,
                               'start_time' => $start_time,
                               'end_time'   => $end_time,
                               'mobile'     => $mobile
                           ])->render(); 
                           ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <script src="{{asset('lib/layer/3.0.3/layer.js')}}"></script>
    <script src="{{asset('/lib/laydate/laydate.js')}}"></script>
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
    </script>
   @stop
