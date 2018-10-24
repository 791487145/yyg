@extends('layout_pop')
<style>
    #all{
        margin-left: 5%;
        margin-top: 5%;
    }
</style>
@section("content")
    <div id="all">
        <form id="form" action="/cuscomer/supplier" method="post">
        <table class="table table-border table-bordered table-hover">
            <tr>
                <td>供应商姓名<input type="text" class="input-text radius" style="width:69%" name="name"></td>
                <td>手机号码<input type="text" datatype="mobile" class="input-text radius" style="width:69%" name="mobile"></td>
            </tr>
            <tr>
                <td>身份证号<input type="text" class="input-text radius" style="width:69%" name="card_id"></td>
                <td>入住保证金<input type="text" datatype="n" class="input-text radius" style="width:69%" name="deposit"></td>
            </tr>
            <tr>
                <td><input type="button" onclick="b()" class="input-text radius" style="width:69%" value="取消"></td>
                <td><input type="submit" class="input-text radius" style="width:69%" value="确定" ></td>
            </tr>
        </table>
        </form>
    </div>

@endsection
@section("javascript")
    <script>
        $(function(){
            $("#form").Validform({
                tiptype:function(){},
                ajaxPost:true,
                postonce:true,
                datatype:{
                    "mobile":/^1[3|4|5|7|8][0-9]\d{8}$/
                },
                callback:function(data){
                    if(data.ret == '{{\App\Http\Controllers\Admin\BaseController::RETFAIL}}'){
                        layer.alert(data.msg, {icon:2,time:5000});
                    }else{
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