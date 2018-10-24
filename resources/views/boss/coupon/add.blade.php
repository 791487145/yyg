@extends('layout_pop')
<style>
    #all{
        margin-left: 5%;
        margin-top: 5%;
    }
    .input-css{
        width: 400px;line-height: 29px;border: solid 1px #ddd;
    }
    .input-padding{
        width: 150px;text-align: right;display: inline-block;margin-right: 10px;
    }
    .red-font{
        color:red;padding-left: 160px;
    }
    .inputSelect{display: inline-block;border: 1px solid #999;padding:0 5px;position: relative;width:400px;line-height: 30px;margin-left: 20px;}
    .inputOption{position: absolute;left:-1px;top:30px;background: #fff;padding-top:20px;display: none;border: 1px solid #999;border-top:0;width: 410px;}
    .inputOption p{ overflow: hidden;padding:0 10px;margin: 0;}
    .inputOption p:hover{ background: #e0e0e0;}
    .inputOption input{height:40px;margin-right: 5px;float: left;}
    .inputOption span{float: left;}
</style>
@section("content")
    <div id="all">
        <form id="form" action="/coupons/add" method="post">
            <table class="table table-border table-bordered table-hover">
                <tr>
                    <td ><span class="input-padding">优惠券名称：</span><input type="text"  datatype="*" class="input-css" name="title"></td>
                </tr>
                <tr>
                    <td ><span class="input-padding">发放条件：</span><input type="checkbox" value="1" style="width: 30px" checked="checked"  datatype="*" class="input-css" name="send_type">购买指定商品后发放使用</td>
                </tr>
                <tr>
                    <td ><span class="input-padding">添加指定商品：</span>
                        <select name="supplier_id" id="supplier-id" value="选择供应商" datatype="*" style="height: 30px;">
                            <option value="">请选择供应商</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{$supplier->id}}">{{$supplier->name}}</option>
                            @endforeach
                        </select>
                       {{-- <div class="inputSelect">
                            <div class="selectText">选择商品</div>
                            <div class="inputOption">

                                <p><input class="selectCheckbox" type="checkbox" value="1" data-title="dfdfd" name="goodsId[]"/><span>dddd</span></p>


                            </div>
                        </div>--}}
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="input-padding">使用条件：</span>一次性购满 &nbsp;&nbsp;<input style="width: 80px;" type="text" class="input-css"  value="" name="amount_order" datatype="*" />&nbsp;&nbsp;元减&nbsp;&nbsp;<input type="text" style="width: 80px;" class="input-css"  value="" onkeyup="if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,'')}else{this.value=this.value.replace(/\D/g,'')}" onafterpaste="if(this.value.length==1){this.value=this.value.replace(/[^1-9]/g,'0')}else{this.value=this.value.replace(/\D/g,'')}" name="amount_coupon" datatype="*" />&nbsp;&nbsp;元
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="inline">有效期
                <input type="text" name="start_time" datatype="*"  value="" id="timeStart" class="input-text Wdate" style="width:180px;">
            -
            <input type="text" name="end_time" datatype="*" value="" id="timeEnd" class="input-text Wdate" style="width:180px;"></span>
                    </td>
                </tr>

            </table>
            <div style="text-align: center;margin:30px;">
                <button class="btn btn-danger" onclick="layer_close()">返回</button>
                <button class="btn btn-success" type="submit">确定</button>
            </div>
        </form>
    </div>

@endsection
@section("javascript")
    <script src="{{asset('/lib/laydate/laydate.js')}}"></script>
    <script>
        $(function(){
            $("#form").Validform({
                tiptype:3,
                ajaxPost:true,
                postonce:true,
                callback:function(data){

                    if(data.ret == 'no'){
                        layer.msg(data.msg);
                    }
                    if(data.ret == 'yes'){
                        layer.msg(data.msg);
                        parent.location.replace(parent.location.href);
                    }
                }
            });
        })
        function b(){
            layer_close();
        }
        $(".inputSelect").click(function(){
            $(".inputOption").show();
        });

        $("body").bind("click",function(evt){
            if($(evt.target).parents(".inputSelect").length==0)
            {
                $('.inputOption').hide();
            }
        });
        /*
        $("#supplier-id").change(function(){
            $(".inputOption").html('');
            $(".selectText").html('选择商品');
            var id = $("#supplier-id").val();
            if(id){
                $.post('/coupon/supplier/goods/'+id,function(data){
                    var item = ''
                    for(var i = 0; i<data.length;i++){
                        item += '<p>' +
                                '<input class="selectCheckbox" type="checkbox" name="goods_id[]" value="'+data[i].id+'" data-title="'+data[i].title+'" name="goodsId[]"/>' +
                                '<span>'+data[i].title+'</span>' +
                                '</p>'
                    }
                    $(".inputOption").append(item);

                    $(".selectCheckbox").click(function(){
                        var val = $(".selectCheckbox:checked").eq(0).attr("data-title");
                        if(val){
                            if(val.length>30){
                                val = val.substring(0,28)+'...';
                            }
                            $(".selectText").text(val);
                        }else{
                            $(".selectText").text("选择商品");
                        }
                    });

                });
            }

        });*/

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
@endsection