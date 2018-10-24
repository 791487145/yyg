@extends('layout_pop')
@section("content")
    <form action="/wx/text/{{$WxReply->id}}/update" id="form" method="post">
    <div style="padding: 20px 20px ">
        <table class="table table-border table-bordered table-hover">
            <tr>
                <td colspan="2">
                    关键字：<input id="txt" value="{{$WxReply->key_word}}" type="text" datatype="*" class="input-text radius" style="width:40%" name="keyword">
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    回复内容：
                    <textarea datatype="*" class="textarea radius" name="response">{{$WxReply->response}}</textarea>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="select-box">
                        <select class="select" size="1" name="state">
                            <option value="0" @if($WxReply->state == 0) selected @endif>禁用</option>
                            <option value="1" @if($WxReply->state == 1) selected @endif>正常</option>
                        </select>
                    </span>
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

        $(function(){
            $("#form").Validform({
                tiptype:2,
                ajaxPost:true,
                postonce:true,
                callback:function(data){
                    if(data.ret == 'no'){
                        layer.msg('关键字重复!',{icon:6,time:1000});
                    }else{
                        layer.msg('已添加!',{icon:1,time:1000});
                        parent.location.replace(parent.location.href);
                    }
                }
            });
        })


    </script>
@endsection