@extends('layout_pop')
<style>
    .all{
        margin-top: 10px;
    }

</style>
@section("content")
    <form action="/conf/confKeywords/{{$ConfHotWords->id}}" id="form" method="post" >
    <div class="all">
        <table class="table table-border table-bordered table-hover">
            <tr>
                <td colspan="2">
                    热搜词：<input id="txt" type="text" class="input-text radius" style="width:40%" name="name" value="{{$ConfHotWords->name}}"><span class="c-red mr-10">不能超过六个字</span>
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
                    链接：<input type="text" class="input-text radius" style="width:30%" name="url" value="{{$ConfHotWords->url}}">
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    排序：<input type="text" class="input-text radius" style="width:30%" name="display_order" value="{{$ConfHotWords->display_order}}">
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

        $(function(){
            $("#form").Validform({
                tiptype:2,
                ajaxPost:true,
                postonce:true,
                callback:function(data){
                    parent.location.replace(parent.location.href);
                }
            });
        })


    </script>
@endsection