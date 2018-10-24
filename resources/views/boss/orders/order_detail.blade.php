@extends('layout_pop')
<style>
    li{
        float: left;
        list-style: none;
    }
    #order li{
        margin-left: 18%;
    }
    .order li{
        margin-left: 18%;
    }
    .all{
        margin-left: 10px;
        margin-top: 10px;
    }
</style>
@section("content")
    <div class="all">
        <div>订单详情</div>
        <div class="cl">
            @if(!empty($orders->orderLog))
                @foreach($orders->orderLog as $orderLog)
                    <div class="f-l">
                        {{$orderLog->action}}
                        {{$orderLog->created_at}}
                    </div>
                @endforeach
            @endif
        </div>
        <div>
            <div>
            	<div></div>
                <table class="table table-border table-bordered table-hover">
                    <tbody>
                        <tr>
                            <td width="500">订单编号：{{$orders->order_no}}</td>
                            <td>订单金额：{{$orders->amount_goods}}</td>
                            <td>实付金额：{{$orders->amount_real}}</td>
                        </tr>
                    </tbody>
                </table>
                @foreach($orders->tmp as $good)
                    <table class="table table-border table-bordered table-hover" style="margin-top: -1px;">
                        <tbody>
                       
                        <tr>
                            <td>商品名称：{{$good['goods_title']}}</td>
                            <td>规格：{{$good['spec_name']}}</td>
                            <td>数量：{{$good['num']}}</td>
                            <td>零售价：{{$good['price']}}</td>
                            <td>建议零售价:{{$good['price']}}</td>
                        </tr>

                        </tbody>
                        </table>
                        @if(!empty($good->goods_gift))
                        <table class="table table-border table-bordered table-hover">
                            @foreach($good->goods_gift as $goods_gift)
                                <thead>赠品信息</thead>
                            <tbody>
                            <tr class="success">
                                <td>商品名：{{$goods_gift['title']}}</td>
                                <td>规格：{{$goods_gift['packname']}}</td>
                                <td>零售价：{{$goods_gift['price']}}</td>
                            </tr>
                            </tbody>
                            @endforeach
                        @endif

                    </table>
                    </br>
                @endforeach
                <div>
                @if($orders->pay_type ==\App\Models\OrderBase::PAY_TYPE_ALI)
                   ping++支付宝支付
                @endif
                @if($orders->pay_type ==\App\Models\OrderBase::PAY_TYPE_WX)
                   ping++微信支付
                @endif
                @if($orders->pay_type ==\App\Models\OrderBase::Pay_TYPE_WX_JS) 
                                                         微信商户支付  
                @endif
                </div><div>付款时间：{{$orders->created_at}}</div>
            </div>
            <div>
                <div><label>配送方式</label>：{{$orders->express_type == 0 ? '快递' : '自提'}}</div>
                <div><label>运费</label>：{{$orders->amount_express}}</div>
                <div><label>收货人信息 </label>：{{$orders->receiver_info['name']}} {{$orders->receiver_info['mobile']}} {{$orders->addressDetail}}</div>
                <div><label>物流公司</label>：{{$orders->express_name}}</div>
                <div>
                    <label>物流单号</label>：
                    @if($orders->state == 2 || $orders->state == 5)
                        <input type="text" id="orderNumber" value="{{$orders->express_no}}"> <a class="btn btn-success" href="https://www.baidu.com/s?wd={{$orders->express_name}}+{{$orders->express_no}}">手动查询物流信息</a>
                    @endif
                </div>
            </div>

            <div>
                <label>买家留言</label>
                <div>
                    <textarea class="textarea radius" readonly>
                        {{$orders->buyer_message}}
                    </textarea>
                </div>
            </div>
            @if($orders->state == 12)
            <div>
                <label>关闭原因</label>
                <div><textarea class="textarea radius"></textarea></div>
            </div>
            @endif
            <div class="text-c">
            <button type="button" onclick="a()" class="btn btn-success radius mt-20" id="user-save" name="route-save"><i class="icon-ok"></i> 返回</button>
             @if($orders->state == 2 || $orders->state == 5)
                    <button type="button" onclick="save('/orders/detail/<?php echo $order_no?>')" class="btn btn-success radius mt-20"><i class="icon-ok"></i> 保存</button>
             @endif
            </div>
        </div>

    </div>



@section("javascript")
<script>
    function a(){
        layer_close();
    }
$(function(){
	$("#form-perm-user-edit").Validform({
        tiptype:2,
        ajaxPost:true,
        postonce:true,
        callback:function(data){
            if(data.ret == 'yes') {
                layer.alert(data.msg,{icon:1,time:1000});
                parent.location.replace(parent.location.href);
            } else if(data.ret == 'no') {
                layer.alert(data.msg,{icon:2,time:5000});
            } else {
                layer.alert('添加失败', {icon:2,time:5000});
            }
        }
    });
});
  function save(url){
      var express_no = $("#orderNumber").val();
      $.ajax({
          method:'post',
          url:url,
          data:{express_no:express_no},
          success:function(data){
              parent.location.replace(parent.location.href);
          }
      });
  }
</script>
@endsection