@extends('layout_pop')
<style>


</style>
@section("content")
    <form action="/conf/confKeyword" id="form" method="post" >
    <div>
        <table class="table table-border table-bordered table-hover">
            <tr>
                <td colspan="2">
                    热搜词：<input id="txt" type="text" class="input-text radius" style="width:40%" name="name"><span class="c-red">不超过六个字</span>
                    <script>
                        $("#txt").keyup(function(){
                            if($(this).val().length > 6){
                                $(this).val( $("#txt").val().substring(0,6) );
                            }
                        });
                    </script>
                </td>

            </tr>
            <tr>
                <td colspan="2">
                    链接：<input type="text" class="input-text radius" style="width:30%" name="url">
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    排序：<input type="text" class="input-text radius" style="width:30%" name="display_order">
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
                tiptype:2,
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