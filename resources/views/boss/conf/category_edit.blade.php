@extends('layout_pop')
<style>
    .all{
        margin-top: 10px;
    }

</style>
@section("content")
    <form action="/conf/confCategorys/{{$ConfCategorys->id}}" id="form" method="post" >
    <div class="all">
        <table class="table table-border table-bordered table-hover">
            <tr>
                <td colspan="2">
                    商品品类：<input id="txt" type="text" class="input-text radius" style="width:40%" name="name" value="{{$ConfCategorys->name}}">
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    排序：<input type="text" class="input-text radius" style="width:30%" name="display_order" value="{{$ConfCategorys->display_order}}">
                </td>
            </tr>
            <tr>
                <td style="text-align: center;">
                   <input type="submit" class="btn btn-success radius" style="width:30%" name="display_order" value="确定">
                </td>
                <td style="text-align: center;">
                    <input type="button" onclick="b()" class="btn btn-default radius" style="width:30%" name="display_order" value="取消">
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
                    layer.msg('已修改!',{icon:1,time:1000});
                    parent.location.replace(parent.location.href);
                }
            });
        })


    </script>
@endsection