@extends('layout_pop')
@section("content")
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
    <div id="all">
        <form id="form" action='/coupon/supplier/goods/{{$supplier_id}}' method="post">
            <table class="table table-border table-bordered table-hover">
                <tr>
                    <td ><span class="input-padding">添加指定商品：</span>
                         <div class="inputSelect">
                             <div class="selectText">选择商品</div>
                             <div class="inputOption">

                                 @foreach($supplierGoods as $goods)
                                     <p>
                                         <input class="selectCheckbox" type="checkbox" @if(in_array($goods->id,$selectGoods)) checked="checked" @endif value="{{$goods->id}}" data-title="{{$goods->title}}" name="goodsId[]"/>
                                         <span>{{$goods->title}}</span>
                                     </p>
                                 @endforeach

                             </div>
                         </div>
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
    <script>
        $(function(){
            $("#form").Validform({
                tiptype:function(data){

                },
                ajaxPost:true,
                postonce:true,
                callback:function(data){
                    if(data == ''){
                        layer.msg('操作失败');
                    }else{
                        layer.msg('操作成功');
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


    </script>
@endsection