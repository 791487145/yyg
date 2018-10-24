@extends('layout')
    <style>
        #all{
            margin-left: 1%;
        }
    </style>
@section("content")
    <div id="all">
        <div class="panel panel-default">
            <div class="panel-header"><a href="javascript:;" onclick="a('添加地方馆','/conf/confShop/adding')"><i class="Hui-iconfont">&#xe600;</i>添加地方馆</a></div>
            <div>
                <table class="table table-border table-bordered table-hover">
                    <tr class="text-c">
                        <td>封面图</td>
                        <td>新封面图</td>
                        <td>背景图</td>
                        <td>排序</td>
                        <td>商品数</td>
                        <td>营业额</td>
                        <td>建馆时间</td>
                        <td>辐射地区</td>
                        <td>操作</td>
                    </tr>
                    @foreach($ConfPavilions as $ConfPavilion)
                        <tr class="text-c" id="tr_{{$ConfPavilion->id}}">
                            <td style="width:20%">
                                <div style="width:15% ;height: 10%;"  class="f-l photos">
                                    <img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$ConfPavilion['cover']}}"  class="radius" width="50px" height="40px">
                                </div>
                                <div>
                                    {{$ConfPavilion->name}}
                                </div>
                            </td>
                            <td>
                                <div class="f-l photos">
                                    <img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$ConfPavilion['new_cover']}}"  class="radius" width="50px" height="40px">
                                </div>
                            </td>
                            <td style="width:20%" class="photos">
                                <img src="{{env('IMAGE_DISPLAY_DOMAIN')}}{{$ConfPavilion['background']}}"  class="radius" width='50px' height='40px'>
                            </td>
                            <td>{{$ConfPavilion->display_order}}</td>
                            <td>{{$ConfPavilion->goods_num}}</td>
                            <td>￥{{$ConfPavilion->amount}}</td>
                            <td>{{$ConfPavilion->created_at}}</td>
                            <td>{{$ConfPavilion->city_names}}</td>
                            <td>
                                <a title="编辑" href="javascript:;" onclick="a( '编辑地方馆', '/conf/confShop/update/{{$ConfPavilion->id}}')" class="ml-5" style="text-decoration:none">
                                    <i class="Hui-iconfont">&#xe6df;</i>
                                </a>
                                {{--<a title="删除" href="javascript:;" onclick="confirm_action('确定需要删除该地方馆吗？删除后，该馆的商品将会全部下架' +
                                        '请谨慎操作~','/conf/confShop/del/del/{{$ConfPavilion->id}}')" class="ml-5" style="text-decoration:none">
                                    <i class="Hui-iconfont">&#xe6e2;</i>
                                </a>--}}
                            </td>

                        </tr>
                    @endforeach
                </table>
                <?php echo $ConfPavilions->render();     ?>
            </div>
        </div>
    </div>
@endsection

@section("javascript")
    <script src="{{asset('lib/layer/3.0.3/layer.js')}}"></script>
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

        layer.photos({
            photos: '.photos'
            ,anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
        });

    </script>
@endsection