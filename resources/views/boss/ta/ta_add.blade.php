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
</style>
@section("content")
    <div id="all">
        <form id="form" action="/ta/add/" method="post">
        <table class="table table-border table-bordered table-hover">
            <tr>
                <td ><span class="input-padding">旅行社名称：</span><input type="text"  datatype="*" class="input-css" name="ta_name"></td>
            </tr>
            <tr>
                <td ><span class="input-padding">负责人真实姓名：</span><input type="text"  datatype="*" class="input-css" name="opt_name"></td>
            </tr>
            <tr>
                <td><span class="red-font">提现账户必须与该负责人真实姓名一致</span></td>
            </tr>
            <tr>
                <td ><span class="input-padding">手机号码：</span><input type="text"  datatype="mobile" class="input-css" name="mobile" errormsg="手机号格式不对">
                </td>
            </tr>
            <tr>
                <td ><span class="input-padding">身份证号码：</span><input type="text" class="input-css"  value="" name="opt_id_card" datatype="*" /></td>
            </tr>

            <tr>
                <td style="text-align: center"><input style="margin-right: 20px;" type="button" onclick="b()" class="btn btn-danger" value="取消"><input type="submit" class="btn btn-success" value="确定" ></td>
            </tr>
        </table>
        </form>
    </div>

@endsection
@section("javascript")
    <script>
        $(function(){
            $("#form").Validform({
                tiptype:function(data,ret){
                   if(data != '通过信息验证！'){
                       layer.msg(data);
                   }
                },
                ajaxPost:true,
                postonce:true,
                datatype:{
                    "mobile":/^1[3|4|5|7|8][0-9]\d{8}$/
                },
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

    </script>
@endsection