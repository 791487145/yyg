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
        <form id="form" action="/order/change/address/{{$orderNo}}" method="post">
            <table class="table table-border table-bordered table-hover">
                <tr>
                    <td ><span class="input-padding">收货人名称：*</span><input type="text" value="{{$receiver_info['name']}}"  datatype="*" class="input-css" name="name"></td>
                </tr>
                <tr>
                    <td ><span class="input-padding">收货人手机号码：*</span><input type="text" value="{{$receiver_info['mobile']}}"  datatype="mobile" class="input-css" name="mobile"></td>
                </tr>
                <tr>
                    <td ><span class="input-padding">添加收货地址：*</span>
                        <select name="province_id" id="province-id" style="height: 30px;">
                            <option value="{{$receiver_info['province_id']}}-{{$receiver_info['province']}}">{{$receiver_info['province']}}</option>
                            @foreach($provinces as $province)
                                <option value="{{$province->id}}-{{$province->name}}">{{$province->name}}</option>
                            @endforeach
                        </select>
                        <select name="city_id" id="city-id"  style="height: 30px;">
                            <option value="{{$receiver_info['city_id']}}-{{$receiver_info['city']}}">{{$receiver_info['city']}}</option>

                        </select>
                        <select name="discount_id" id="discount-id" style="height: 30px;">
                            <option value="{{$receiver_info['district_id']}}-{{$receiver_info['district']}}">{{$receiver_info['district']}}</option>

                        </select>

                    </td>
                </tr>
                <tr>
                    <td ><span class="input-padding">街道信息：*</span><input type="text"  datatype="*" class="input-css" name="address" value="{{$receiver_info['address']}}"></td>
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
                tiptype:function(data){
                    layer.msg(data);
                },
                ajaxPost:true,
                postonce:true,
                datatype:{
                    "mobile":/^1[3|4|5|7|8][0-9]\d{8}$/
                },
                callback:function(data){
                        layer.msg(data.msg,function(){
                            parent.location.replace(parent.location.href);
                        });
                    }
            });
        })
        function b(){
            layer_close();
        }
        $(".inputSelect").click(function(){
            $(".inputOption").show();
        });

        $("#province-id").change(function(){
            var province_id = $("#province-id").val();
            $("#city-id").html('<option value="">请选择市区</option>');
            $.post('/province/citys/'+province_id,function(data){
                var item = '';
                for(var i = 0;i<data.length;i++){
                    item += '<option value="'+data[i].id+'-'+data[i].name+'">'+data[i].name+'</option>';
                }
                $("#city-id").append(item);
            })
        })

        $("#city-id").change(function(){
            var city_id = $("#city-id").val();
            $("#discount-id").html('<option value="">请选择县区</option>');
            $.post('/province/citys/'+city_id,function(data){
                var item = '';
                for(var i = 0;i<data.length;i++){
                    item += '<option value="'+data[i].id+'-'+data[i].name+'">'+data[i].name+'</option>';
                }
                $("#discount-id").append(item);
            })

        })


        /*$("body").bind("click",function(evt){
            if($(evt.target).parents(".inputSelect").length==0)
            {
                $('.inputOption').hide();
            }
        });*/
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