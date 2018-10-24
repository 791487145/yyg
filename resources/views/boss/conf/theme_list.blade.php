@extends('layout')
    <style>
        #all{
            margin-left: 1%;
            margin-top: 10px;
        }
    </style>
@section("content")
    <div id="all">
        <div>
            <form>
                <table class="table table-border table-bordered table-hover">
                    <tr>
                        <td>所属应用:
                            <span class="select-box" style="width: 69%">
                                <select class="select" size="1" name="location" >
                                    <option value="99" @if($tmp['location'] == 99) selected @endif >全部</option>
                                    <option value="0" @if($tmp['location'] == 0) selected @endif>微商城</option>
                                    <option value="1" @if($tmp['location'] == 1) selected @endif>app导游端</option>
                                </select>
                            </span>
                        </td>
                        <td>专题名称:<input type="text" class="input-text radius" style="width:69%" name="name" value="{{$tmp['name']}}"></td>
                        <td>所属分馆:
                            <span class="select-box" style="width: 69%">
                                <select class="select" size="1" name="pavilion_id" >
                                    <option value="" >请选择</option>
                                    @foreach($ConfPavilions as $ConfPavilion)
                                            @if($tmp['pavilion_id'] == $ConfPavilion->id)
                                                <option selected value="{{$ConfPavilion->id}}" >{{$ConfPavilion->name}}</option>
                                             @endif
                                        <option value="{{$ConfPavilion->id}}" >{{$ConfPavilion->name}}</option>
                                    @endforeach
                                </select>
                            </span>
                        </td>
                        <td><input type="submit" class="btn btn-default radius" value="搜索">
                        <td><a href="javascript:" onclick="a('添加专题','/conf/confTheme')"><i class="Hui-iconfont">&#xe600;</i>添加专题 </a></td>
                    </tr>
                </table>
            </form>
        </div>
        <div>
            <table class="table table-border table-bordered table-hover">
                <tr class="text-c">
                    <td>封面图</td>
                    <td>排序</td>
                    <td>URL</td>
                    <td>描述</td>
                    <td>所属应用</td>
                    <td>所属分馆</td>
                    <td>操作</td>
                </tr>
                @foreach($ConfsThemes as $ConfsTheme)
                <tr class="text-c" id="tr_{{$ConfsTheme->id}}">
                    <td style="width:20%">
                            <div style="width:15% ;height: 10%;"  class="f-l">
                                <img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$ConfsTheme->cover}}"  class="radius" width="50px" height="40px">
                            </div>
                            <div>
                                {{$ConfsTheme->name}}
                            </div>
                    </td>
                    <td>{{$ConfsTheme->display_order}}</td>
                    <td>{{$ConfsTheme->url}}</td>
                    <td>{{$ConfsTheme->content}}</td>
                    <td>{{$ConfsTheme->location}}</td>
                    <td>{{$ConfsTheme->pavilion_name}}</td>
                    <td>
                        <a title="编辑" href="javascript:;" onclick="a( '编辑专题', '/conf/confThemes/{{$ConfsTheme->id}}')" class="ml-5" style="text-decoration:none">
                               <i class="Hui-iconfont">&#xe6df;</i>
                        </a>
                        <a title="删除" href="javascript:;" onclick="confirm_action('确认删除{{$ConfsTheme->name}}？','/conf/confTheme/{{$ConfsTheme->id}}')" class="ml-5" style="text-decoration:none">
                            <i class="Hui-iconfont">&#xe6e2;</i>
                        </a>
                    </td>

                </tr>
                @endforeach
            </table>

        </div>
        <?php echo $ConfsThemes->appends(['name'=>$tmp['name'],'pavilion_id'=>$tmp['pavilion_id']])->render();     ?>
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