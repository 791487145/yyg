@extends('layout_pop')
<style>
.text-c{
    margin-top: 5%;
}

</style>
@section("content")
    <form action="/cuscomer/guider/send/<?php echo $id?>" id="form" method="post" >
    <div class="text-c">
        <div>请选择驳回原因：</div>
        <label><input name="content" type="radio" value="导游信息与导游证件信息不一致" /> 导游信息与导游证件信息不一致</label><br/>
        <label><input name="content" type="radio" value="导游证件拍摄不清晰" />导游证件拍摄不清晰 </label><br/>
        <label><input name="content" type="radio" value="导游证件拍摄不清晰" />导游证件拍摄不清晰 </label><br/>
        <div class="abd" style="margin-top: 5%">
            <input type="submit"  class="input-text radius" value="发送短信" style="width:25%" >
            <input type="button" class="input-text radius" value="取消" onclick="b()" style="width:25%" >
        </div>
    </div>
    </form>
@endsection
@section("javascript")
    <script>


        function b(){
            layer_close();
        }

        $(function(){
            $("#form").Validform({
                tiptype:function(msg,o,cssctl){
                    msg:'提示信息'
                },
                ajaxPost:true,
                postonce:true,
                callback:function(data){
                    debugger
                    if(data.status==200){
                        setTimeout(function(){
                            $.Hidemsg();
                        },100);
                        layer.msg('提交成功');
                    }
                    setTimeout(function(){
                        parent.location.replace(parent.location.href);
                    },1000)

                }
            });
        })


    </script>
@endsection