@extends('layout')
    <style>
        #all{
            margin-left: 1%;
        }
    </style>
@section("content")
    <div id="all">
        <div>
            <table class="table table-border table-bordered table-hover">
                <tr>
                    <td><span>图片自动回复</span></td>
                </tr>
            </table>
        </div>
        <div>
            <form>
                <table class="table table-border table-bordered table-hover">
                    <tr>
                        <td><a href="javascript:" onclick="a('添加关键字','/wx/reply/add')"><i class="Hui-iconfont">&#xe600;</i>新建自动回复</a> </td>
                        <td class="text-c">
                            <input type="text" class="btn btn-default radius" placeholder="输入关键字" name="key_word" value="{{$tmp['keyword']}}" >
                            <button type="submit" class="btn btn-success" id="" name=""><i class="Hui-iconfont">&#xe665;</i> 搜索</button>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
        <div>
            <table class="table table-border table-bordered table-hover">
                <tr class="text-c">
                    <td>ID</td>
                    <td>关键字</td>
                    <td>自动回复内容</td>
                    <td>状态</td>
                    <td>添加时间</td>
                    <td>备注</td>
                    <td>操作</td>
                </tr>
                @foreach($WxReplys as $WxReply)
                <tr class="text-c" id="tr_{{$WxReply->id}}">
                    <td>{{$WxReply->id}}</td>
                    <td style="width:20%">
                            {{$WxReply->key_word}}
                    </td>
                    <td class="photos"><img src="{{$WxReply->img}}"  class="radius" width="50px" height="40px"></td>
                    <td>{{$WxReply->state}}</td>
                    <td>{{$WxReply->created_at}}</td>
                    <td>{{$WxReply->remark}}</td>
                    <td>
                        <a title="修改" href="javascript:;" onclick="a('修改','/wx/reply/{{$WxReply->id}}/update')" class="ml-5" style="text-decoration:none">
                            <i class="Hui-iconfont">&#xe6df;</i>
                        </a>
                        <a title="删除" href="javascript:;" onclick="confirm_action('删除？','/wx/reply/{{$WxReply->id}}/del')" class="ml-5" style="text-decoration:none">
                            <i class="Hui-iconfont">&#xe6e2;</i>
                        </a>
                    </td>
                </tr>
                    @endforeach
            </table>
        </div>
    </div>
    <span style="float: left;line-height: 70px;margin: 0 20px">共{{$WxReplys->lastPage()}}页,{{$WxReplys->total()}}条数据 ；每页显示{{$WxReplys->perPage()}}条数据</span>
    <?php echo $WxReplys->appends(['name'=>$tmp['keyword']])->render();     ?>
@endsection
@section("javascript")
    <script src="{{asset('lib/layer/3.0.3/layer.js')}}"></script>
    <script>
        layer.photos({
            photos: '.photos'
            ,anim: 5 
        });
        function a(title,url){
            layer_show(title,url);
        }
        function confirm_action(message,url){
            layer.confirm(message,function(index){
                $.get(url,function (data) {
                    $("#tr_"+data).remove();
                    layer.msg('已删除!',{icon:1,time:1000});
                })
            });
        }
    </script>
@endsection