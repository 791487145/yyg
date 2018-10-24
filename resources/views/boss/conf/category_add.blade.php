@extends('layout_pop')
<style>


</style>
@section("content")
    <form action="/conf/confCategory" id="form" method="post" >
    <div>
        <table class="table table-border table-bordered table-hover">
            <tr>
                <td colspan="2">
                    商品品类：<input id="txt" type="text" class="input-text radius" style="width:40%" name="name">
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
                    <input onclick="b()" type="button" class="input-text radius" style="width:30%" name="display_order" value="取消">
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
        $("#txt").keyup(function(){
            if($(this).val().length > 4){
                $(this).val( $(this).val().substring(0,4) );
            }
        });

        $(function(){
            $("#form").Validform({
                tiptype:2,
                ajaxPost:true,
                postonce:true,
                callback:function(data){
                    layer.msg('已添加!',{icon:1,time:1000});
                    parent.location.replace(parent.location.href);
                }
            });
        })


    </script>
@endsection