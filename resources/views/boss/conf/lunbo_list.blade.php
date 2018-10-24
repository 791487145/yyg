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
                                <td>所属应用:
                                    <span class="select-box" style="width: 69%">
                                        <select class="select" size="1" name="location" >
                                            <option value="99" @if($tmp['location'] == 99) selected @endif >全部</option>
                                            <option value="0" @if($tmp['location'] == 0) selected @endif>微商城</option>
                                            <option value="1" @if($tmp['location'] == 1) selected @endif>app导游端</option>
                                        </select>
                                    </span>
                                </td>
                                <td>轮播图名称:<input type="text" class="input-text radius" style="width:69%" name="name" value="{{$tmp['name']}}"></td>
                                <td>所属分馆:
                                    <span class="select-box" style="width: 69%">
                                        <select class="select" size="1" name="pavilion_id" >
                                            <option value="" >请选择</option>
                                            @foreach($confPavilions as $confPavilion)
                                                @if($confPavilion->id == $tmp['pavilion_id'])
                                                <option value="{{$confPavilion->id}}" selected >{{$confPavilion->name}}</option>
                                                @else
                                                    <option value="{{$confPavilion->id}}"  >{{$confPavilion->name}}</option>
                                                @endif
                                            @endforeach
                                            <option value="9999" @if($tmp['pavilion_id'] == 9999) selected @endif >地方馆</option>
                                        </select>
                                    </span>
                                </td>
                                <td><input type="submit" class="btn btn-default radius" value="搜索">
                                <td><a href="javascript:" onclick="a('添加轮播图','/conf/confLunBo/add')"><i class="Hui-iconfont">&#xe600;</i>添加轮播图</a> </td>
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
                    <td>起始时间</td>
                    <td>所属应用</td>
                    <td>所属分管</td>
                    <td>操作</td>
                </tr>
                @foreach($confBanners as $confBanner)
                <tr class="text-c" id="tr_{{$confBanner->id}}">
                    <td style="width:20%">
                            <div style="width:15% ;height: 10%;"  class="f-l photos">
                                <img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$confBanner['cover']}}"  class="radius" width="50px" height="40px">
                            </div>
                            <div>
                                {{$confBanner->name}}
                            </div>
                    </td>
                    <td>{{$confBanner->display_order}}</td>
                    <td>{{$confBanner->url_content}}</td>
                    <td>{{$confBanner->start_time}}到{{$confBanner->end_time}}</td>
                    <td>{{$confBanner->location}}</td>
                    <td>
                        @if($confBanner->pavilion_id == 9999)
                            地方馆
                        @else
                            {{$confBanner->pavilion_name}}
                        @endif
                    </td>
                    <td>
                        <a title="编辑" href="javascript:;" onclick="a( '编辑轮播图', '/conf/confLunBo/update/{{$confBanner->id}}')" class="ml-5" style="text-decoration:none">
                               <i class="Hui-iconfont">&#xe6df;</i>
                        </a>
                        <a title="删除" href="javascript:;" onclick="confirm_action('确定要删除该轮播图吗？删除后将直接从轮播图专区下线','/conf/confLunBo/del/{{$confBanner->id}}')" class="ml-5" style="text-decoration:none">
                            <i class="Hui-iconfont">&#xe6e2;</i>
                        </a>
                    </td>

                </tr>
                @endforeach
            </table>
            <?php echo $confBanners->appends(['name' => $tmp['name'],'pavilion_id'=>$tmp['pavilion_id']])->render();     ?>
        </div>
    </div>
@endsection

@section("javascript")
    <script src="{{asset('lib/layer/3.0.3/layer.js')}}"></script>
    <script>
        layer.photos({
            photos: '.photos'
            ,anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
        });

        function a(title,url){
            layer_show(title,url);
        }

        function confirm_action(message,url){
            layer.confirm(message,function(index){
                $.get(url,function (data) {
                    layer.msg('已删除!',{icon:1,time:1000});
                    window.location.reload();
                })
            });
        }
    </script>
@endsection