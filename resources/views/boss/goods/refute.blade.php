@extends('layout_pop')
<style>
    .btn-handle{text-align: center;margin-top: 20px; }
    .btn-handle .btn{width: 140px;text-align: center;margin-right: 50px;}
    ul li{padding: 20px;}
</style>
@section("content")
    <div class="pd-20">
        <form action="{{url('goods/action/refute',$id)}}" method="post" class="form form-horizontal" id="gift-add">
            <div class="row">
                <p>请选择驳回原因</p>
                <span><textarea style="width: 650px;height: 160px" name="refute_reason"></textarea></span>
            </div>

            <div class="row btn-handle">
                <button type="submit" class="btn btn-success radius" ><i class="icon-ok"></i>确定</button>
                <button type="button" class="btn btn-danger radius" onclick="layer_close();">取消</button>
            </div>
        </form>
    </div>
    <script>


    </script>
@endsection

@section("javascript")
    <script>
        $(function(){
            $("#gift-add").Validform({
                tiptype:function(){},
                ajaxPost:true,
                postonce:true,
                callback:function(data){
                    if(data.msg) {
                        layer.alert(data.msg,{icon:1,time:1000});
                        parent.location.href = '/goods/check/0';//(parent.location.href);
                    } else if(data.ret) {
                        layer.msg(data.ret,{icon:5,time:1000});
                    }else {
                        layer.alert('操作失败', {icon:2,time:5000});
                    }
                }
            });
        });
    </script>
@endsection