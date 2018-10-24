@extends('layout')
<style>
    .goods-nav{font-size:14px;margin-bottom:20px;}
    .goods-nav a{display: inline-block;margin-right: 20px;padding: 5px; text-decoration-line: none !important;}
    .goods-nav .active{border-bottom: 2px solid #4395ff !important;}
</style>
@section("content")
    <div class="pd-20">
        <div class="goods-nav">
            <a href="/orders/express/type/1"  @if($type == 1)class="active"@endif >自提订单</a>
            <a href="/orders/express/type/kuaidi"  @if($type != 1)class="active"@endif >快递订单</a>
            <div class="search">
                <form>

                    <table class="table table-border table-bordered table-bg">
                        <tr>
                            <td>订单编号:<input type="text" class="input-text ml-10" style="width:57%" value="{{$tmp['order_no'] }}"  name="order_no"></td>
                            <td>商品名称：<input type="text" class="input-text ml-10" style="width:57%" value="{{$tmp['goods_title'] }}"  name="goods_title"></td>
                            <td>下单时间:
                               {{-- <input type="hidden"  value="{{$state}}"  name="state">--}}

                                <input type="text" class="Wdate" style="width:35%" value="{{$tmp['created_at_min'] }}"  name="created_at_min"  id="d412" onfocus="WdatePicker({skin:'whyGreen',dateFmt:'yyyy-MM-dd'})" />到
                                <input type="text" class="Wdate" style="width:35%" value="{{$tmp['created_at_max'] }}"  name="created_at_max"  id="d412" onfocus="WdatePicker({skin:'whyGreen',dateFmt:'yyyy-MM-dd'})" />
                            </td>
                            <td>供应商:
                                <select name="supplier" class="input-text ml-10" style="width:41%">
                                    <option value="">请选择供应商</option>
                                    @foreach($suppliers as $val)
                                        <option value="{{$val->id}}" @if($val->id == $tmp['supplier'])selected="selected"@endif>{{$val->name}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td></td>
                        </tr>

                        <tr >
                            <td>收货人姓名:<input type="text" class="input-text ml-10" style="width:250px" value="{{$tmp['name'] }}"  name="name"></td>
                            <td>支付方式：
                                <select name="pay_type"  class="input-text" style="width:166px" >
                                    <option value="0" @if($tmp['pay_type']==0) selected @endif>全部</option>
                                    <option value="1" @if($tmp['pay_type']==1) selected @endif>ping++支付宝支付</option>
                                    <option value="2" @if($tmp['pay_type']==2) selected @endif>ping++微信支付</option>
                                    <option value="3" @if($tmp['pay_type']==3) selected @endif>微信商户支付</option>
                                </select>
                            </td>
                            <td>收货人手机:<input type="text" class="input-text ml-10" style="width:250px" value="{{$tmp['mobile'] }}"  name="mobile"></td>
                            <td>
                                <input type="submit" class="btn btn-danger" value="筛选">
                               <button type="button" class="btn btn-success" onclick="exal('express')" ><i class="Hui-iconfont">&#xe665;</i>导出</button>
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
                <th width="5%">数量</th>
                <th>运费</th>
                <th>订单金额</th>
                <th width="5%">实付</th>
                <th>状态</th>
                <th>下单时间</th>
                <th>收款账户</th>
                <th>导游/分利</th>
                <th>配送方式</th>
                <th>收货人信息</th>
                <th width="10%" colspan="2">操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($orders as $k => $order)
                <tr class="text-c">
                    <td>
                        <div>
                            <div>订单编号{{$order->order_no}}</div>
                            <div>
                                @foreach($order->tmp as $good)
                                    <div class="oh mb-10 mt-10" style="width: 350px;">
                                        <div class="f-l photos" ><img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$good[2]['bannerFirst']['name']}}"  width="80px" height="80px"></div>
                                        <div class="f-l ml-10" style="width:250px;text-align: left;">
                                            {{$good[0]['goods_title']}}<br/>
                                            【价格】:{{$good[0]['price']}}
                                            【规格】:{{$good[0]['spec_name']}}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </td>
                    <td>
                        @foreach($order->tmp as $good)
                            {{$good[0]['num']}}<br/><br/>
                        @endforeach
                    </td>
                    <td>{{$order->amount_express}}</td>
                    <td>{{$order->amount_goods}}</td>
                    <td>{{$order->amount_real}}</td>
                    <td>{{$order->state}}</td>
                    <td>{{$order->created_at}}</td>
                    <td>
                        @if($order->pay_type ==\App\Models\OrderBase::PAY_TYPE_ALI)
                            ping++支付
                        @endif
                        @if($order->pay_type ==\App\Models\OrderBase::PAY_TYPE_WX)
                            ping++支付
                        @endif
                        @if($order->pay_type ==\App\Models\OrderBase::Pay_TYPE_WX_JS)
                            微信商户支付
                        @endif
                    </td>
                    <td>
                        @if($order->guideName)
                            {{$order->guideName}}
                        @else
                            GID.{{$order->guide_id}}
                        @endif
                        /{{$order->guide_amount}}
                    </td>
                    <td>{{$order->express_type == 0 ? '快递' : '自提'}}</td>
                    <td>
                        {{$order->receiver_info['name']}}</br>
                        {{$order->receiver_info['mobile']}}</br>
                        {{$order->receiver_info['province']}}{{$order->receiver_info['city']}}{{$order->receiver_info['district']}}{{$order->receiver_info['address']}}
                    </td>
                    <td>
                        @if($type != 1)
                            <a style="color: blue" href="javascript:;" onclick="press_sms({{$order->order_no}},'/order/supplier/sms/')">短信催单</a>

                        @endif
                        <a title="查看" href="javascript:;" onclick="a( '订单详情', '/orders/ordersDetail/{{$order->order_no}}')" class="ml-5" style="text-decoration:none">
                            <i class="Hui-iconfont">&#xe709;</i>
                        </a>
                        @if($type != 1)
                            <br/><span class="send_sms_time_{{$order->order_no}}">{{$order->sendSmsRecordTime}}</span>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <span style="float: left;line-height: 70px;margin: 0 20px">共{{$orders->lastPage()}}页,{{$orders->total()}}条数据 ；每页显示{{$orders->perPage()}}条数据</span>
        <?php echo $orders->appends(['order_no'=>$tmp['order_no'],'created_at_min'=>$tmp['created_at_min'],'created_at_max'=>$tmp['created_at_max'],'pay_type'=>$tmp['pay_type'],'name'=>$tmp['name'],'mobile'=>$tmp['mobile'],'supplier'=>$tmp['supplier']])->render(); ?>
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
            url += "?<?php echo $_SERVER['QUERY_STRING'];?>&type={{$type}}";
            location.href = url;
        }

        function press_sms(order_no,url) {
            $.get('/order/supplier/info/' + order_no, function (data) {
                var title = '【易游购】' + data.name + '，您好！您的后台订单（编号' + order_no + ')还未发货，为了保证您的权益，请尽快处理发货！';
                var index = layer.confirm(title, {btn: ['确定发送', '取消']}, function (q) {
                    $.post('/order/supplier/sms/' + title, {mobile: data.mobile}, function (i) {
                        $(".send_sms_time_" + order_no).text(i.created_at);
                        layer.close(index);
                    })
                })
            })

        }
    </script>
@endsection

