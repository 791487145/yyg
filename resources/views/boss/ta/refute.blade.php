@extends('layout_pop')
<style>
    .btn-handle{text-align: center;margin-top: 20px; }
    .btn-handle .btn{width: 140px;text-align: center;margin-right: 50px;}
    ul li{padding: 20px;}
</style>
@section("content")
    <div class="pd-20">
        <form action="{{url('/ta/checkPass')}}" method="post" class="form form-horizontal" id="gift-add">
            <div class="row">
                <input type="hidden" name="id" value="{{$id}}">
                <input type="hidden" name="active" value="0">
                <p>请选择驳回原因</p>
                <ul>
                    <li><input type="radio" name="refute_des" value="0">身份证信息与身份证照信息不一致</li>
                    <li><input type="radio" name="refute_des" value="1">身份证照拍摄不清晰</li>
                    <li><input type="radio" name="refute_des" value="2">上传的是无效证照信息</li>
                </ul>
            </div>


            <div class="row btn-handle">
                <button type="submit" class="btn btn-success radius" ><i class="icon-ok"></i>发送短信</button>
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
                    if(data.ret == 'yes') {
                        layer.alert('操作成功',{icon:1,time:1000});
                        parent.location.href = '/ta/tamanages';
                    }else{
                        layer.alert('操作失败', {icon:2,time:5000});
                    }
                }
            });
        });

        function layer_close(){
            layer_close();
        }
    </script>
@endsection