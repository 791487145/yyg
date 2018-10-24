@extends('supplier')
@section('content')
    <div class="rightCon">
        <div class="wrap">
            <h2><span>订单详情</span></h2>
            <form class="form orderDetail" method="post" action="/order/show" id="form">
                <div class="box">
                    <h5>订单信息</h5>
                    <table>
                        <tr>
                            <input type="hidden" name ="order_no" id="order_no" value="{{$order->order_no}}">
                            <td width="400">订单编号：{{$order->order_no}}</td>
                            <td width="400">订单金额：{{sprintf("%.2f", $order->amount_goods + $order->amount_express)}}</td>
                        </tr>
                        <tr>
                            <td>付款方式：@if($order->pay_type == 1) 支付宝支付 @elseif($order->pay_type == 2) 微信支付 @endif</td>
                            <td>付款时间：{{$order->log}}</td>
                        </tr>
                        <tr>
                            <td>付款金额：<span style="color: red;">￥{{$order->amount_real}}</span></td>
                            <td>配送方式：{{$order->express_type}}</td>
                        </tr>
                        <tr>
                            <td><td>运费：{{$order->amount_express}}</td></td>
                        </tr>
                    </table>

                </div>
                <div class="box">
                    <h5>商品信息</h5>
                    @forelse($order->goods as $goods)
                        <table style="padding: 20px;">
                            <tr>
                                <td rowspan="2" width="100"><img src="{{env('IMAGE_DISPLAY_DOMAIN')}}/{{$goods->data->img}}" width="100"></td>
                                <td width="400"><p class="limitText">商品名称：{{$goods->data->title}}</p></td>
                                <td width="400">零售价：{{$goods->spec->price}}</td>
                            </tr>

                            <tr>
                                <td>规格：{{$goods->spec->name}}</td>
                                <td>数量：{{$goods->num}}</td>
                            </tr>
                            <!--赠品信息-->
                            @forelse($goods->data->gift as $gift)
                            <tr>
                                <td colspan="3">[赠品]:<a href="{{url('goods',$gift->goods_id)}}"  style="color: #8a8989;">{{$gift->goods_title}}</a></td>
                            </tr>
                                @empty
                            @endforelse
                        </table>
                    @empty
                    @endforelse
                </div>


                <div class="box">
                    <h5>收货人信息</h5>
                    <p>
                        <span>{{$order->receiver_name}}</span>
                        <span>{{$order->receiver_mobile}}</span>
                        <span>{{$order->receiver_info->province}}{{$order->receiver_info->city}}{{$order->receiver_info->district}}{{$order->receiver_info->address}}</span>
                    </p>
                </div>
                <div class="box">
                    <table>
                    <tbody id="add_express" style="vertical-align: inherit;">
                        <tr>
                            <th width="100">物流公司：</th>
                            <td width="280">{{$order->express_name}}</td>
                            
                        </tr>
                        <tr height="50">
                            <th width="100">物流单号：</th>
                            <td width="280"><input type="text" name="express_no" class="express_no" value="{{$order->express_no}}"></td>
                            <td><a href="http://www.baidu.com/s?wd={{$order->express_name}}+{{$order->express_no}}" target="_blank" class="button">手动查询物流信息</a></td>
                            @if($order->express_type == '快递' && ($order->state == 1 || $order->state == 2))
                                <td width="200"><a style="margin-left: 50px" href="javascript:;" target="_blank" class="button" onclick="addexpressno()">添加物流单号</a></td>
                            @endif
                        </tr>
                        @if(!empty($expressInfo))
                            @foreach($expressInfo as $info)
                                <tr height="50">
                                    <th width="100">物流单号：</th>
                                    <td width="280"><input type="text" value="{{$info->express_no}}" name="express_no" class="express_no"></td>
                                    <td><a href="http://www.baidu.com/s?wd={{$info->express_name}}+{{$info->express_no}}" target="_blank" class="button">手动查询物流信息</a></td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>    
                    </table>
                </div>
                <div class="box">
                    <table>
                        <tr>
                            <th>买家留言：</th>
                        </tr>
                        <tr>
                            <td width="650">
                                <div>{{$order->buyer_message}}</div>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="footButton">
                    @if($order->state == 2)
                        <input type="submit" id="serve" value="保存">
                    @endif
                    <input type="button" class="back" value="返回" onclick="javascript:history.back(-1);">
                </div>
            </form>
        </div>
    </div>
    </div>
        <script type="text/javascript">
            //添加物流单号
            function addexpressno(){
            	layer.open({
            	    title:'添加物流单号',
            	    content:'<textarea style="width:300px;max-width:300px;height:100px;max-height:100px;"></textarea>',
            	    yes:function(index,layno){
                	    var order_no = $('#order_no').val();
                	    var content  = $('textarea').val();
            	        $.post('/order/addexpress/'+order_no+'/'+content,function(data){
            	            layer.close(index);
            	            if(data.ret == 'fail'){
            	                layer.alert(data.msg);
                	        }
                	        if(data.ret == 'yes'){
            	            	var str = '<tr height="50">'+
                                '<th width="100" >物流单号：</th>'+
                                '<td width="280"><input type="text" value="'+data.content.express_no+'" name="express_no" class="express_no"></td>'+
                                '<td><a href="http://www.baidu.com/s?wd='+data.content.express_name+data.content.express_no+'" target="_blank" class="button">手动查询物流信息</a></td>'+
                                '</tr>';
                    	        $('#add_express').append(str);
                    	    }else{
                    	    	layer.alert(data.msg);
                        	}
                    	})
                	}
                })
            }
        
            $("#form").Validform({
                tiptype:2,
                ajaxPost:true,
                postonce:true,
                callback:function(data){
                    if(data.ret == "yes"){
                        layer.msg('已修改!',{icon:1,time:1000});
                        //$("#express_no").val(data.content);
                        location.href = '/order/all';
                    }
                    if(data.ret == "no"){
                        layer.msg('修改失败!',{icon:1,time:1000});
                    }
                }
            });
        </script>
    @stop
