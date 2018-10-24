@extends('layout')
    <style>
        #all{
            margin-left: 1%;
        }
    </style>
@section("content")
    <div id="all">
        <div class="panel panel-default">
            <div class="panel-header"><a href="javascript:;" onclick="a('添加快递公司','/expresses')"><i class="Hui-iconfont">&#xe600;</i>添加快递公司</a></div>
            <div>
                <table class="table table-border table-bordered table-hover">
                    <tr class="text-c">
                        <td>编号</td>
                        <td>名称</td>
                        <td>电话</td>
                        <td>排序</td>
                        <td>操作</td>
                    </tr>
                    @foreach($datas as $data)
                        <tr class="text-c" id="tr_{{$data->id}}">
                            <td>{{$data->id}}</td>
                            <td>{{$data->name}}</td>
                            <td>{{$data->tel}}</td>
                            <td>{{$data->order_sort}}</td>
                            <td>
                                <a title="编辑" href="javascript:;" onclick="a( '编辑快递', '/expresses/{{$data->id}}')" class="ml-5" style="text-decoration:none">
                                    <i class="Hui-iconfont">&#xe6df;</i>
                                </a>
                                <a title="删除" href="javascript:;" onclick="confirm_action('确定需要删除该快递吗？','/express/{{$data->id}}')" class="ml-5" style="text-decoration:none">
                                    <i class="Hui-iconfont">&#xe6e2;</i>
                                </a>
                            </td>

                        </tr>
                    @endforeach
                </table>
                <?php echo $datas->render();     ?>
            </div>
        </div>
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