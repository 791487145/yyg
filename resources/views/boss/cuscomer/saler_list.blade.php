@extends('layout')
    <style>
        #all{
            margin-left: 1%;
        }
    </style>
@section("content")
    <div id="all">
        <div>
                    <form>

                <table class="table table-border table-bordered table-hover">
                    <tr>
                        <td><a href="javascript:" onclick="a('添加销售员','/cuscomer/saler')"><i class="Hui-iconfont">&#xe600;</i>添加销售员</a> </td>
                        <td class="text-r">
                            <input type="text" class="btn btn-default radius" placeholder="输入关键字" name="name" value="{{$tmp['name'] }}" >
                            <button type="submit" class="btn btn-success" id="" name=""><i class="Hui-iconfont">&#xe665;</i> 搜索</button>
                        </td>
                    </tr>
                </table>
            </form>
        </div>
        <div>
            <table class="table table-border table-bordered table-hover">
                <tr class="text-c">
                    <td>序号</td>
                    <td>销售人员</td>
                    <td>推荐旅行社数</td>
                    <td>推荐导游数量</td>
                    <td>登陆账号</td>
                    <td>注册时间</td>
                    <td>操作</td>
                </tr>
                @foreach($Users as $User)
                <tr class="text-c" id="tr_{{$User->id}}">
                    <td>{{$User->id}}</td>
                    <td>{{$User->name}}</td>
                    <td>{{$User->ta_num}}</td>
                    <td>{{$User->guide_num}}</td>
                    <td>{{$User->email}}</td>
                    <td>{{$User->created_at}}</td>
                    <td>
                        <a title="编辑" href="javascript:;" onclick="a( '编辑', '/cuscomer/saler/{{$User->id}}')" class="ml-5" style="text-decoration:none">
                               <i class="Hui-iconfont">&#xe6df;</i>
                        </a>
                        <a title="查看" href="javascript:;" onclick="a( '查看', '/cuscomer/saler/guide/{{$User->id}}')" class="ml-5" style="text-decoration:none">
                            <i class="Hui-iconfont">&#xe709;</i>
                        </a>
                    </td>

                </tr>
                    @endforeach

            </table>
        </div>
        <span style="float: left;line-height: 70px;margin: 0 20px">共{{$Users->lastPage()}}页,{{$Users->total()}}条数据 ；每页显示{{$Users->perPage()}}条数据</span>
        <?php echo $Users->appends(['name'=>$tmp['name']])->render();     ?>
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