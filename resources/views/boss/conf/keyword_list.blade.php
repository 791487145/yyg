@extends('layout')
    <style>
        #all{
            margin-left: 1%;
        }
    </style>
@section("content")
    <div id="all">
        <div>
            {{--<form  method="post" action="/conf/confKeywords" >--}}
            <form>

                <table class="table table-border table-bordered table-hover">
                    <tr>
                        <td><a href="javascript:" onclick="a('添加热搜词','/conf/confKeyword')"><i class="Hui-iconfont">&#xe600;</i>新建热搜词</a> </td>
                        <td class="text-c">
                            <input type="text" class="btn btn-default radius" placeholder="输入热搜词" name="name" value="{{$tmp['name'] }}" >
                            <button type="submit" class="btn btn-success" id="" name=""><i class="Hui-iconfont">&#xe665;</i> 搜索</button>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
        <div>
            <table class="table table-border table-bordered table-hover">
                <tr class="text-c" id="first">
                    <td>热搜词</td>
                    <td>链接</td>
                    <td>排序</td>
                    <td>操作</td>
                </tr>
                @foreach($ConfHotWords as $ConfHotWord)
                <tr class="text-c" id="tr_{{$ConfHotWord->id}}">
                    <td style="width:20%">
                            {{$ConfHotWord->name}}
                    </td>
                    <td>{{$ConfHotWord->url}}</td>
                    <td>{{$ConfHotWord->display_order}}</td>
                    <td>
                        <a title="编辑" href="javascript:;" onclick="a( '编辑热搜词', '/conf/confKeywords/{{$ConfHotWord->id}}')" class="ml-5" style="text-decoration:none">
                               <i class="Hui-iconfont">&#xe6df;</i>
                        </a>
                        <a title="删除" href="javascript:;" onclick="confirm_action('删除？','/conf/confKeyword/{{$ConfHotWord->id}}')" class="ml-5" style="text-decoration:none">
                            <i class="Hui-iconfont">&#xe6e2;</i>
                        </a>
                    </td>

                </tr>
                    @endforeach

            </table>
        </div>
        {!! $ConfHotWords->appends(['name' => $tmp['name']])->render() !!}
    </div>
@endsection

@section("javascript")
    <script>
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