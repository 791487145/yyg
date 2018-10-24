@extends('layout_pop')
<style>
    li{
        float: left;
        list-style: none;
    }
    .order li{
        margin-left: 18%;
    }
</style>
@section("content")

    @if($action == 'list')
        @include('boss.orders.a');
    @endif
    @if($action == 'passing')
        <div class="pd-20">
            <h3>请输入退款说明</h3>
            <textarea class="textarea radius" name="return_content" id="return_content"></textarea>
            <h3>订单实际支付金额</h3>
            <div style="position: relative">
                <span style="position: absolute;left: 5px;top: 5px;">￥</span>
                <input type="text" style="text-indent: 20px;" class="input-text radius size-M realAmount" value="{{$amount_real}}" readonly>
            </div>

            <h3>请输入退款金额</h3>
            <input type="number" class="input-text radius size-M refoundAmount" style="width: 700px" id="refund">
            <input type="button" data-url="/orders/check/passOne/{{$order_no}}/{{$amount_real}}" class="btn btn-success radius pass_refund" style="margin:50px 50px 50px 300px;" value="确认">
            <input type="button" onclick="up_level()" class="btn btn-default radius" value="取消">
        </div>
    @endif
    @if($action == "refuse")
        <div  class="pd-20" id="a">
            <div>请输入驳回原因
                    <div><textarea id="reason" class="textarea radius"></textarea></div>
                    <div>
                        <input class="btn btn-success radius refuse_order" data-url="/orders/check/{{$order_no}}/reason" style="margin:50px 50px 50px 300px;" value="确定">
                    	<input class="btn btn-default radius" type="button" onclick="up_level()" value="取消">
                    </div>
            </div>
        </div>
    @endif



@section("javascript")
<script>
    function refuse(title,url,w,h){
        layer_show(title,url,w,h);
    }

    function pass(title,url){
        layer_show(title,url);
    }

    function up_level(){
        layer_close();
    }

    function b(url){
        var realAmount = parseFloat($(".realAmount").val());
        var refoundAmount = parseFloat($(".refoundAmount").val());
        if(refoundAmount > realAmount){
            layer.msg('退款金额不能大于实际金额');
            return false;
        }
        var refund = $("#refund").val();
        var return_content = $("#return_content").val();
        $.get(url,{refund:refund,return_content:return_content},function(msg){

            if(msg.ret == 'no'){
                layer.msg('退款金额不能大于实际付款金额');
            }else{
                parent.location.replace(parent.location.href);
            }

        })
    }

    $(".pass_refund").click(function(){
        var return_content = $("#return_content").val();
        var amount = $(".refoundAmount").val();
        if(return_content == '' || amount == ''){
            layer.msg("退款原因和退款金额必须填写!")
        }else{
            var realAmount = parseFloat($(".realAmount").val());
            var refoundAmount = parseFloat($(".refoundAmount").val());
            if(refoundAmount > realAmount){
                layer.msg('退款金额不能大于实际金额');
                return false;
            }
            var refund = $("#refund").val();
            $(".pass_refund").attr("disabled","disabled");
            var url = $(".pass_refund").attr("data-url");
            $.get(url,{refund:refund,return_content:return_content},function(msg){

                if(msg.ret == 'no'){
                    layer.msg('退款金额不能大于实际付款金额');
                    $(".pass_refund").removeAttr("disabled");
                }else{
                    parent.location.replace(parent.location.href);
                }

            })
        }
    })


    $("#reason").on("change keyup",function(){
        var length1 = 120;
        var reasons = $("#reason").val();
        if(reasons.length > length1){
            var length2 = reasons.substring(0,length1);
            alert('不能超过120个字符');
            $("#reason").val(length2);
        }
    })


    function c(url){
        var reasons = $("#reason").val();
        $.get(url,{reasons:reasons},function(msg){
            parent.location.replace(parent.location.href);
        })
    }

    $(".refuse_order").click(function(){
        var reasons = $("#reason").val();
        if(reasons == ''){
            layer.msg("驳回原因不能为空");
        }else{
            $(".refuse_order").attr("disabled","disabled");
            var url = $(".refuse_order").attr("data-url");
            $.get(url,{reasons:reasons},function(data){
                if(data.ret == 'yes'){
                    parent.location.replace(parent.location.href);
                }else{
                    $(".refuse_order").removeAttr("disabled");
                }
            })
        }
    })

</script>
@endsection