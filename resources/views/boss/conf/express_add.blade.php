@extends('layout_pop')
<style>


</style>
@section("content")
    <form action="/expresses" id="form" method="post" >
    <div>
        <table class="table table-border table-bordered table-hover">
            <tr>
                <td colspan="2">
                    名称：<input id="txt" type="text" datatype="*" class="input-text radius" style="width:40%" name="name">

                </td>
            </tr>
            <tr>
                <td colspan="2">
                    电话：<input type="text" datatype="n" class="input-text radius" style="width:30%" name="tel">
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    排序：<input type="text" datatype="n" class="input-text radius" style="width:30%" name="order_sort">
                </td>
            </tr>
            <tr>
                <td>
                   <input type="submit" class="input-text radius" style="width:30%" name="display_order" value="确定">
                </td>
                <td>
                    <input type="button" class="input-text radius" style="width:30%" name="display_order" value="取消">
                </td>
            </tr>
        </table>
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
                tiptype:function(){},
                ajaxPost:true,
                postonce:true,
                callback:function(data){
                    if(data.yet == 'yes'){
                        layer.msg('已添加!',{icon:1,time:1000});
                        parent.location.replace(parent.location.href);
                    }else{
                        layer.msg('信息不完整!',{icon:1,time:1000});
                    }
                }
            });
        })


    </script>
@endsection